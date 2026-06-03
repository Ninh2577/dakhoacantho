<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the Homepage.
     */
    public function home()
    {
        $categories = Category::all();
        
        // Eager load category to prevent N+1 queries
        $articles = Article::with('category')
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'articles'));
    }

    /**
     * Display a specific Category/Specialty page.
     */
    public function category(?string $slug = null)
    {
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            abort(404, 'No categories found. Please run seeders first.');
        }

        // Get the selected category or default to the first one
        $selectedCategory = $slug 
            ? Category::where('slug', $slug)->firstOrFail() 
            : $categories->first();

        // Paginate articles belonging to the selected category
        $articles = Article::with('category')
            ->where('category_id', $selectedCategory->id)
            ->where('is_published', true)
            ->latest()
            ->paginate(4);

        // Get a featured article (the first one)
        $featuredArticle = Article::with('category')
            ->where('category_id', $selectedCategory->id)
            ->where('is_published', true)
            ->first();

        return view('categories.show', compact('categories', 'selectedCategory', 'articles', 'featuredArticle'));
    }

    /**
     * Display the Contact page.
     */
    public function contact()
    {
        return view('contact');
    }
}
