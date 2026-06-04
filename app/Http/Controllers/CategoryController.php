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
        // Get parent-level categories with subcategories
        $categories = Category::whereNull('parent_id')
            ->where('name', '!=', 'Chưa được phân loại')
            ->with('children')
            ->get();

        // Retrieve and paginate all published articles
        $articles = Article::with('category')
            ->where('is_published', true)
            ->latest()
            ->paginate(4);

        // Fetch a featured article
        $featuredArticle = Article::with('category')
            ->where('is_published', true)
            ->first();

        return view('categories.index', compact('categories', 'articles', 'featuredArticle'));
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
        $categories = Category::whereNull('parent_id')->with('children')->get();

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

        return view('categories.show', compact('categories', 'selectedCategory', 'articles', 'featuredArticle'));
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
