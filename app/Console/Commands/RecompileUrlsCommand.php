<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlRecompileItem;
use App\Models\UrlSettingHistory;
use App\Services\UrlRoutingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecompileUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'urls:recompile {--initial : Set default patterns and run initial compile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompile url_path values for all Categories and Articles';

    /**
     * Execute the console command.
     */
    public function handle(UrlRoutingService $routingService): int
    {
        $this->info('Bắt đầu biên dịch đường dẫn URL...');

        $articlePattern = Setting::get('url_pattern_article');
        $categoryPattern = Setting::get('url_pattern_category');

        if ($this->option('initial')) {
            $this->info('Đang thiết lập cấu hình mặc định (article: {slug}, category: category/{categories})...');
            $articlePattern = '{slug}';
            $categoryPattern = 'category/{categories}';
            Setting::set('url_pattern_article', $articlePattern);
            Setting::set('url_pattern_category', $categoryPattern);
        }

        if (empty($articlePattern) || empty($categoryPattern)) {
            $this->error('Lỗi: Chưa cấu hình URL patterns. Vui lòng chạy với tùy chọn --initial hoặc cấu hình trong Admin panel.');

            return 1;
        }

        // Check conflicts first
        $conflictResult = $routingService->checkConflicts($articlePattern, $categoryPattern);
        if ($conflictResult['conflict_count'] > 0) {
            $this->error("Tìm thấy {$conflictResult['conflict_count']} xung đột đường dẫn. Không thể tiếp tục:");
            foreach ($conflictResult['conflicts'] as $conflict) {
                $this->line(' - '.$conflict['message']);
            }

            return 1;
        }

        // Create setting history run
        $history = UrlSettingHistory::create([
            'old_article_pattern' => Setting::get('url_pattern_article'),
            'new_article_pattern' => $articlePattern,
            'old_category_pattern' => Setting::get('url_pattern_category'),
            'new_category_pattern' => $categoryPattern,
            'status' => 'processing',
            'started_at' => now(),
            'created_by' => null, // Run from CLI
        ]);

        $totalCategories = Category::count();
        $totalArticles = Article::count();
        $totalItems = $totalCategories + $totalArticles;
        $history->update(['total_items' => $totalItems]);

        $this->output->progressStart($totalItems);
        $processed = 0;
        $updated = 0;
        $failed = 0;

        // Categories
        $categories = Category::all();
        foreach ($categories as $category) {
            DB::beginTransaction();
            try {
                $oldPath = $category->url_path;
                $newPath = $routingService->compileCategoryPath($category, $categoryPattern);

                $recompileItem = UrlRecompileItem::create([
                    'history_id' => $history->id,
                    'target_type' => 'category',
                    'target_id' => $category->id,
                    'old_path' => $oldPath,
                    'new_path' => $newPath,
                    'status' => 'pending',
                ]);

                $category->url_path = $newPath;
                $category->saveQuietly();

                if (! empty($oldPath) && $oldPath !== $newPath) {
                    $routingService->registerRedirect($oldPath, $newPath, 'category', $category->id);
                    $history->increment('redirect_count');
                }

                $recompileItem->update(['status' => 'updated']);
                DB::commit();
                $processed++;
                $updated++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $failed++;
                if (isset($recompileItem)) {
                    $recompileItem->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                }
                $history->update([
                    'status' => 'failed',
                    'error_message' => 'Lỗi tại danh mục ID '.$category->id.': '.$e->getMessage(),
                    'finished_at' => now(),
                    'processed_items' => $processed,
                    'updated_items' => $updated,
                    'failed_items' => $failed,
                ]);
                $this->error("\nBiên dịch thất bại: ".$e->getMessage());

                return 1;
            }
            $this->output->progressAdvance();
        }

        // Articles
        Article::with('category')->chunk(100, function ($articles) use ($routingService, $history, $articlePattern, &$processed, &$updated, &$failed) {
            foreach ($articles as $article) {
                DB::beginTransaction();
                try {
                    $oldPath = $article->url_path;
                    $newPath = $routingService->compileArticlePath($article, $articlePattern);

                    $recompileItem = UrlRecompileItem::create([
                        'history_id' => $history->id,
                        'target_type' => 'article',
                        'target_id' => $article->id,
                        'old_path' => $oldPath,
                        'new_path' => $newPath,
                        'status' => 'pending',
                    ]);

                    $article->url_path = $newPath;
                    $article->saveQuietly();

                    if (! empty($oldPath) && $oldPath !== $newPath) {
                        $routingService->registerRedirect($oldPath, $newPath, 'article', $article->id);
                        $history->increment('redirect_count');
                    }

                    $recompileItem->update(['status' => 'updated']);
                    DB::commit();
                    $processed++;
                    $updated++;
                } catch (\Throwable $e) {
                    DB::rollBack();
                    $failed++;
                    if (isset($recompileItem)) {
                        $recompileItem->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
                    }
                    $history->update([
                        'status' => 'failed',
                        'error_message' => 'Lỗi tại bài viết ID '.$article->id.': '.$e->getMessage(),
                        'finished_at' => now(),
                        'processed_items' => $processed,
                        'updated_items' => $updated,
                        'failed_items' => $failed,
                    ]);
                    $this->error("\nBiên dịch thất bại: ".$e->getMessage());
                    throw $e;
                }
                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();

        $history->update([
            'status' => 'completed',
            'finished_at' => now(),
            'processed_items' => $processed,
            'updated_items' => $updated,
            'failed_items' => $failed,
        ]);

        $this->info("Hoàn tất biên dịch URL. Tổng số mục: {$processed}. Lỗi: {$failed}.");

        return 0;
    }
}
