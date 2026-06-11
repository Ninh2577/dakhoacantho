<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\UrlRedirect;
use App\Services\UrlRoutingService;
use Illuminate\Http\Request;

class DynamicRouterController extends Controller
{
    /**
     * Resolve the dynamic path and dispatch to the correct controller or issue a redirect.
     */
    public function resolve(Request $request, string $path = '')
    {
        $routingService = app(UrlRoutingService::class);
        $path = $routingService->normalizePath($path);

        // When path is empty (root URL via subdirectory XAMPP/cPanel setup),
        // delegate to the home page controller instead of aborting.
        if (empty($path)) {
            return app(PageController::class)->home();
        }

        // Immediately bypass reserved system paths
        if ($routingService->isReservedPath($path)) {
            abort(404);
        }

        // 1. Resolve SEO 301 Redirects with loop protection (up to 3 hops)
        $redirect = UrlRedirect::where('old_path', $path)->where('is_active', true)->first();
        if ($redirect) {
            $resolvedPath = $redirect->new_path;
            $hops = 0;
            
            while ($nextRedirect = UrlRedirect::where('old_path', $resolvedPath)->where('is_active', true)->first()) {
                $hops++;
                if ($hops >= 3) {
                    // Prevent circular loop, break
                    break;
                }
                $resolvedPath = $nextRedirect->new_path;
            }

            return redirect()->to(url($resolvedPath), $redirect->status_code ?: 301);
        }

        // 2. Resolve Category Paths
        $category = Category::where('url_path', $path)->first();
        if ($category) {
            return app(CategoryController::class)->showResolved($category);
        }

        // 3. Resolve Article Paths
        $article = Article::where('url_path', $path)->where('is_published', true)->first();
        if ($article) {
            return app(ArticleController::class)->showResolved($article);
        }

        // 4. Default Fallback
        abort(404);
    }
}
