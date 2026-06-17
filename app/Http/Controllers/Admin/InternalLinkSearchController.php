<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InternalLinkSearchController extends Controller
{
    /**
     * Search internal links for TinyMCE editor.
     */
    public function search(Request $request)
    {
        // 1. Security check using Gate
        Gate::authorize('access-admin-api');

        $queryParam = $request->input('q', '');
        $excludeId = $request->input('exclude_id');

        // Normalize keyword for cache keys
        $normalizedQuery = Str::lower(trim($queryParam));
        $cacheKey = 'internal_links_search:'.md5($normalizedQuery.'_'.$excludeId);

        // 2. Fetch and Cache results for 5 minutes (300 seconds)
        $results = Cache::remember($cacheKey, 300, function () use ($queryParam, $excludeId) {
            $queryBuilder = Article::query();

            // Only published & public content
            $queryBuilder->where('is_published', true)
                ->where('published_at', '<=', now());

            // Handle SoftDeletes if added in the future
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(Article::class))) {
                $queryBuilder->whereNull('deleted_at');
            }

            // Exclude current article
            if ($excludeId) {
                $queryBuilder->where('id', '!=', $excludeId);
            }

            if ($queryParam !== '') {
                // Vietnamese Accent Insensitive search via slug matching
                $slugQuery = Str::slug($queryParam);

                $queryBuilder->where(function ($sub) use ($queryParam, $slugQuery) {
                    $sub->where('title', 'like', "%{$queryParam}%")
                        ->orWhere('slug', 'like', "%{$slugQuery}%");
                });

                $queryBuilder->orderBy('id', 'desc')->limit(50);
            } else {
                // If query is empty, return 10 latest articles
                $queryBuilder->orderBy('published_at', 'desc')->limit(10);
            }

            $articles = $queryBuilder->get();

            return $articles->map(function ($article) {
                // Generate path relative URL
                $path = parse_url($article->public_url, PHP_URL_PATH);
                $url = $path ?: '/'.ltrim($article->slug.'.html', '/');

                return [
                    'id' => $article->id,
                    'type' => 'article',
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'url' => $url,
                    'absolute_url' => $article->public_url,
                ];
            })->toArray();
        });

        return response()->json($results);
    }
}
