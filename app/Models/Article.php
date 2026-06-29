<?php

namespace App\Models;

use App\Services\UrlRoutingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail_image',
        'featured_image',
        'meta_title',
        'meta_description',
        'is_published',
        'published_at',
        'schema_type',
        'schema_json',
        'focus_keyword',
        'seo_slug',
        'canonical_url',
        'robots_index',
        'robots_follow',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'seo_score',
        'seo_checks',
    ];

    protected $casts = [
        'author_id' => 'integer',
        'is_published' => 'boolean',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'published_at' => 'datetime',
        'schema_json' => 'array',
        'seo_checks' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    protected static function booted()
    {
        static::saving(function ($article) {
            // Auto-set published_at on first publish
            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }
            // Do NOT reset published_at if already published

            // Sync thumbnail_image and featured_image safely
            if ($article->isDirty('featured_image')) {
                $article->thumbnail_image = $article->featured_image;
            } elseif ($article->isDirty('thumbnail_image')) {
                $article->featured_image = $article->thumbnail_image;
            } else {
                if (empty($article->featured_image) && ! empty($article->thumbnail_image)) {
                    $article->featured_image = $article->thumbnail_image;
                } elseif (empty($article->thumbnail_image) && ! empty($article->featured_image)) {
                    $article->thumbnail_image = $article->featured_image;
                }
            }

            $pattern = Setting::get('url_pattern_article') ?: '{slug}';
            $service = app(UrlRoutingService::class);
            $article->url_path = $service->compileArticlePath($article, $pattern);
        });

        static::saved(function ($article) {
            Cache::forget('home_latest_articles');
            Cache::forget("dakhoacantho:articles:related:{$article->id}");
            Cache::forget('dakhoacantho:sitemap:entries:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:entries:'.request()->getHost());
            }
            foreach (Category::all() as $cat) {
                Cache::forget("category_related_articles_{$cat->id}");
            }
        });

        static::deleted(function ($article) {
            Cache::forget('home_latest_articles');
            Cache::forget("dakhoacantho:articles:related:{$article->id}");
            Cache::forget('dakhoacantho:sitemap:entries:'.parse_url(config('app.url'), PHP_URL_HOST));
            if (request()->getHost()) {
                Cache::forget('dakhoacantho:sitemap:entries:'.request()->getHost());
            }
            foreach (Category::all() as $cat) {
                Cache::forget("category_related_articles_{$cat->id}");
            }
        });
    }

    public function getCategoryPathAttribute()
    {
        return $this->category ? $this->category->full_path : 'uncategorized';
    }

    public function getPublicUrlAttribute()
    {
        $path = $this->url_path ?: $this->slug;

        return url(ltrim($path, '/'));
    }

    public function getContentAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        // 0. Clean up accumulated duplicate domain prefixes from previous bugs
        $domain = rtrim(config('app.url'), '/');
        $escapedDomain = preg_quote($domain, '/');
        $value = preg_replace('/(' . $escapedDomain . ')+/i', $domain, $value);

        $requestDomain = rtrim(request()->getSchemeAndHttpHost(), '/');
        if ($requestDomain !== $domain) {
            $escapedRequestDomain = preg_quote($requestDomain, '/');
            $value = preg_replace('/(' . $escapedRequestDomain . ')+/i', $requestDomain, $value);
        }

        // 1. Resolve relative /storage/uploads to dynamic asset path safely using regex
        $assetUrl = rtrim(asset('storage/uploads'), '/');
        $value = preg_replace('/(src|href)="\/storage\/uploads/i', '$1="' . $assetUrl, $value);
        $value = preg_replace('/(src|href)="storage\/uploads/i', '$1="' . $assetUrl, $value);

        // 2. Resolve broken image URLs in content dynamically
        return preg_replace_callback('/(<img[^>]+src=")([^"]+)("[^>]*>)/i', function ($m) {
            $resolvedUrl = $this->resolveLocalImageUrl($m[2]);
            return $m[1] . $resolvedUrl . $m[3];
        }, $value);
    }

    private function resolveLocalImageUrl($url)
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        
        $prefixes = [
            '/dakhoacantho_web/public/',
            '/benhtridkgp/public/',
            '/' . basename(base_path()) . '/public/',
            '/' . basename(base_path()) . '/',
        ];
        foreach ($prefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                $path = substr($path, strlen($prefix));
                break;
            }
        }
        $path = ltrim($path, '/');
        
        if (strpos($path, 'storage/uploads/') !== 0) {
            return $url;
        }
        
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return $url;
        }
        
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            return $url;
        }
        
        $filename = basename($fullPath);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $basenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        
        $altExts = ($ext === 'png') ? ['jpg', 'jpeg', 'webp', 'gif'] : ['png', 'webp', 'jpeg', 'jpg'];
        
        $checkAndBuildUrl = function($name, $ext) use ($dir) {
            $testPath = $dir . '/' . $name . '.' . $ext;
            if (file_exists($testPath)) {
                $relPath = str_replace(public_path(), '', $testPath);
                $relPath = str_replace('\\', '/', $relPath);
                $relPath = ltrim($relPath, '/');
                return asset($relPath);
            }
            return null;
        };
        
        // 1. Try alternate extensions
        foreach ($altExts as $altExt) {
            if ($res = $checkAndBuildUrl($basenameWithoutExt, $altExt)) {
                return $res;
            }
        }
        
        // 2. Try removing dimension suffix (e.g., -300x199)
        $cleanName = preg_replace('/-(\d+)x(\d+)$/', '', $basenameWithoutExt);
        if ($cleanName !== $basenameWithoutExt) {
            foreach (array_merge([$ext], $altExts) as $testExt) {
                if ($res = $checkAndBuildUrl($cleanName, $testExt)) {
                    return $res;
                }
            }
        }
        
        // 3. Try removing copy suffix (e.g. -1, -2) and dimensions
        $superCleanName = preg_replace('/-\d+$/', '', $cleanName);
        $superCleanName = preg_replace('/-(\d+)x(\d+)$/', '', $superCleanName);
        $superCleanName = preg_replace('/-\d+$/', '', $superCleanName);
        if ($superCleanName !== $cleanName) {
            foreach (array_merge([$ext], $altExts) as $testExt) {
                if ($res = $checkAndBuildUrl($superCleanName, $testExt)) {
                    return $res;
                }
            }
        }
        
        // 4. Try case-insensitive prefix match in the directory
        $escapedName = glob($dir . '/*');
        if ($escapedName) {
            $normalizedSearch = mb_strtolower($this->removeAccents($basenameWithoutExt));
            $normalizedSearch = preg_replace('/[^a-z0-9]/', '', $normalizedSearch);
            
            $cleanSearch = preg_replace('/(300x200|150x150|800x445|300x199|300x99|300x300|300x211|300x154|\d+x\d+)$/', '', $normalizedSearch);
            $cleanSearch = preg_replace('/\d+$/', '', $cleanSearch);
            
            foreach ($escapedName as $file) {
                $testFilename = basename($file);
                $testName = pathinfo($testFilename, PATHINFO_FILENAME);
                $normalizedTest = mb_strtolower($this->removeAccents($testName));
                $normalizedTest = preg_replace('/[^a-z0-9]/', '', $normalizedTest);
                $cleanTest = preg_replace('/(300x200|150x150|800x445|300x199|300x99|300x300|300x211|300x154|\d+x\d+)$/', '', $normalizedTest);
                $cleanTest = preg_replace('/\d+$/', '', $cleanTest);
                
                if ($cleanTest === $cleanSearch && strlen($cleanSearch) > 3) {
                    $relPath = str_replace(public_path(), '', $file);
                    $relPath = str_replace('\\', '/', $relPath);
                    $relPath = ltrim($relPath, '/');
                    return asset($relPath);
                }
            }
        }
        
        return $url;
    }

    private function removeAccents($str)
    {
        $accents = [
            'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ','À','Á','Ạ','Ả','Ã','Â','Ầ','Ấ','Ậ','Ẩ','Ẫ','Ă','Ằ','Ắ','Ặ','Ẳ','Ẵ',
            'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ','È','É','Ẹ','Ẻ','Ẽ','Ê','Ề','Ế','Ệ','Ể','Ễ',
            'ì','í','ị','ỉ','ĩ','Ì','Í','Ị','Ỉ','Ĩ',
            'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ','Ò','Ó','Ọ','Ỏ','Õ','Ô','Ồ','Ố','Ộ','Ổ','Ỗ','Ơ','Ờ','Ớ','Ợ','Ở','Ỡ',
            'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ','Ù','Ú','Ụ','Ủ','Ũ','Ư','Ừ','Ứ','Ự','Ử','Ữ',
            'ỳ','ý','ỵ','ỷ','ỹ','Ỳ','Ý','Ỵ','Ỷ','Ỹ',
            'đ','Đ'
        ];
        $replacements = [
            'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A','A',
            'e','e','e','e','e','e','e','e','e','e','e','E','E','E','E','E','E','E','E','E','E','E',
            'i','i','i','i','i','I','I','I','I','I',
            'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O','O',
            'u','u','u','u','u','u','u','u','u','u','u','U','U','U','U','U','U','U','U','U','U','U',
            'y','y','y','y','y','Y','Y','Y','Y','Y',
            'd','D'
        ];
        return str_replace($accents, $replacements, $str);
    }
}
