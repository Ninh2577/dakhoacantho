<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Setting;
use App\Services\UrlRoutingService;

header('Content-Type: text/plain; charset=utf-8');

echo "Article Pattern: " . Setting::get('url_pattern_article') . "\n";
echo "Category Pattern: " . Setting::get('url_pattern_category') . "\n";

echo "\n=== CATEGORIES URL PATHS ===\n";
$categories = Category::all();
foreach ($categories as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Slug: {$cat->slug} | URL Path: {$cat->url_path} | Public URL: {$cat->public_url}\n";
}

echo "\n=== TESTING ROUTER RESOLVE FOR 'nam-khoa' ===\n";
$path = 'nam-khoa';
$category = Category::where('url_path', $path)->first();
if ($category) {
    echo "Found Category: ID {$category->id} | Name: {$category->name}\n";
} else {
    echo "Category not found for path: '{$path}'\n";
}

echo "\n=== TESTING ROUTER RESOLVE FOR 'nam-khoa/bao-quy-dau' ===\n";
$path = 'nam-khoa/bao-quy-dau';
$category = Category::where('url_path', $path)->first();
if ($category) {
    echo "Found Category: ID {$category->id} | Name: {$category->name}\n";
} else {
    echo "Category not found for path: '{$path}'\n";
}
