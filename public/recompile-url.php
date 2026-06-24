<?php
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlSettingHistory;
use App\Services\UrlRoutingService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// 1. Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// 2. Security Check: Only allow logged-in Administrator
if (!auth()->check() || auth()->user()->role !== 'admin') {
    http_response_code(403);
    die('Unauthorized. Please log in as administrator in another tab first.');
}

header('Content-Type', 'text/plain; charset=utf-8');
echo "Bắt đầu mở khóa và biên dịch URL đồng bộ...\n";

// 3. Force mark all stuck histories as failed to unlock the Filament page
UrlSettingHistory::whereIn('status', ['pending', 'processing'])->update([
    'status' => 'failed',
    'error_message' => 'Stuck process cleared by script.',
    'finished_at' => now(),
]);
echo "-> Đã dọn dẹp tiến trình bị treo (PENDING/PROCESSING).\n";

$artPattern = '{slug}';
$catPattern = '{categories}';

$routingService = app(UrlRoutingService::class);

// 4. Perform conflict check first
$check = $routingService->checkConflicts($artPattern, $catPattern);
if ($check['conflict_count'] > 0) {
    echo "LỖI: Phát hiện xung đột chéo! Bạn cần sửa đổi slug bị trùng lặp trước khi tiếp tục:\n";
    foreach ($check['conflicts'] as $conflict) {
        echo " - " . $conflict['message'] . "\n";
    }
    die();
}
echo "-> Kiểm tra xung đột: 0 lỗi. Bắt đầu cập nhật...\n";

// 5. Recompile Category URLs
$categories = Category::all();
foreach ($categories as $category) {
    DB::transaction(function () use ($category, $catPattern, $routingService) {
        $oldPath = $category->url_path;
        $newPath = $routingService->compileCategoryPath($category, $catPattern);
        $category->url_path = $newPath;
        $category->saveQuietly();
        if (!empty($oldPath) && $oldPath !== $newPath) {
            $routingService->registerRedirect($oldPath, $newPath, 'category', $category->id);
        }
    });
}
echo "-> Cập nhật URL Danh mục thành công.\n";

// 6. Recompile Article URLs
Article::with('category')->chunk(100, function ($articles) use ($artPattern, $routingService) {
    foreach ($articles as $article) {
        DB::transaction(function () use ($article, $artPattern, $routingService) {
            $oldPath = $article->url_path;
            $newPath = $routingService->compileArticlePath($article, $artPattern);
            $article->url_path = $newPath;
            $article->saveQuietly();
            if (!empty($oldPath) && $oldPath !== $newPath) {
                $routingService->registerRedirect($oldPath, $newPath, 'article', $article->id);
            }
        });
    }
});
echo "-> Cập nhật URL Bài viết thành công.\n";

// 7. Save settings to DB
Setting::set('url_pattern_article', $artPattern);
Setting::set('url_pattern_category', $catPattern);
echo "-> Đã lưu cấu hình mới vào hệ thống.\n";

// 8. Flush all caches
Artisan::call('optimize:clear');
Artisan::call('cache:clear');
Artisan::call('view:clear');
echo "-> Đã xóa toàn bộ bộ nhớ đệm (Cache/Views).\n";

echo "Đã chuyển đổi thành công cấu trúc URL phẳng!\n";

// 9. Self-destruct for security
unlink(__FILE__);
echo "Script đã tự động xóa để bảo mật hệ thống.";
