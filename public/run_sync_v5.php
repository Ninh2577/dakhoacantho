<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}
if (($_GET['key'] ?? '') !== 'gemini_secret_123') {
    http_response_code(403);
    exit('Access Denied');
}
header('Content-Type: text/plain; charset=utf-8');

shell_exec('php ../artisan media:sync > sync_log.txt 2>&1');
echo "Command executed.";
