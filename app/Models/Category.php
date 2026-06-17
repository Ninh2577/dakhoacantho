<?php

namespace App\Models;

use App\Services\UrlRoutingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use SolutionForest\FilamentTree\Concern\ModelTree;

class Category extends Model
{
    use ModelTree;

    protected $fillable = ['parent_id', 'order', 'name', 'slug', 'description', 'featured_image'];

    /**
     * Get category tree formatted options for Select dropdown.
     */
    public static function getTreeOptions(): array
    {
        return Cache::remember('dakhoacantho:categories:tree_options', now()->addHours(12), function () {
            // Load all categories ordered by the 'order' column
            $categories = self::orderBy('order')->get();
            $grouped = $categories->groupBy('parent_id');

            $options = [];
            // Root categories in SolutionForest\FilamentTree have parent_id = -1
            $roots = $grouped->get(-1) ?? collect();

            foreach ($roots as $root) {
                self::buildTreeOption($root, $grouped, $options, 0);
            }

            return $options;
        });
    }

    private static function buildTreeOption($category, $grouped, &$options, int $depth): void
    {
        $prefix = $depth > 0 ? str_repeat('—', $depth).' ' : '';
        $options[$category->id] = $prefix.$category->name;

        $children = $grouped->get($category->id) ?? collect();
        foreach ($children as $child) {
            self::buildTreeOption($child, $grouped, $options, $depth + 1);
        }
    }

    /**
     * Get IDs of category and all its descendants.
     */
    public static function getDescendantIdsAndSelf(int $categoryId): array
    {
        return Cache::remember("dakhoacantho:categories:descendants_and_self:{$categoryId}", now()->addHours(12), function () use ($categoryId) {
            $categories = self::all();
            $grouped = $categories->groupBy('parent_id');

            $ids = [$categoryId];
            self::collectDescendantIds($categoryId, $grouped, $ids);

            return $ids;
        });
    }

    private static function collectDescendantIds(int $parentId, $grouped, array &$ids): void
    {
        $children = $grouped->get($parentId) ?? collect();
        foreach ($children as $child) {
            $ids[] = $child->id;
            self::collectDescendantIds($child->id, $grouped, $ids);
        }
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $id = $this->id;

        return Cache::remember("category_full_path_{$id}", now()->addHours(6), function () {
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
        $path = $this->url_path ?: 'category/'.$this->full_path;

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
        $categories = Cache::remember('dakhoacantho:categories:by_slug', now()->addHours(24), function () {
            return self::all()->keyBy('slug');
        });

        return $categories->get($slug);
    }

    protected static function booted()
    {
        static::saving(function ($category) {
            $pattern = Setting::get('url_pattern_category') ?: 'category/{categories}';
            $service = app(UrlRoutingService::class);
            $category->url_path = $service->compileCategoryPath($category, $pattern);
        });

        static::saved(function ($category) {
            Cache::forget('public_navigation_categories');
            Cache::forget('dakhoacantho:categories:by_slug');
            Cache::forget('dakhoacantho:footer:categories');
            Cache::forget('dakhoacantho:categories:tree_options');
            Cache::forget('dakhoacantho:sitemap:xml:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:xml:'.request()->getHost());
            }
            foreach (self::all() as $cat) {
                Cache::forget("category_full_path_{$cat->id}");
                Cache::forget("dakhoacantho:categories:descendants_and_self:{$cat->id}");
            }

            if ($category->wasChanged('slug') || $category->wasChanged('parent_id')) {
                $service = app(UrlRoutingService::class);
                $patternCat = Setting::get('url_pattern_category') ?: 'category/{categories}';

                // Recompile descendant categories path quietly to prevent loop
                foreach ($category->allDescendants() as $descendant) {
                    $newPath = $service->compileCategoryPath($descendant, $patternCat);
                    $descendant->url_path = $newPath;
                    $descendant->saveQuietly();
                }

                // Recompile all articles under this category hierarchy quietly
                $patternArt = Setting::get('url_pattern_article') ?: '{slug}';
                $categoryIds = array_merge([$category->id], $category->allDescendants()->pluck('id')->toArray());
                $articles = Article::whereIn('category_id', $categoryIds)->get();
                foreach ($articles as $article) {
                    $newPath = $service->compileArticlePath($article, $patternArt);
                    $article->url_path = $newPath;
                    $article->saveQuietly();
                }
            }
        });

        static::deleted(function () {
            Cache::forget('public_navigation_categories');
            Cache::forget('dakhoacantho:categories:by_slug');
            Cache::forget('dakhoacantho:footer:categories');
            Cache::forget('dakhoacantho:categories:tree_options');
            Cache::forget('dakhoacantho:sitemap:xml:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:xml:'.request()->getHost());
            }
            foreach (self::all() as $cat) {
                Cache::forget("category_full_path_{$cat->id}");
                Cache::forget("dakhoacantho:categories:descendants_and_self:{$cat->id}");
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
