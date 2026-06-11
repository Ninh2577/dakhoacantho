<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticlePreviewController extends Controller
{
    public function createPreview(Request $request)
    {
        $data = session('article_preview_create');

        if (!$data) {
            return redirect('/admin/articles/create')
                ->with('error', 'Chưa có dữ liệu xem trước. Vui lòng bấm Xem trước từ trang tạo mới bài viết.');
        }

        // Create temporary unsaved Article model
        $article = new Article();
        $article->forceFill([
            'id' => 0,
            'title' => $data['title'] ?? '',
            'slug' => $data['slug'] ?? '',
            'content' => $data['content'] ?? '',
            'excerpt' => $data['excerpt'] ?? '',
            'featured_image' => $data['featured_image'] ?? null,
            'thumbnail_image' => $data['featured_image'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'schema_type' => $data['schema_type'] ?? 'Article',
            'robots_index' => false, // Override to false for previews
            'robots_follow' => false, // Override to false for previews
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image' => $data['og_image'] ?? null,
            'twitter_title' => $data['twitter_title'] ?? null,
            'twitter_description' => $data['twitter_description'] ?? null,
            'twitter_image' => $data['twitter_image'] ?? null,
        ]);

        $article->created_at = now();
        $article->updated_at = now();

        // Load Category relation
        if (!empty($data['category_id'])) {
            $category = Category::find($data['category_id']);
            if ($category) {
                $article->setRelation('category', $category);
            }
        }

        if (!$article->category) {
            $fallbackCategory = new Category(['name' => 'Chuyên khoa', 'slug' => 'chuyen-khoa']);
            $article->setRelation('category', $fallbackCategory);
        }

        // Query Related Articles: Prioritize same category, fallback to latest, exclude current (id = 0)
        $relatedArticles = collect();
        if ($article->category_id) {
            $relatedArticles = Article::with('category.parent.parent')
                ->where('category_id', $article->category_id)
                ->where('id', '!=', 0)
                ->where('is_published', true)
                ->latest()
                ->take(4)
                ->get();
        }

        if ($relatedArticles->count() < 4) {
            $needed = 4 - $relatedArticles->count();
            $excludeIds = $relatedArticles->pluck('id')->push(0)->toArray();
            
            $fallbackArticles = Article::with('category.parent.parent')
                ->whereNotIn('id', $excludeIds)
                ->where('is_published', true)
                ->latest()
                ->take($needed)
                ->get();

            $relatedArticles = $relatedArticles->merge($fallbackArticles);
        }

        // Process storage upload path replacements (local subdirectories support)
        $article->content = str_replace('/storage/uploads/', asset('storage/uploads') . '/', $article->content);

        // Safe server-side Inline CTA injection after the second paragraph (approx 35% of content)
        $paragraphs = explode('</p>', $article->content);
        if (count($paragraphs) > 3) {
            $ctaHtml = view('components.article-inline-cta')->render();
            $paragraphs[1] .= '</p>' . $ctaHtml;
            $article->content = implode('</p>', $paragraphs);
        } else {
            $ctaHtml = view('components.article-inline-cta')->render();
            $article->content .= $ctaHtml;
        }

        // Ensure all inline content images have lazy loading and async decoding
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

        return view('articles.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'isPreview' => true,
        ]);
    }
}
