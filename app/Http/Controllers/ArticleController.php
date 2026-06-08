<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

class ArticleController extends Controller
{
    public function show(string $category_path, string $slug)
    {
        // Eager load category and approved comments to avoid N+1 queries
        $article = Article::with([
            'category', 
            'comments' => function($query) {
                $query->where('status', 'approved')->latest();
            }
        ])
        ->where('slug', $slug)
        ->where('is_published', true)
        ->firstOrFail();

        // Strict SEO path verification:
        if ($article->category_path !== $category_path) {
            abort(404);
        }

        // 1. Query Related Articles: Prioritize same category, fallback to latest, exclude current
        $relatedArticles = collect();
        if ($article->category_id) {
            $relatedArticles = Article::with('category')
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->where('is_published', true)
                ->latest()
                ->take(4)
                ->get();
        }

        if ($relatedArticles->count() < 4) {
            $needed = 4 - $relatedArticles->count();
            $excludeIds = $relatedArticles->pluck('id')->push($article->id)->toArray();
            
            $fallbackArticles = Article::with('category')
                ->whereNotIn('id', $excludeIds)
                ->where('is_published', true)
                ->latest()
                ->take($needed)
                ->get();

            $relatedArticles = $relatedArticles->merge($fallbackArticles);
        }

        // 2. Dynamically replace storage uploads path to support local subdirectories (XAMPP) and production domain root
        $article->content = str_replace('/storage/uploads/', asset('storage/uploads') . '/', $article->content);

        // 3. Safe server-side Inline CTA injection after the second paragraph (approx 35% of content)
        // This is a pure string split and merge, preventing XML/DOM parser crashes on malformed WYSIWYG HTML.
        $paragraphs = explode('</p>', $article->content);
        if (count($paragraphs) > 3) {
            $ctaHtml = view('components.article-inline-cta')->render();
            // Append CTA block after the second paragraph
            $paragraphs[1] .= '</p>' . $ctaHtml;
            $article->content = implode('</p>', $paragraphs);
        } else {
            // Fallback: append disclaimer and CTA at the end
            $ctaHtml = view('components.article-inline-cta')->render();
            $article->content .= $ctaHtml;
        }

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
