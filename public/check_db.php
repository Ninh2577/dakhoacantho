<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

function getFolderSize($dir) {
    $size = 0;
    if (!is_dir($dir)) {
        return 0;
    }
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        return $bytes . ' bytes';
    } elseif ($bytes == 1) {
        return '1 byte';
    } else {
        return '0 bytes';
    }
}

$uploadsDir = storage_path('app/public/uploads');

echo "=== UPLOADS DIRECTORY SIZE ANALYSIS ===\n";
echo "Path: " . $uploadsDir . "\n";
$totalSize = getFolderSize($uploadsDir);
echo "Total Size: " . formatSize($totalSize) . "\n\n";

$subfolders = glob($uploadsDir . '/*', GLOB_ONLYDIR);
foreach ($subfolders as $sub) {
    $name = basename($sub);
    $size = getFolderSize($sub);
    echo "- /" . $name . ": " . formatSize($size) . "\n";
}
