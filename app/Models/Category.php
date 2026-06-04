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
