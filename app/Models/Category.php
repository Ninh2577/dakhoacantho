<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'description'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $slugs = [];
        $category = $this;
        while ($category) {
            array_unshift($slugs, $category->slug);
            $category = $category->parent;
        }
        return implode('/', $slugs);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
