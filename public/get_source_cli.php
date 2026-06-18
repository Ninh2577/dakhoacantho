<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$admin = User::where('role', 'admin')->first();
if (!$admin) {
    die("ERROR: Admin user not found.\n");
}
Auth::login($admin);

$subRequest = Illuminate\Http\Request::create(
    '/admin/articles/create', 
    'GET', 
    ['tab' => '-tab_content-tab']
);

$subRequest->setUserResolver(function() use ($admin) { return $admin; });

try {
    $subResponse = $kernel->handle($subRequest);
    $html = $subResponse->getContent();
    file_put_contents(__DIR__ . '/source.html', $html);
    echo "SUCCESS: " . strlen($html) . " bytes written.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
