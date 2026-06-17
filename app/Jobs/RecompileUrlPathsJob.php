<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlRecompileItem;
use App\Models\UrlSettingHistory;
use App\Services\UrlRoutingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class RecompileUrlPathsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 minutes

    protected string $newArticlePattern;

    protected string $newCategoryPattern;

    protected int $historyId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $newArticlePattern, string $newCategoryPattern, int $historyId)
    {
        $this->newArticlePattern = $newArticlePattern;
        $this->newCategoryPattern = $newCategoryPattern;
        $this->historyId = $historyId;
    }

    /**
     * Execute the job.
     */
    public function handle(UrlRoutingService $routingService): void
    {
        $history = UrlSettingHistory::find($this->historyId);
        if (! $history) {
            return;
        }

        // Set status lock
        $history->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        // 1. Database Backup
        $backupFile = null;
        $backupSuccess = $this->runBackup($backupFile);
        if (! $backupSuccess) {
            $errorMsg = 'Database backup failed before recompile. Recompilation halted.';
            $history->update([
                'status' => 'failed',
                'error_message' => $errorMsg,
                'finished_at' => now(),
            ]);

            return;
        }

        try {
            // Compute total items
            $totalCategories = Category::count();
            $totalArticles = Article::count();
            $totalItems = $totalCategories + $totalArticles;
            $history->update(['total_items' => $totalItems]);

            $processed = 0;
            $updated = 0;
            $failed = 0;

            // 2. Recompile Categories
            $categories = Category::all();
            foreach ($categories as $category) {
                DB::beginTransaction();
                try {
                    $oldPath = $category->url_path;
                    $newPath = $routingService->compileCategoryPath($category, $this->newCategoryPattern);

                    // Create log item
                    $recompileItem = UrlRecompileItem::create([
                        'history_id' => $history->id,
                        'target_type' => 'category',
                        'target_id' => $category->id,
                        'old_path' => $oldPath,
                        'new_path' => $newPath,
                        'status' => 'pending',
                    ]);

                    // Update category quietly
                    $category->url_path = $newPath;
                    $category->saveQuietly();

                    // Register redirect if old path exists
                    if (! empty($oldPath) && $oldPath !== $newPath) {
                        $routingService->registerRedirect($oldPath, $newPath, 'category', $category->id);
                        $history->increment('redirect_count');
                    }

                    $recompileItem->update(['status' => 'updated']);
                    DB::commit();

                    $processed++;
                    $updated++;
                    $history->update([
                        'processed_items' => $processed,
                        'updated_items' => $updated,
                    ]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $failed++;
                    $history->update(['failed_items' => $failed]);

                    if (isset($recompileItem)) {
                        $recompileItem->update([
                            'status' => 'failed',
                            'error_message' => $e->getMessage(),
                        ]);
                    }
                    throw $e; // Re-throw to trigger job failure
                }
            }

            // 3. Recompile Articles (processed in chunks of 100)
            Article::with('category')->chunk(100, function ($articles) use ($routingService, $history, &$processed, &$updated, &$failed) {
                foreach ($articles as $article) {
                    DB::beginTransaction();
                    try {
                        $oldPath = $article->url_path;
                        $newPath = $routingService->compileArticlePath($article, $this->newArticlePattern);

                        // Create log item
                        $recompileItem = UrlRecompileItem::create([
                            'history_id' => $history->id,
                            'target_type' => 'article',
                            'target_id' => $article->id,
                            'old_path' => $oldPath,
                            'new_path' => $newPath,
                            'status' => 'pending',
                        ]);

                        // Update article quietly
                        $article->url_path = $newPath;
                        $article->saveQuietly();

                        // Register redirect if old path exists
                        if (! empty($oldPath) && $oldPath !== $newPath) {
                            $routingService->registerRedirect($oldPath, $newPath, 'article', $article->id);
                            $history->increment('redirect_count');
                        }

                        $recompileItem->update(['status' => 'updated']);
                        DB::commit();

                        $processed++;
                        $updated++;
                        $history->update([
                            'processed_items' => $processed,
                            'updated_items' => $updated,
                        ]);
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        $failed++;
                        $history->update(['failed_items' => $failed]);

                        if (isset($recompileItem)) {
                            $recompileItem->update([
                                'status' => 'failed',
                                'error_message' => $e->getMessage(),
                            ]);
                        }
                        throw $e; // Re-throw to trigger job failure and stop subsequent chunks
                    }
                }
            });

            // 4. Save configuration settings after full success
            Setting::set('url_pattern_article', $this->newArticlePattern);
            Setting::set('url_pattern_category', $this->newCategoryPattern);

            $history->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);

            // Flush caches
            try {
                Artisan::call('optimize:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
            } catch (\Throwable $cacheEx) {
                Log::warning('Failed to clear cache after URL recompilation: '.$cacheEx->getMessage());
            }

        } catch (\Throwable $e) {
            Log::error('URL Recompilation Job failed: '.$e->getMessage());

            $history->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
            ]);

            // Flush caches even on failure to ensure consistent state
            try {
                Artisan::call('optimize:clear');
            } catch (\Throwable $ex) {
            }
        }
    }

    /**
     * Run database backup before compilation.
     */
    protected function runBackup(?string &$backupFile): bool
    {
        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $backupFile = $backupDir."/dakhoacantho_web_before_url_recompile_{$timestamp}.sql";

        $mysqlDumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (! File::exists($mysqlDumpPath)) {
            $mysqlDumpPath = 'mysqldump';
        }

        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'dakhoacantho_web');
        $dbUser = config('database.connections.mysql.username', 'root');
        $dbPass = config('database.connections.mysql.password', '');

        if ($dbPass === '') {
            $passwordOption = '';
        } else {
            $passwordOption = '--password='.escapeshellarg($dbPass);
        }

        $command = sprintf(
            '"%s" -h %s -P %s -u %s %s %s > "%s"',
            $mysqlDumpPath,
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            $passwordOption,
            escapeshellarg($dbName),
            $backupFile
        );

        exec($command, $output, $resultCode);

        return $resultCode === 0;
    }
}
