<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

class ArticleController extends Controller
{
    public function show(string $slug)
    {
        $reservedSlugs = [
            'admin', 'login', 'logout', 'register', 'lien-he', 'tim-kiem', 
            'category', 'categories', 'bai-viet', 'articles', 'nam-khoa', 
            'phu-khoa', 'ngoai-khoa', 'benh-xa-hoi', 'xet-nghiem', 'vi-cong-dong', 
            'gioi-thieu', 'chinh-sach-bao-mat', 'dieu-khoan-su-dung', 'sitemap',
            'sitemap.xml'
        ];

        if (in_array(strtolower($slug), $reservedSlugs)) {
            abort(404);
        }

        $query = Article::where('slug', $slug);
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $query->where('is_published', true);
        }
        $article = $query->firstOrFail();

        $routingService = app(\App\Services\UrlRoutingService::class);
        $currentPath = $routingService->normalizePath(request()->path());
        $newPath = $routingService->normalizePath($article->url_path);

        if ($currentPath !== $newPath) {
            return redirect()->to($article->public_url, 301);
        }

        return $this->showResolved($article);
    }

    public function showResolved(Article $article)
    {
        // Eager load category.parent.parent and approved comments to avoid N+1 queries
        $article->load([
            'category.parent.parent', 
            'comments' => function($query) {
                $query->where('status', 'approved')->latest();
            }
        ]);

        // 1. Query Related Articles: Prioritize same category, fallback to latest, exclude current
        $relatedArticles = collect();
        if ($article->category_id) {
            $relatedArticles = Article::with('category.parent.parent')
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
            
            $fallbackArticles = Article::with('category.parent.parent')
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

        // 4. Ensure all inline content images have lazy loading and async decoding
        $article->content = preg_replace_callback('/<img\s+([^>]*)/i', function($matches) {
            $attributes = $matches[1];
            
            if (stripos($attributes, 'loading=') === false) {
                $attributes .= ' loading="lazy"';
            }
            if (stripos($attributes, 'decoding=') === false) {
                $attributes .= ' decoding="async"';
            }
            
            return '<img ' . $attributes;
        }, $article->content);

        return view('articles.show', compact('article', 'relatedArticles'));
    }

    public function redirectOldUrl(string $category_path, string $slug)
    {
        $query = Article::where('slug', $slug);
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $query->where('is_published', true);
        }
        $article = $query->firstOrFail();

        $routingService = app(\App\Services\UrlRoutingService::class);
        $currentPath = $routingService->normalizePath(request()->path());
        $newPath = $routingService->normalizePath($article->url_path);

        if ($currentPath === $newPath) {
            return $this->showResolved($article);
        }

        return redirect()->to($article->public_url, 301);
    }
}
