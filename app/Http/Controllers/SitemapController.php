<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Article;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap.
     */
    public function index()
    {
        $categories = Category::all();
        $articles = Article::with('category')->where('is_published', true)->get();

        return response()->view('sitemap', compact('categories', 'articles'))
            ->header('Content-Type', 'text/xml');
    }
}
