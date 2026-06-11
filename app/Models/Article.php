<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail_image',
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
        'is_published'  => 'boolean',
        'robots_index'  => 'boolean',
        'robots_follow' => 'boolean',
        'published_at'  => 'datetime',
        'schema_json'   => 'array',
        'seo_checks'    => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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

            $pattern = \App\Models\Setting::get('url_pattern_article') ?: '{slug}';
            $service = app(\App\Services\UrlRoutingService::class);
            $article->url_path = $service->compileArticlePath($article, $pattern);
        });

        static::saved(function ($article) {
            \Illuminate\Support\Facades\Cache::forget('home_latest_articles');
            foreach (\App\Models\Category::all() as $cat) {
                \Illuminate\Support\Facades\Cache::forget("category_related_articles_{$cat->id}");
            }
        });

        static::deleted(function ($article) {
            \Illuminate\Support\Facades\Cache::forget('home_latest_articles');
            foreach (\App\Models\Category::all() as $cat) {
                \Illuminate\Support\Facades\Cache::forget("category_related_articles_{$cat->id}");
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
