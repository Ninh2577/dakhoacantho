<?php
header('Content-Type: text/plain; charset=utf-8');

// The target folder containing the actual files (dynamic)
$target = dirname(__DIR__) . '/storage/app/public';

// The symbolic link location we want to create (dynamic)
$link = __DIR__ . '/storage';

echo "Target: {$target}\n";
echo "Link: {$link}\n\n";

if (!is_dir($target)) {
    echo "ERROR: Target directory does not exist. Please check your path.\n";
    exit;
}

if (file_exists($link)) {
    echo "Link location already exists.\n";
    if (is_link($link)) {
        echo "Removing existing symlink...\n";
        unlink($link);
    } else {
        echo "Renaming existing physical directory as backup...\n";
        rename($link, $link . '_backup_' . time());
    }
}

if (symlink($target, $link)) {
    echo "SUCCESS: Symlink created successfully!\n";
    echo "You should now be able to load images in production.\n";
} else {
    echo "ERROR: Failed to create symlink via PHP.\n";
}
