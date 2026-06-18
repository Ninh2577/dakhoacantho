<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

header('Content-Type: text/plain');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// 1. Authenticate as admin user
$admin = User::where('role', 'admin')->first();
if (!$admin) {
    die("ERROR: Admin user not found.\n");
}
Auth::login($admin);
echo "Logged in as: " . $admin->name . "\n";

// 2. Simulate GET request to /admin/articles/create?tab=-tab_content-tab
$subRequest = Illuminate\Http\Request::create(
    '/admin/articles/create', 
    'GET', 
    ['tab' => '-tab_content-tab']
);

// Copy session and cookies to subRequest
$subRequest->setLaravelSession(session());
$subRequest->setUserResolver(function() use ($admin) { return $admin; });

// Dispatch request via Kernel
try {
    $subResponse = $kernel->handle($subRequest);
    $html = $subResponse->getContent();
    
    file_put_contents('source.html', $html);
    echo "Successfully wrote source.html (" . strlen($html) . " bytes).\n";
} catch (\Exception $e) {
    echo "Error dispatching request: " . $e->getMessage() . "\n";
}
