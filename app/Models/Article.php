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
        'thumbnail_image',
        'meta_title',
        'meta_description',
        'is_published',
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
