<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

class ArticleController extends Controller
{
    public function show(string $category_slug, string $article_slug)
    {
        $article = Article::with('category')
            ->where('slug', $article_slug)
            ->whereHas('category', function ($query) use ($category_slug) {
                $query->where('slug', $category_slug);
            })
            ->where('is_published', true)
            ->firstOrFail();

        return view('articles.show', compact('article'));
    }
}
