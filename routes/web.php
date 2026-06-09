<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ArticleCommentController;

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
