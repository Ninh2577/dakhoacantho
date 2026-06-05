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

    public function getCategoryPathAttribute()
    {
        return $this->category ? $this->category->full_path : 'uncategorized';
    }
}
