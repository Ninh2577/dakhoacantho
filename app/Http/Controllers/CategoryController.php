<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Article;
use Illuminate\Http\Request;

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
        $articles = Article::with('category')
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
        $selectedCategory = Category::with('children')->where('slug', $slug)->firstOrFail();

        // Strict SEO Path Verification: redirect or fail if the path segments are not perfectly matched
        if ($selectedCategory->full_path !== $category_path) {
            abort(404);
        }

        // Retrieve all root categories and their descendants for the category sidebar index
        $categories = Category::where('parent_id', -1)->with('children')->get();

        // Collect the category ID and all of its subcategories' IDs recursively
        $categoryIds = $this->getCategoryAndChildrenIds($selectedCategory);

        // Paginate articles belonging to the selected category and all its descendants
        $articles = Article::with('category')
            ->whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->latest()
            ->paginate(4);

        // Retrieve the first featured article
        $featuredArticle = Article::with('category')
            ->whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->first();

        // Retrieve 9 latest articles belonging to this category hierarchy (with category eager loading)
        $relatedArticles = Article::with('category')
            ->whereIn('category_id', $categoryIds)
            ->where('is_published', true)
            ->latest()
            ->take(9)
            ->get();

        // Fallback: if there are fewer than 9 articles, merge the latest published articles from other categories
        if ($relatedArticles->count() < 9) {
            $needed = 9 - $relatedArticles->count();
            $excludeIds = $relatedArticles->pluck('id')->toArray();
            
            $fallbackArticles = Article::with('category')
                ->whereNotIn('id', $excludeIds)
                ->where('is_published', true)
                ->latest()
                ->take($needed)
                ->get();
                
            $relatedArticles = $relatedArticles->merge($fallbackArticles);
        }

        // Check for custom landing page
        $customView = 'categories.landing.' . $selectedCategory->slug;
        if (view()->exists($customView)) {
            $category = $selectedCategory;
            return view($customView, compact('categories', 'category', 'selectedCategory', 'articles', 'featuredArticle', 'relatedArticles'));
        }

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
