<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ArticleCommentController;

// Diagnostic routes
Route::get('/native-session-test', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['test_count'])) {
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

Route::get('/db-test', function () {
    $user = \App\Models\User::where('email', 'admin@dakhoacantho.com')->first();
    
    if (request()->has('reset')) {
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Admin',
                'email' => 'admin@dakhoacantho.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Admin user created successfully with password "password"!',
                'user' => $user,
            ]);
        } else {
            $user->password = \Illuminate\Support\Facades\Hash::make('password');
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Admin user password reset to "password" successfully!',
                'user' => $user,
            ]);
        }
    }
    
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'User admin@dakhoacantho.com does not exist in the database! Go to /db-test?reset=1 to create it.',
        ]);
    }
    
    $password_matches = \Illuminate\Support\Facades\Hash::check('password', $user->password);
    
    $implements_filament_user = $user instanceof \Filament\Models\Contracts\FilamentUser;
    $can_access_panel = 'N/A';
    if ($implements_filament_user) {
        try {
            $panel = \Filament\Facades\Filament::getCurrentPanel();
            if (!$panel) {
                // Get the admin panel manually if not booted in this request context
                $panel = \Filament\Facades\Filament::getPanel('admin');
            }
            $can_access_panel = $user->canAccessPanel($panel) ? 'YES' : 'NO';
        } catch (\Exception $e) {
            $can_access_panel = 'ERROR: ' . $e->getMessage();
        }
    }

    return response()->json([
        'status' => 'success',
        'user_found' => true,
        'email' => $user->email,
        'role' => $user->role,
        'password_hash' => $user->password,
        'password_matches_default_password' => $password_matches ? 'YES' : 'NO',
        'implements_filament_user' => $implements_filament_user ? 'YES' : 'NO',
        'can_access_panel' => $can_access_panel,
    ]);
});

Route::get('/request-test', function () {
    return response()->json([
        'url' => request()->url(),
        'full_url' => request()->fullUrl(),
        'is_secure' => request()->isSecure(),
        'base_path' => request()->getBasePath(),
        'base_url' => request()->getBaseUrl(),
        'filament_url' => \Filament\Facades\Filament::getUrl(),
        'intended_url' => session()->get('url.intended'),
        'header_host' => request()->header('host'),
        'header_x_forwarded_proto' => request()->header('x-forwarded-proto'),
        'header_x_forwarded_port' => request()->header('x-forwarded-port'),
        'server_port' => $_SERVER['SERVER_PORT'] ?? null,
        'https_server_var' => $_SERVER['HTTPS'] ?? null,
    ]);
});

Route::get('/debug-login-run', function() {
    $user = \App\Models\User::where('email', 'admin@dakhoacantho.com')->first();
    if (!$user) {
        return "User not found";
    }
    
    // Log the user in
    auth()->login($user);
    
    // Set a session value
    session(['test_auth' => 'authenticated_ok']);
    session()->save();
    
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'session_id' => session()->getId(),
        'session_test_auth' => session('test_auth'),
    ]);
});

Route::get('/debug-login-check', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user_id' => auth()->id(),
        'session_id' => session()->getId(),
        'session_test_auth' => session('test_auth'),
    ]);
});

Route::get('/debug-logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return "Log file not found at " . $logFile;
    }
    
    $lines = file($logFile);
    $output = [];
    // Look at last 1000 lines, keep error messages and timestamps
    $recentLines = array_slice($lines, -1000);
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR:') !== false || strpos($line, 'Exception') !== false || preg_match('/^\[202\d-/', $line)) {
            $output[] = $line;
        }
    }
    
    return response(implode("", $output), 200, ['Content-Type' => 'text/plain']);
});

// 1. Home Page
Route::get('/', [PageController::class, 'home'])->name('home');

// 2. Contact Page
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');

Route::view('/chinh-sach-bao-mat', 'policies.privacy')->name('privacy.policy');
Route::view('/dieu-khoan-su-dung', 'policies.terms')->name('terms.policy');

// Category Index Page
Route::get('/chuyen-khoa', [CategoryController::class, 'index'])->name('categories.index');

// 3. Consultation Form POST
Route::post('/tu-van', [ConsultationController::class, 'store'])->name('consultation.store');

// Search Route
Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');

// 4. SEO Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

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
Route::fallback([App\Http\Controllers\DynamicRouterController::class, 'resolve']);
