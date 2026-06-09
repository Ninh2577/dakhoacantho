<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class SearchController extends Controller
{
    /**
     * Search database for articles matching query and date range.
     */
    public function index(Request $request)
    {
        // 1. Validate inputs
        $request->validate([
            'q' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = $request->input('q');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 2. Build Query using Eloquent when() helper
        $articles = Article::with('category.parent.parent')
            ->where('is_published', true)
            ->when($query, function ($qBuilder) use ($query) {
                // Nested closure isolates search criteria so OR doesn't override is_published
                $qBuilder->where(function ($subQ) use ($query) {
                    $subQ->where('title', 'LIKE', "%{$query}%")
                         ->orWhere('content', 'LIKE', "%{$query}%");
                });
            })
            ->when($startDate, function ($qBuilder) use ($startDate) {
                $qBuilder->whereDate('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($qBuilder) use ($endDate) {
                $qBuilder->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString(); // Preserves q, start_date, end_date in pagination links

        return view('search.results', compact('articles', 'query', 'startDate', 'endDate'));
    }
}
