<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\SitemapController;

// 1. Home Page
Route::get('/', [PageController::class, 'home'])->name('home');

// 2. Contact Page
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');

// Category Index Page
Route::get('/chuyen-khoa', [CategoryController::class, 'index'])->name('categories.index');

// 3. Consultation Form POST
Route::post('/tu-van', [ConsultationController::class, 'store'])->name('consultation.store');

// 4. SEO Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// 5. Category Archive (wildcard to capture nested parent/child paths)
Route::get('/category/{category_path}', [CategoryController::class, 'show'])
    ->where('category_path', '.*')
    ->name('category.show');

// 6. Article Details (wildcard to capture nested category directories and trailing .html)
Route::get('/{category_path}/{slug}.html', [ArticleController::class, 'show'])
    ->where('category_path', '.*')
    ->name('article.show');

