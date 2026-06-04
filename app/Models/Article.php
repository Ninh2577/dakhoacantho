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
