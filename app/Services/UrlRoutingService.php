<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlRedirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class UrlRoutingService
{
    // Allowed patterns
    protected array $allowedArticlePatterns = [
        '{slug}',
        '{slug}.html',
        'category/{slug}',
        'category/{slug}.html',
        'category/{category}/{slug}',
        'category/{category}/{slug}.html',
        'category/{categories}/{slug}',
        'category/{categories}/{slug}.html',
        '{category}/{slug}',
        '{category}/{slug}.html',
        '{categories}/{slug}',
        '{categories}/{slug}.html',
    ];

    protected array $allowedCategoryPatterns = [
        'category/{slug}',
        'category/{categories}',
        '{slug}',
        '{categories}',
    ];

    // Reserved paths that should never be hijacked
    protected array $reservedPaths = [
        'admin',
        'login',
        'logout',
        'register',
        'livewire',
        'filament',
        'build',
        'storage',
        'vendor',
        'api',
        '_debugbar',
        'favicon.ico',
        'robots.txt',
        'sitemap.xml',
        'sitemap',
        'lien-he',
        'tim-kiem',
        'tu-van',
        'chuyen-khoa',
        'chinh-sach-bao-mat',
        'dieu-khoan-su-dung',
        'articles',
        'categories',
        'wp-content',
    ];

    /**
     * Normalize paths safely.
     */
    public function normalizePath(?string $path): string
    {
        // Treat null as empty string — callers will handle redirect/404 logic
        if ($path === null) {
            return '';
        }

        // 1. Remove domain or host if full URL is pasted
        if (preg_match('/^(?:https?:\/\/)?(?:[a-z0-9\-]+\.)+[a-z0-9\-]+(?::\d+)?(.*)$/i', $path, $matches)) {
            $path = $matches[1];
        }

        // 2. Remove query strings
        if (strpos($path, '?') !== false) {
            $path = explode('?', $path)[0];
        }

        // 3. Convert to lowercase but preserve unicode/Vietnamese characters
        $path = mb_strtolower(trim($path));

        // 4. Resolve multiple slashes
        $path = preg_replace('/\/+/', '/', $path);

        // 5. Trim leading and trailing slashes
        $path = trim($path, '/');

        // 6. Loop protection & traversals: reject unsafe parent directories
        if (strpos($path, '..') !== false) {
            return '';
        }

        // 7. Strip completely unsafe characters (e.g. quotes, brackets, spaces, query symbols)
        // Keeps letters, numbers, hyphens, slashes, dots, underscores, and Vietnamese unicode characters
        $path = preg_replace('/[^a-z0-9\-\/\._\x{00C0}-\x{017F}\x{1EA0}-\x{1EF9}]/u', '', $path);

        // 8. Clean trailing slashes/periods near extensions
        // e.g. .html/ -> .html
        if (str_ends_with($path, '.html/')) {
            $path = substr($path, 0, -1);
        }
        
        // e.g. .html.html -> .html
        $path = preg_replace('/(\.html)+/i', '.html', $path);

        return $path;
    }

    /**
     * Validate article pattern.
     */
    public function validateArticlePattern(string $pattern): bool
    {
        return in_array(trim($pattern), $this->allowedArticlePatterns);
    }

    /**
     * Validate category pattern.
     */
    public function validateCategoryPattern(string $pattern): bool
    {
        return in_array(trim($pattern), $this->allowedCategoryPatterns);
    }

    /**
     * Compile Article path.
     */
    public function compileArticlePath(Article $article, string $pattern): string
    {
        $slug = $article->slug;
        
        // Resolve Category placeholders
        $categorySlug = 'khong-phan-loai';
        $categoriesSlug = 'khong-phan-loai';

        if ($article->category) {
            $categorySlug = $article->category->slug ?: 'khong-phan-loai';
            $categoriesSlug = $article->category->full_path ?: 'khong-phan-loai';
        }

        $compiled = str_replace(
            ['{slug}', '{category}', '{categories}'],
            [$slug, $categorySlug, $categoriesSlug],
            $pattern
        );

        return $this->normalizePath($compiled);
    }

    /**
     * Compile Category path.
     */
    public function compileCategoryPath(Category $category, string $pattern): string
    {
        $slug = $category->slug;
        $categories = $category->full_path ?: $slug;

        $compiled = str_replace(
            ['{slug}', '{categories}'],
            [$slug, $categories],
            $pattern
        );

        return $this->normalizePath($compiled);
    }

    /**
     * Register a redirect safely with loop check and cycle breaking.
     */
    public function registerRedirect(string $oldPath, string $newPath, ?string $targetType = null, ?int $targetId = null): void
    {
        $oldPath = $this->normalizePath($oldPath);
        $newPath = $this->normalizePath($newPath);

        if (empty($oldPath) || empty($newPath) || $oldPath === $newPath) {
            return;
        }

        // Prevent redirecting system paths
        if ($this->isReservedPath($oldPath)) {
            return;
        }

        // Redirect Loop Check: check if newPath redirects back to oldPath (directly or transitively)
        if ($this->detectRedirectLoop($newPath, $oldPath)) {
            Log::warning("Redirect loop detected: {$oldPath} -> {$newPath}. Skipping redirect generation.");
            return;
        }

        // Clean up: If oldPath already exists in redirect records, update it
        $existing = UrlRedirect::where('old_path', $oldPath)->first();
        if ($existing) {
            $existing->update([
                'new_path' => $newPath,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'is_active' => true,
            ]);
        } else {
            UrlRedirect::create([
                'old_path' => $oldPath,
                'new_path' => $newPath,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'status_code' => 301,
                'is_active' => true,
            ]);
        }

        // Clean up inverse redirects: If there is a redirect pointing from newPath to oldPath, remove it to prevent a loop
        UrlRedirect::where('old_path', $newPath)->where('new_path', $oldPath)->delete();
    }

    /**
     * Detect loops up to 3 hops.
     */
    protected function detectRedirectLoop(string $startPath, string $targetPath): bool
    {
        $current = $startPath;
        for ($i = 0; $i < 3; $i++) {
            $redirect = UrlRedirect::where('old_path', $current)->where('is_active', true)->first();
            if (!$redirect) {
                return false;
            }
            if ($redirect->new_path === $targetPath) {
                return true;
            }
            $current = $redirect->new_path;
        }
        return false;
    }

    /**
     * Check if a path is reserved or static.
     */
    public function isReservedPath(string $path): bool
    {
        $firstSegment = explode('/', $path)[0];
        return in_array($firstSegment, $this->reservedPaths);
    }

    /**
     * Dry-run compile and check for conflicts globally.
     */
    public function checkConflicts(string $articlePattern, string $categoryPattern): array
    {
        $conflicts = [];
        $articlePaths = [];
        $categoryPaths = [];

        // 1. Category compilation dry-run
        $categories = Category::all();
        foreach ($categories as $cat) {
            $path = $this->compileCategoryPath($cat, $categoryPattern);
            if (empty($path)) {
                $conflicts[] = [
                    'type' => 'empty_category_path',
                    'message' => "Danh mục ID {$cat->id} ({$cat->name}) có URL trống.",
                    'target_type' => 'category',
                    'target_id' => $cat->id,
                ];
                continue;
            }

            // Reserved path check
            if ($this->isReservedPath($path)) {
                $conflicts[] = [
                    'type' => 'reserved_path_conflict',
                    'message' => "Đường dẫn danh mục '{$path}' trùng với trang hệ thống.",
                    'target_type' => 'category',
                    'target_id' => $cat->id,
                ];
            }

            // Duplicate check within categories
            if (isset($categoryPaths[$path])) {
                $conflicts[] = [
                    'type' => 'duplicate_category_path',
                    'message' => "Trùng lặp đường dẫn danh mục: '{$path}' giữa các danh mục ID {$cat->id} và ID {$categoryPaths[$path]}.",
                    'target_type' => 'category',
                    'target_id' => $cat->id,
                ];
            } else {
                $categoryPaths[$path] = $cat->id;
            }
        }

        // 2. Article compilation dry-run
        $articles = Article::with('category')->get();
        foreach ($articles as $art) {
            $path = $this->compileArticlePath($art, $articlePattern);
            if (empty($path)) {
                $conflicts[] = [
                    'type' => 'empty_article_path',
                    'message' => "Bài viết ID {$art->id} ({$art->title}) có URL trống.",
                    'target_type' => 'article',
                    'target_id' => $art->id,
                ];
                continue;
            }

            // Reserved path check
            if ($this->isReservedPath($path)) {
                $conflicts[] = [
                    'type' => 'reserved_path_conflict',
                    'message' => "Đường dẫn bài viết '{$path}' trùng với trang hệ thống.",
                    'target_type' => 'article',
                    'target_id' => $art->id,
                ];
            }

            // Duplicate check within articles
            if (isset($articlePaths[$path])) {
                $conflicts[] = [
                    'type' => 'duplicate_article_path',
                    'message' => "Trùng lặp đường dẫn bài viết: '{$path}' giữa các bài viết ID {$art->id} và ID {$articlePaths[$path]}.",
                    'target_type' => 'article',
                    'target_id' => $art->id,
                ];
            } else {
                $articlePaths[$path] = $art->id;
            }

            // Cross-table conflict check
            if (isset($categoryPaths[$path])) {
                $conflicts[] = [
                    'type' => 'cross_table_conflict',
                    'message' => "Xung đột chéo: Đường dẫn '{$path}' trùng giữa bài viết ID {$art->id} và danh mục ID {$categoryPaths[$path]}.",
                    'target_type' => 'article',
                    'target_id' => $art->id,
                ];
            }
        }

        return [
            'conflicts' => $conflicts,
            'conflict_count' => count($conflicts),
        ];
    }
}
