<?php

use App\Http\Controllers\Admin\ArticlePreviewController;
use App\Http\Controllers\Admin\InternalLinkSearchController;
use App\Http\Controllers\Admin\TinyMCEUploadController;
use App\Http\Controllers\ArticleCommentController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DynamicRouterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Models\User;
use App\Http\Controllers\Api\ArticleSyncController;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// API Sync Endpoint
Route::get('/api/v1/sync/articles', [ArticleSyncController::class, 'getArticlesForSync']);

Route::get('/my-test-debug', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    
    $dbConnection = config('database.default');
    $dbConfig = config("database.connections.{$dbConnection}");
    
    $article = null;
    $error = null;
    try {
        $article = \App\Models\Article::find(1222);
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
    
    return response()->json([
        'default_connection' => $dbConnection,
        'config' => $dbConfig,
        'article' => $article ? $article->toArray() : null,
        'error' => $error,
    ]);
});

if (app()->environment('local')) {

    Route::get('/native-session-test', function () {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (! isset($_SESSION['test_count'])) {
            $_SESSION['test_count'] = 1;
        } else {
            $_SESSION['test_count']++;
        }

        return response()->json([
            'engine' => 'Native PHP Session',
            'session_id' => session_id(),
            'visit_count' => $_SESSION['test_count'],
            'save_path' => session_save_path(),
            'cookies_received' => $_COOKIE,
        ]);
    });

    Route::get('/laravel-session-test', function () {
        $session = request()->session();
        $count = $session->get('test_count', 0) + 1;
        $session->put('test_count', $count);
        $session->save();

        $sessions_dir = storage_path('framework/sessions');
        $is_writable = is_writable($sessions_dir);
        $dir_exists = is_dir($sessions_dir);

        return response()->json([
            'engine' => 'Laravel Session',
            'session_driver' => config('session.driver'),
            'session_id' => $session->getId(),
            'visit_count' => $count,
            'cookie_domain' => config('session.domain'),
            'cookie_secure' => config('session.secure'),
            'same_site' => config('session.same_site'),
            'sessions_dir' => $sessions_dir,
            'sessions_dir_exists' => $dir_exists,
            'sessions_dir_writable' => $is_writable,
            'cookies_received' => $_COOKIE,
        ]);
    });


    Route::get('/request-test', function () {
        return response()->json([
            'url' => request()->url(),
            'full_url' => request()->fullUrl(),
            'is_secure' => request()->isSecure(),
            'base_path' => request()->getBasePath(),
            'base_url' => request()->getBaseUrl(),
            'filament_url' => Filament::getUrl(),
            'intended_url' => session()->get('url.intended'),
            'header_host' => request()->header('host'),
            'header_x_forwarded_proto' => request()->header('x-forwarded-proto'),
            'header_x_forwarded_port' => request()->header('x-forwarded-port'),
            'server_port' => $_SERVER['SERVER_PORT'] ?? null,
            'https_server_var' => $_SERVER['HTTPS'] ?? null,
        ]);
    });


    Route::get('/debug-logs', function () {
        $routingServiceFile = app_path('Services/UrlRoutingService.php');
        $mtime = file_exists($routingServiceFile) ? date('Y-m-d H:i:s', filemtime($routingServiceFile)) : 'unknown';

        $logFile = storage_path('logs/laravel.log');
        if (! file_exists($logFile)) {
            return 'Routing service mtime: '.$mtime."\nLog file not found at ".$logFile;
        }

        $lines = file($logFile);
        $output = [];
        $output[] = 'Routing service mtime: '.$mtime."\n";
        // Look at last 2000 lines, keep error messages and timestamps
        $recentLines = array_slice($lines, -2000);
        foreach ($recentLines as $line) {
            if (strpos($line, 'ERROR:') !== false || strpos($line, 'Exception') !== false || preg_match('/^\[202\d-/', $line)) {
                $output[] = $line;
            }
        }

        return response(implode('', $output), 200, ['Content-Type' => 'text/plain']);
    });

}

