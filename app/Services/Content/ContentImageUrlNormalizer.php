<?php

namespace App\Services\Content;

class ContentImageUrlNormalizer
{
    /**
     * Normalize all local image upload URLs inside the content to the correct current environment absolute URL.
     */
    public function normalize(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Requirement 9: Lightweight quick check before regex parsing
        if (! str_contains($content, 'storage/uploads') &&
            ! str_contains($content, 'localhost') &&
            ! str_contains($content, 'public/storage') &&
            ! str_contains($content, 'blob:') &&
            ! str_contains($content, 'data:image')
        ) {
            return $content;
        }

        // Parse host from current app.url config to support dynamic domains
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $hostPattern = 'localhost|127\.0\.0\.1|app\.dakhoacantho\.com|'.preg_quote($host, '/');

        // Resolve absolute storage asset URL
        $assetUrl = asset('storage/uploads');

        // Requirement 5: Force HTTPS if APP_URL is configured as HTTPS to avoid mixed content
        if (str_starts_with(config('app.url'), 'https://')) {
            $assetUrl = str_replace('http://', 'https://', $assetUrl);
        }

        $assetUrl = rtrim($assetUrl, '/');

        // Regex pattern to match local storage upload paths inside quotes
        // Capture 1: Quote character (" or ')
        // Capture 2: Relative file path under storage/uploads
        $pattern = '/(["\'])(?:https?:\/\/(?:'.$hostPattern.'))?(?:\/dakhoacantho_web\/public|\/public)?\/?storage\/uploads\/([^"\'\s>]+)\1/i';

        return preg_replace_callback($pattern, function ($matches) use ($assetUrl) {
            $quote = $matches[1];
            $relativePath = $matches[2];

            return $quote.$assetUrl.'/'.$relativePath.$quote;
        }, $content);
    }
}
