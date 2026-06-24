<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate the sitemap XML.
     * 
     * Uses the current request's scheme+host+basePath as root URL,
     * so the sitemap is always correct regardless of subdomain or domain changes.
     */
    public function index_v2()
    {
        // Determine the clean root URL from current request (no script name)
        $currentRoot = rtrim(request()->root(), '/');
        $host = request()->getHost() ?: parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $cacheKey = "dakhoacantho:sitemap:entries:{$host}";

        // Cache the URL entries (with __ROOT__ placeholder) per host for 6 hours
        $entries = Cache::remember($cacheKey, now()->addHours(6), function () {
            $categories = Category::all();
            $articles = Article::with('category')->where('is_published', true)->get();

            $entries = [];

            // Homepage
            $entries[] = ['loc' => '__ROOT__/', 'changefreq' => 'daily', 'priority' => '1.0'];

            // Static landing pages
            $entries[] = ['loc' => '__ROOT__/lien-he', 'changefreq' => 'weekly', 'priority' => '0.8'];
            $entries[] = ['loc' => '__ROOT__/chuyen-khoa', 'changefreq' => 'weekly', 'priority' => '0.8'];
            $entries[] = ['loc' => '__ROOT__/chinh-sach-bao-mat', 'changefreq' => 'monthly', 'priority' => '0.5'];
            $entries[] = ['loc' => '__ROOT__/dieu-khoan-su-dung', 'changefreq' => 'monthly', 'priority' => '0.5'];

            // Categories
            foreach ($categories as $category) {
                $path = ltrim($category->url_path ?: $category->full_path, '/');
                $entries[] = ['loc' => '__ROOT__/' . $path, 'changefreq' => 'weekly', 'priority' => '0.8'];
            }

            // Articles
            foreach ($articles as $article) {
                $path = ltrim($article->url_path ?: $article->slug, '/');
                $entries[] = [
                    'loc'        => '__ROOT__/' . $path,
                    'lastmod'    => $article->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority'   => '0.6',
                ];
            }

            return $entries;
        });

        // Build the XML — swap __ROOT__ with the actual current request root
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($entries as $entry) {
            $loc = str_replace('__ROOT__', $currentRoot, $entry['loc']);
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
            if (!empty($entry['lastmod'])) {
                $xml .= "    <lastmod>" . htmlspecialchars($entry['lastmod'], ENT_XML1) . "</lastmod>\n";
            }
            $xml .= "    <changefreq>" . $entry['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $entry['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
