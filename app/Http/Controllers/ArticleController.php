<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

class ArticleController extends Controller
{
    public function show(string $category_path, string $slug)
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Strict SEO path verification:
        // Ensure that the accessed category_path matches the article's actual category full path
        if ($article->category_path !== $category_path) {
            abort(404);
        }

        // Dynamically replace storage uploads path to support local subdirectories (XAMPP) and production domain root
        $article->content = str_replace('/storage/uploads/', asset('storage/uploads') . '/', $article->content);

        return view('articles.show', compact('article'));
    }
}
