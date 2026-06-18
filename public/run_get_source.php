<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
}

header('Content-Type: text/plain');

$output = shell_exec("C:\\xampp\\php\\php.exe C:\\xampp\\htdocs\\dakhoacantho_web\\public\\get_source_cli.php 2>&1");
echo $output;