// TinyMCE admin image upload route
Route::post('/admin/tinymce/upload-image', [TinyMCEUploadController::class, 'upload'])
    ->name('admin.tinymce.upload-image')
    ->middleware(['web', 'auth']);

// TinyMCE admin internal links search API route
Route::get('/admin/api/internal-links/search', [InternalLinkSearchController::class, 'search'])
    ->name('admin.internal-links.search')
    ->middleware(['web', 'auth']);

// Article preview create route (supports POST for synchronous preview form submits)
Route::match(['get', 'post'], '/admin/articles/preview-create', [ArticlePreviewController::class, 'createPreview'])
    ->name('admin.articles.preview-create')
    ->middleware(['web', 'auth']);

// Article preview show route using cache token
Route::get('/admin/articles/preview/{uuid}', [ArticlePreviewController::class, 'showCachePreview'])
    ->name('admin.articles.preview-show')
    ->middleware(['web', 'auth']);

// 1. Home Page
Route::get('/', [PageController::class, 'home'])->name('home');

// Route for named 'login' required by Laravel's auth middleware
Route::get('/login', function () {
    return redirect()->to('/giaphuoc57hv');
})->name('login');

// Secret admin login URL - redirect to the hidden Filament login endpoint
Route::get('/giaphuoc57hv', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }
    return redirect('/admin/giaphuoc57hv');
})->name('admin.secret.login');

// 2. Contact Page
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');

Route::view('/chinh-sach-bao-mat', 'policies.privacy')->name('privacy.policy');
Route::view('/dieu-khoan-su-dung', 'policies.terms')->name('terms.policy');


// Category Index Page
Route::get('/chuyen-khoa', [CategoryController::class, 'index'])->name('categories.index');

// 3. Consultation Form POST (with rate limiting to prevent spam)
Route::post('/tu-van', [ConsultationController::class, 'store'])
    ->name('consultation.store')
    ->middleware('throttle:3,1');

// Search Route
Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');

// 4. SEO Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index_v2'])->name('sitemap');

// robots.txt route (dynamic sitemap declaration)
Route::get('/robots.txt', function () {
    $sitemapUrl = url('sitemap.xml');
    $content = "User-agent: *\nDisallow:\n\nSitemap: {$sitemapUrl}";
    return response($content)->header('Content-Type', 'text/plain; charset=utf-8');
});

// Old URL format redirect routes (.html) - placed BEFORE category wildcard to prevent hijack
Route::get('/category/{category_path}/{slug}.html', [ArticleController::class, 'redirectOldUrl'])
    ->where('category_path', '.*')
    ->where('slug', '[A-Za-z0-9\-]+');

Route::get('/{category_path}/{slug}.html', [ArticleController::class, 'redirectOldUrl'])
    ->where('category_path', '.*')
    ->where('slug', '[A-Za-z0-9\-]+');

// 5. Category Archive (wildcard to capture nested parent/child paths)
Route::get('/category/{category_path}', [CategoryController::class, 'show'])
    ->where('category_path', '.*')
    ->name('category.show');

// 7. Store Article Comment
Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store'])
    ->name('articles.comments.store')
    ->middleware(['web', 'throttle:5,1']);

// 8. Root-level Article Details (placed at the VERY bottom as a fallback)
Route::get('/{slug}', [ArticleController::class, 'show'])
    ->where('slug', '^(?!admin|login|logout|register|lien-he|tim-kiem|category|categories|bai-viet|articles|nam-khoa|phu-khoa|ngoai-khoa|benh-xa-hoi|xet-nghiem|vi-cong-dong|gioi-thieu|chinh-sach-bao-mat|dieu-khoan-su-dung|sitemap|sitemap\.xml$)[A-Za-z0-9\-]+')
    ->name('articles.show');

// 9. Fallback Dynamic Router catch-all
Route::fallback([DynamicRouterController::class, 'resolve']);
