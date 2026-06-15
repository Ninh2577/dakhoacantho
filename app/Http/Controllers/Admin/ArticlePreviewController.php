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
        if ($request->isMethod('post')) {
            // Get data from 'data' key (Filament form fields are nested under 'data')
            $postData = $request->input('data');

            // If 'data' key not found, try to collect from individual form fields
            if (!is_array($postData) || empty($postData)) {
                $postData = [];
                // Map common form field names
                $fieldNames = [
                    'title',
                    'slug',
                    'content',
                    'excerpt',
                    'featured_image',
                    'category_id',
                    'author',
                    'meta_title',
                    'meta_description',
                    'canonical_url',
                    'schema_type',
                    'og_title',
                    'og_description',
                    'og_image',
                    'twitter_title',
                    'twitter_description',
                    'twitter_image',
                    'is_published'
                ];

                foreach ($fieldNames as $field) {
                    $value = $request->input($field);
                    if ($value !== null) {
                        $postData[$field] = $value;
                    }
                }
            }

            if (!empty($postData)) {
                session()->put('article_preview_create', $postData);
                session()->save();
            }
        }

        $data = session('article_preview_create');

        if (!$data) {
            return redirect('/admin/articles/create')
                ->with('error', 'Chưa có dữ liệu xem trước. Vui lòng bấm Xem trước từ trang tạo mới bài viết.');
        }

        // Normalize inputs to scalar types (strings/null/numeric) to avoid Array to String conversion errors in the view
        $title = $this->scalarString($data['title'] ?? null, 'Bản xem trước');
        $slug = $this->scalarString($data['slug'] ?? null, 'ban-xem-truoc');
        $content = $this->scalarString($data['content'] ?? null, '');
        $excerpt = $this->scalarString($data['excerpt'] ?? null);
        $featuredImage = $this->scalarString($data['featured_image'] ?? null);
        $metaTitle = $this->scalarString($data['meta_title'] ?? null);
        $metaDescription = $this->scalarString($data['meta_description'] ?? null);
        $canonicalUrl = $this->scalarString($data['canonical_url'] ?? null);
        $schemaType = $this->scalarString($data['schema_type'] ?? null, 'Article');
        $author = $this->scalarString($data['author'] ?? null);
        $ogTitle = $this->scalarString($data['og_title'] ?? null);
        $ogDescription = $this->scalarString($data['og_description'] ?? null);
        $ogImage = $this->scalarString($data['og_image'] ?? null);
        $twitterTitle = $this->scalarString($data['twitter_title'] ?? null);
        $twitterDescription = $this->scalarString($data['twitter_description'] ?? null);
        $twitterImage = $this->scalarString($data['twitter_image'] ?? null);

        // Create temporary unsaved Article model
        $article = new Article();
        $article->forceFill([
            'id' => 0,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'thumbnail_image' => $featuredImage, // Map featured_image to thumbnail_image as expected by view
            'author' => $author,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'schema_type' => $schemaType,
            'robots_index' => false, // Override to false for previews
            'robots_follow' => false, // Override to false for previews
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'twitter_title' => $twitterTitle,
            'twitter_description' => $twitterDescription,
            'twitter_image' => $twitterImage,
        ]);

        $article->created_at = now();
        $article->updated_at = now();

        // Load Category relation
        $categoryId = is_array($data['category_id'] ?? null)
            ? collect($data['category_id'])->flatten()->filter(fn($item) => is_scalar($item))->first()
            : ($data['category_id'] ?? null);

        if (!empty($categoryId)) {
            $category = Category::find($categoryId);
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

        // Process storage upload path replacements using the unified normalizer service
        $article->content = app(\App\Services\Content\ContentImageUrlNormalizer::class)->normalize($article->content);

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
        $article->content = preg_replace_callback('/<img\s+([^>]*)/i', function ($matches) {
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

    private function scalarString(mixed $value, ?string $default = null): ?string
    {
        if (is_null($value)) {
            return $default;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value) || is_bool($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $first = collect($value)->flatten()->filter(fn($item) => is_scalar($item))->first();

            return $first !== null ? (string) $first : $default;
        }

        return $default;
    }
}
