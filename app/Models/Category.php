<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Concern\ModelTree;

class Category extends Model
{
    use ModelTree;

    protected $fillable = ['parent_id', 'order', 'name', 'slug', 'description', 'featured_image'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $id = $this->id;
        return \Illuminate\Support\Facades\Cache::remember("category_full_path_{$id}", now()->addHours(6), function () {
            $slugs = [];
            $category = $this;
            while ($category) {
                $slugs[] = $category->slug;
                $category = $category->parent;
            }
            return implode('/', array_reverse($slugs));
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('public_navigation_categories');
            foreach (self::all() as $cat) {
                \Illuminate\Support\Facades\Cache::forget("category_full_path_{$cat->id}");
            }
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('public_navigation_categories');
            foreach (self::all() as $cat) {
                \Illuminate\Support\Facades\Cache::forget("category_full_path_{$cat->id}");
            }
        });
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function determineParentColumnName(): string
    {
        return 'parent_id';
    }

    public function determineOrderColumnName(): string
    {
        return 'order';
    }

    public function determineTitleColumnName(): string
    {
        return 'name';
    }
}
