<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/chuyen-khoa/{slug?}', [PageController::class, 'category'])->name('category.show');
Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');

Route::get('/{category_slug}/{article_slug}', [App\Http\Controllers\ArticleController::class, 'show']);
