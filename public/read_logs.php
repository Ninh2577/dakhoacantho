<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

header('Content-Type: text/plain');

$log_path = 'C:\\xampp\\apache\\logs\\access.log';

if (!file_exists($log_path)) {
    die("Apache access log not found at $log_path\n");
}

$lines = file($log_path);
$recent = array_slice($lines, -150);

echo "=== APACE ACCESS LOG (LAST 150 LINES) ===\n";
foreach ($recent as $line) {
    // Show 404s, 500s or requests containing tinymce, themes, skins, icons
    if (strpos($line, ' 404 ') !== false || 
        strpos($line, ' 500 ') !== false || 
        stripos($line, 'tinymce') !== false ||
        stripos($line, 'theme') !== false ||
        stripos($line, 'skin') !== false ||
        stripos($line, 'icon') !== false) {
        echo $line;
    }
}
