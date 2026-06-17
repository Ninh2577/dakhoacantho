<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlRedirect;
use App\Models\UrlSettingHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RollbackUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'urls:rollback-last';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last completed URL path recompilation run';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Bắt đầu tìm kiếm đợt biên dịch URL cuối cùng...');

        $history = UrlSettingHistory::where('status', 'completed')
            ->orderBy('id', 'desc')
            ->first();

        if (! $history) {
            $this->error('Không tìm thấy đợt biên dịch hoàn thành nào để rollback.');

            return 1;
        }

        $this->warn("Tìm thấy đợt biên dịch ID #{$history->id} chạy lúc {$history->finished_at}.");
        $this->warn(" - Pattern bài viết cũ: '{$history->old_article_pattern}' -> mới: '{$history->new_article_pattern}'");
        $this->warn(" - Pattern danh mục cũ: '{$history->old_category_pattern}' -> mới: '{$history->new_category_pattern}'");

        if (! $this->confirm('Bạn có chắc chắn muốn khôi phục lại toàn bộ đường dẫn URL trước đợt này?', false)) {
            $this->info('Hủy bỏ thao tác.');

            return 0;
        }

        $items = $history->items()->where('status', 'updated')->get();
        $total = $items->count();

        $this->info("Đang khôi phục {$total} mục về đường dẫn cũ...");
        $this->output->progressStart($total);

        $success = 0;
        $failed = 0;

        foreach ($items as $item) {
            DB::beginTransaction();
            try {
                if ($item->target_type === 'category') {
                    $category = Category::find($item->target_id);
                    if ($category) {
                        $category->url_path = $item->old_path;
                        $category->saveQuietly();
                    }
                } elseif ($item->target_type === 'article') {
                    $article = Article::find($item->target_id);
                    if ($article) {
                        $article->url_path = $item->old_path;
                        $article->saveQuietly();
                    }
                }

                // Delete redirects created during this history run
                if (! empty($item->old_path) && ! empty($item->new_path)) {
                    UrlRedirect::where('old_path', $item->old_path)
                        ->where('new_path', $item->new_path)
                        ->delete();
                }

                $item->update(['status' => 'pending']); // Reset status
                DB::commit();
                $success++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $failed++;
                $this->error("\nLỗi khi rollback mục ID {$item->id}: ".$e->getMessage());
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        // Revert active patterns in configuration
        Setting::set('url_pattern_article', $history->old_article_pattern);
        Setting::set('url_pattern_category', $history->old_category_pattern);

        // Update history status to show it was rolled back
        $history->update(['status' => 'pending']); // or we could add a 'rolled_back' status

        $this->info("Đã hoàn tất rollback. Thành công: {$success}, Thất bại: {$failed}. Thư mục cấu hình đã được khôi phục về cũ.");

        // Clear caches
        try {
            Artisan::call('optimize:clear');
        } catch (\Throwable $e) {
        }

        return 0;
    }
}
