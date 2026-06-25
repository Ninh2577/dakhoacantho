<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Services\UrlRoutingService;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display the index page listing all categories and articles.
     */
    public function index()
    {
        // Fetch the top-level main categories for the sidebar filter
        $parentCategories = Category::where('parent_id', -1)
            ->whereIn('slug', ['nam-khoa', 'phu-khoa', 'ngoai-khoa', 'noi-khoa', 'benh-xa-hoi', 'xet-nghiem'])
            ->get();

        // Fetch all published articles (paginated by 10)
        $articles = Article::with('category.parent.parent')
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('categories.index', compact('parentCategories', 'articles'));
    }

    public function show(string $category_path)
    {
        // Extract the target slug (last segment) from the category path
        $segments = explode('/', $category_path);
        $slug = end($segments);

        // Retrieve category with its immediate children
        $selectedCategory = Category::with('children')->where('slug', $slug)->first();

        // Fallback: If not a category, check if it matches an article slug and redirect
        if (! $selectedCategory) {
            $article = \App\Models\Article::where('slug', $slug)->first();
            if ($article) {
                return redirect()->to($article->public_url, 301);
            }
            abort(404);
        }

        // Strict SEO Path Verification: redirect or fail if the path segments are not perfectly matched
        if ($selectedCategory->full_path !== $category_path) {
            abort(404);
        }

        $routingService = app(UrlRoutingService::class);

        // Heal null url_path for legacy category records
        if ($selectedCategory->url_path === null) {
            try {
                $pattern = Setting::get('url_pattern_category') ?: '{categories}';
                $selectedCategory->url_path = $routingService->compileCategoryPath($selectedCategory, $pattern);
                $selectedCategory->saveQuietly();
            } catch (\Throwable) {
                $selectedCategory->url_path = $category_path;
            }
        }

        $currentPath = $routingService->normalizePath(request()->path());
        $newPath = $routingService->normalizePath($selectedCategory->url_path);

        if (! empty($newPath) && $currentPath !== $newPath) {
            return redirect()->to($selectedCategory->public_url, 301);
        }

        return $this->showResolved($selectedCategory);
    }

    public function showResolved(Category $selectedCategory)
    {
        // Retrieve all root categories and their descendants for the category sidebar index
        $categories = Category::where('parent_id', -1)->with('children')->get();

        // Collect the category ID and all of its subcategories' IDs recursively
        $categoryIds = $this->getCategoryAndChildrenIds($selectedCategory);

        // Paginate articles belonging to the selected category and all its descendants
        $articles = Article::with('category.parent.parent')
            ->whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->latest()
            ->paginate(7);

        // Retrieve the first article on the current page as the featured article
        $featuredArticle = $articles->first();

        // Retrieve and cache 9 latest related articles for 15 minutes
        $cacheKey = "category_related_articles_{$selectedCategory->id}";
        $relatedArticles = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($categoryIds) {
            $articles = Article::with('category.parent.parent')
                ->whereIn('category_id', $categoryIds)
                ->where('is_published', true)
                ->latest()
                ->take(9)
                ->get();

            if ($articles->count() < 9) {
                $needed = 9 - $articles->count();
                $excludeIds = $articles->pluck('id')->toArray();

                $fallbackArticles = Article::with('category.parent.parent')
                    ->whereNotIn('id', $excludeIds)
                    ->where('is_published', true)
                    ->latest()
                    ->take($needed)
                    ->get();

                $articles = $articles->merge($fallbackArticles);
            }

            return $articles;
        });

        return view('categories.show', compact('categories', 'selectedCategory', 'articles', 'featuredArticle', 'relatedArticles'));
    }

    /**
     * Recursively fetch IDs of a category and all its children.
     */
    private function getCategoryAndChildrenIds($category)
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getCategoryAndChildrenIds($child));
        }

        return $ids;
    }
}
