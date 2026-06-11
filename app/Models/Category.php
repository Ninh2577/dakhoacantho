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

    public function getPublicUrlAttribute()
    {
        $path = $this->url_path ?: 'category/' . $this->full_path;
        return url(ltrim($path, '/'));
    }

    public function allDescendants()
    {
        $descendants = collect();
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->allDescendants());
        }
        return $descendants;
    }

    public static function findBySlug(string $slug): ?Category
    {
        $categories = \Illuminate\Support\Facades\Cache::remember('dakhoacantho:categories:by_slug', now()->addHours(24), function () {
            return self::all()->keyBy('slug');
        });
        return $categories->get($slug);
    }

    protected static function booted()
    {
        static::saving(function ($category) {
            $pattern = \App\Models\Setting::get('url_pattern_category') ?: 'category/{categories}';
            $service = app(\App\Services\UrlRoutingService::class);
            $category->url_path = $service->compileCategoryPath($category, $pattern);
        });

        static::saved(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('public_navigation_categories');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:categories:by_slug');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:footer:categories');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:sitemap:xml:' . parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                \Illuminate\Support\Facades\Cache::forget('dakhoacantho:sitemap:xml:' . request()->getHost());
            }
            foreach (self::all() as $cat) {
                \Illuminate\Support\Facades\Cache::forget("category_full_path_{$cat->id}");
            }

            if ($category->wasChanged('slug') || $category->wasChanged('parent_id')) {
                $service = app(\App\Services\UrlRoutingService::class);
                $patternCat = \App\Models\Setting::get('url_pattern_category') ?: 'category/{categories}';
                
                // Recompile descendant categories path quietly to prevent loop
                foreach ($category->allDescendants() as $descendant) {
                    $newPath = $service->compileCategoryPath($descendant, $patternCat);
                    $descendant->url_path = $newPath;
                    $descendant->saveQuietly();
                }

                // Recompile all articles under this category hierarchy quietly
                $patternArt = \App\Models\Setting::get('url_pattern_article') ?: '{slug}';
                $categoryIds = array_merge([$category->id], $category->allDescendants()->pluck('id')->toArray());
                $articles = \App\Models\Article::whereIn('category_id', $categoryIds)->get();
                foreach ($articles as $article) {
                    $newPath = $service->compileArticlePath($article, $patternArt);
                    $article->url_path = $newPath;
                    $article->saveQuietly();
                }
            }
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('public_navigation_categories');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:categories:by_slug');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:footer:categories');
            \Illuminate\Support\Facades\Cache::forget('dakhoacantho:sitemap:xml:' . parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                \Illuminate\Support\Facades\Cache::forget('dakhoacantho:sitemap:xml:' . request()->getHost());
            }
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
