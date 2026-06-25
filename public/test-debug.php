<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

try {
    $options = [
        'pageType' => 'home',
        'title' => 'Trang chủ',
        'description' => 'Trang chủ phòng khám',
    ];
    $schema = \App\Support\SchemaBuilder::build($options);
    echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
