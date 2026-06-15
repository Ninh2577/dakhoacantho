<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SchemaBuilder
{
    /**
     * Build the JSON-LD @graph structure from input options.
     */
    public static function build(array $options): array
    {
        $siteUrl = url('/');
        $logoUrl = asset('images/doctor.webp');

        // Ensure canonical url is absolute
        $currentUrl = $options['url'] ?? request()->url();
        if (!preg_match('/^https?:\/\//', $currentUrl)) {
            $currentUrl = url($currentUrl);
        }

        // 1. Organization / MedicalClinic / LocalBusiness combined node
        $organization = [
            '@context' => 'https://schema.org',
            '@type' => ['Organization', 'MedicalClinic', 'LocalBusiness'],
            '@id' => $siteUrl . '/#organization',
            'name' => 'Phòng Khám Đa Khoa Gia Phước',
            'alternateName' => 'Đa Khoa Gia Phước',
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ],
            'image' => $logoUrl,
            'telephone' => '+84966332352',
            'email' => 'info@dakhoagiaphuoc.vn',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '57 Hùng Vương',
                'addressLocality' => 'Ninh Kiều',
                'addressRegion' => 'Cần Thơ',
                'addressCountry' => 'VN',
            ],
            'openingHoursSpecification' => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => [
                    'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
                ],
                'opens' => '07:30',
                'closes' => '20:00',
            ],
            'areaServed' => 'Cần Thơ',
        ];

        // 2. WebSite Node
        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => $siteUrl . '/#website',
            'url' => $siteUrl,
            'name' => 'Phòng Khám Đa Khoa Gia Phước',
            'publisher' => [
                '@id' => $siteUrl . '/#organization',
            ],
        ];

        // Add potential SearchAction only if route exists
        if (Route::has('search')) {
            $website['potentialAction'] = [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/tim-kiem') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ];
        }

        // 3. WebPage Node (ContactPage, CollectionPage, SearchResultsPage, or WebPage)
        $pageTypeMapping = [
            'contact' => 'ContactPage',
            'category' => 'CollectionPage',
            'category archive' => 'CollectionPage',
            'search' => 'SearchResultsPage',
            'search results' => 'SearchResultsPage',
        ];
        $mappedPageType = $pageTypeMapping[strtolower($options['pageType'] ?? '')] ?? 'WebPage';

        $webpage = [
            '@context' => 'https://schema.org',
            '@type' => $mappedPageType,
            '@id' => $currentUrl . '#webpage',
            'url' => $currentUrl,
            'name' => $options['title'] ?? 'Phòng Khám Đa Khoa Gia Phước',
            'description' => $options['description'] ?? '',
            'isPartOf' => [
                '@id' => $siteUrl . '/#website',
            ],
            'about' => [
                '@id' => $siteUrl . '/#organization',
            ],
            'publisher' => [
                '@id' => $siteUrl . '/#organization',
            ],
            'inLanguage' => 'vi-VN',
        ];

        if (!empty($options['breadcrumbs'])) {
            $webpage['breadcrumb'] = [
                '@id' => $currentUrl . '#breadcrumb',
            ];
        }

        // 4. BreadcrumbList Node
        $breadcrumbList = null;
        if (!empty($options['breadcrumbs'])) {
            $itemListElement = [];
            foreach ($options['breadcrumbs'] as $index => $crumb) {
                if (empty($crumb['name']) || empty($crumb['url'])) {
                    continue;
                }
                $crumbUrl = $crumb['url'];
                if (!preg_match('/^https?:\/\//', $crumbUrl)) {
                    $crumbUrl = url($crumbUrl);
                }
                $itemListElement[] = [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $crumb['name'],
                    'item' => $crumbUrl,
                ];
            }

            if (!empty($itemListElement)) {
                $breadcrumbList = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    '@id' => $currentUrl . '#breadcrumb',
                    'itemListElement' => $itemListElement,
                ];
            }
        }

        // 5. Article Node (Article/BlogPosting/MedicalWebPage, based on schema_type)
        $blogPosting = null;
        $pageTypeLower = strtolower($options['pageType'] ?? '');
        $schemaType    = $options['schemaType'] ?? ($options['article']->schema_type ?? 'Article');

        if (($pageTypeLower === 'article' || $pageTypeLower === 'article detail')
            && !empty($options['article'])
            && strtolower((string) $schemaType) !== 'none'
        ) {
            $article = $options['article'];
            $articleUrl = $article->public_url;

            $pubDate = $options['publishedAt'] ?? $article->published_at ?? $article->created_at;
            $modDate = $options['modifiedAt']  ?? $article->updated_at;

            if ($pubDate instanceof \DateTimeInterface) {
                $pubDate = $pubDate->format('c'); // ISO 8601
            }
            if ($modDate instanceof \DateTimeInterface) {
                $modDate = $modDate->format('c'); // ISO 8601
            }

            // Image fallback
            $articleImage = $options['image'];
            if (empty($articleImage)) {
                $imagePath = $article->featured_image ?: $article->thumbnail_image;
                $articleImage = $imagePath ? asset('storage/' . $imagePath) : $logoUrl;
            }

            // Normalize schema type
            $allowedTypes = ['Article', 'BlogPosting', 'MedicalWebPage'];
            $resolvedType = in_array($schemaType, $allowedTypes) ? $schemaType : 'Article';

            $blogPosting = [
                '@context'       => 'https://schema.org',
                '@type'          => $resolvedType,
                '@id'            => $articleUrl . '#article',
                'mainEntityOfPage' => [
                    '@id' => $articleUrl . '#webpage',
                ],
                'headline'       => $article->title,
                'description'    => $options['description'] ?? $article->meta_description ?? Str::limit(strip_tags($article->content), 150),
                'image'          => $articleImage,
                'datePublished'  => $pubDate,
                'dateModified'   => $modDate,
                'author'         => [
                    '@type' => ($article->author || stripos($article->author ?: '', 'BS.') !== false || stripos($article->author ?: '', 'Bác sĩ') !== false || in_array($article->category?->slug, ['nam-khoa', 'phu-khoa'])) ? 'Person' : 'Organization',
                    'name'  => $article->author ?: match ($article->category?->slug) {
                        'nam-khoa' => 'BS. Nguyễn Văn An',
                        'phu-khoa' => 'BS. Trần Thị Mai',
                        default    => 'Ban Biên Tập - Phòng Khám Đa Khoa Gia Phước',
                    },
                    'url'   => $siteUrl,
                ],
                'publisher'      => [
                    '@id' => $siteUrl . '/#organization',
                ],
                'inLanguage'     => 'vi-VN',
            ];

            if ($article->category) {
                $blogPosting['articleSection'] = $article->category->name;
            }

            // MedicalWebPage extra fields
            if ($resolvedType === 'MedicalWebPage') {
                $blogPosting['medicalAudience'] = [
                    '@type' => 'MedicalAudience',
                    'audienceType' => 'Patient',
                ];
                $blogPosting['about'] = [
                    '@id' => $siteUrl . '/#organization',
                ];
            }
        }

        // 6. FAQPage Node
        $faqPage = null;
        if (!empty($options['faqItems'])) {
            $mainEntity = [];
            foreach ($options['faqItems'] as $faq) {
                $q = self::cleanText($faq['q'] ?? $faq['question'] ?? '');
                $a = self::cleanText($faq['a'] ?? $faq['answer'] ?? '');
                if (empty($q) || empty($a)) {
                    continue;
                }
                $mainEntity[] = [
                    '@type' => 'Question',
                    'name' => $q,
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $a,
                    ],
                ];
            }

            if (!empty($mainEntity)) {
                $faqPage = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    '@id' => $currentUrl . '#faq',
                    'mainEntity' => $mainEntity,
                ];
            }
        }

        // Build @graph output
        $graph = [];
        $graph[] = self::cleanArrayRecursive($organization);
        $graph[] = self::cleanArrayRecursive($website);

        if ($webpage) {
            $graph[] = self::cleanArrayRecursive($webpage);
        }
        if ($breadcrumbList) {
            $graph[] = self::cleanArrayRecursive($breadcrumbList);
        }
        if ($blogPosting) {
            $graph[] = self::cleanArrayRecursive($blogPosting);
        }
        if ($faqPage) {
            $graph[] = self::cleanArrayRecursive($faqPage);
        }

        // Remove any null or empty items from the graph array itself
        $graph = array_filter($graph);

        // Remove context from child elements to avoid duplicate context declarations inside graph
        foreach ($graph as &$item) {
            if (isset($item['@context'])) {
                unset($item['@context']);
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values($graph),
        ];
    }

    /**
     * Recursively remove null, empty strings, empty arrays, and nested empty structures.
     */
    public static function cleanArrayRecursive($array)
    {
        if (!is_array($array)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned = self::cleanArrayRecursive($value);
                if ($cleaned === null || $cleaned === '' || (is_array($cleaned) && empty($cleaned))) {
                    unset($array[$key]);
                } else {
                    $array[$key] = $cleaned;
                }
            } else {
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    unset($array[$key]);
                }
            }
        }

        return empty($array) ? null : $array;
    }

    /**
     * Clean and safely strip tags and whitespace from HTML string.
     */
    private static function cleanText(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
