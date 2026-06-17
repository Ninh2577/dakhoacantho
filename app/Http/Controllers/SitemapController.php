<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $host = request()->getHost() ?: parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $cacheKey = "dakhoacantho:sitemap:xml:{$host}";

        $sitemapXml = Cache::remember($cacheKey, now()->addHours(6), function () {
            $categories = Category::all();
            $articles = Article::with('category')->where('is_published', true)->get();

            return view('sitemap', compact('categories', 'articles'))->render();
        });

        return response($sitemapXml)
            ->header('Content-Type', 'text/xml');
    }
}
