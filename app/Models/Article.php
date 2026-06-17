<?php

namespace App\Models;

use App\Services\UrlRoutingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail_image',
        'featured_image',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'schema_type',
        'schema_json',
        'focus_keyword',
        'seo_slug',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'seo_score',
        'seo_checks',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'is_published' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'published_at' => 'datetime',
        'schema_json' => 'array',
        'seo_checks' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    protected static function booted()
    {
        static::saving(function ($article) {
            // Auto-set published_at on first publish
            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }
            // Do NOT reset published_at if already published

            // Sync thumbnail_image and featured_image safely
            if ($article->isDirty('featured_image')) {
                $article->thumbnail_image = $article->featured_image;
            } elseif ($article->isDirty('thumbnail_image')) {
                $article->featured_image = $article->thumbnail_image;
            } else {
                if (empty($article->featured_image) && ! empty($article->thumbnail_image)) {
                    $article->featured_image = $article->thumbnail_image;
                } elseif (empty($article->thumbnail_image) && ! empty($article->featured_image)) {
                    $article->thumbnail_image = $article->featured_image;
                }
            }

            $pattern = Setting::get('url_pattern_article') ?: '{slug}';
            $service = app(UrlRoutingService::class);
            $article->url_path = $service->compileArticlePath($article, $pattern);
        });

        static::saved(function ($article) {
            Cache::forget('home_latest_articles');
            Cache::forget("dakhoacantho:articles:related:{$article->id}");
            Cache::forget('dakhoacantho:sitemap:xml:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:xml:'.request()->getHost());
            }
            foreach (Category::all() as $cat) {
                Cache::forget("category_related_articles_{$cat->id}");
            }
        });

        static::deleted(function ($article) {
            Cache::forget('home_latest_articles');
            Cache::forget("dakhoacantho:articles:related:{$article->id}");
            Cache::forget('dakhoacantho:sitemap:xml:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:xml:'.request()->getHost());
            }
            foreach (Category::all() as $cat) {
                Cache::forget("category_related_articles_{$cat->id}");
            }
        });
    }

    public function getCategoryPathAttribute()
    {
        return $this->category ? $this->category->full_path : 'uncategorized';
    }

    public function getPublicUrlAttribute()
    {
        $path = $this->url_path ?: $this->slug;

        return url(ltrim($path, '/'));
    }
}
