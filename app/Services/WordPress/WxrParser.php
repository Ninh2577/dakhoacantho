<?php

namespace App\Services\WordPress;

use DOMDocument;
use Exception;
use Illuminate\Support\Facades\Log;
use XMLReader;

class WxrParser
{
    /**
     * Dynamically detect namespaces in the WXR file.
     */
    public static function detectNamespaces(string $filePath): array
    {
        $namespaces = [];
        $reader = new XMLReader;

        if ($reader->open($filePath)) {
            $count = 0;
            // Scan first 10 nodes to detect namespaces dynamically from DOM
            while ($reader->read() && $count < 10) {
                if ($reader->nodeType === XMLReader::ELEMENT) {
                    try {
                        $node = $reader->expand();
                        if ($node) {
                            $dom = new DOMDocument;
                            $dom->appendChild($dom->importNode($node, true));
                            $sxml = simplexml_import_dom($dom);
                            if ($sxml) {
                                $namespaces = array_merge($namespaces, $sxml->getNamespaces(true));
                            }
                        }
                    } catch (\Throwable $e) {
                        // Ignore parse errors on incomplete structures
                    }
                    $count++;
                }
            }
            $reader->close();
        }

        // Standard WXR Namespace fallbacks if they are not detected
        $defaults = [
            'wp' => 'http://wordpress.org/export/1.2/',
            'content' => 'http://purl.org/rss/1.0/modules/content/',
            'excerpt' => 'http://wordpress.org/export/1.2/excerpt/',
            'dc' => 'http://purl.org/dc/elements/1.1/',
        ];

        foreach ($defaults as $prefix => $uri) {
            if (empty($namespaces[$prefix])) {
                // Check if a similar namespace URI exists under a versioned path (e.g. export/1.1/ or export/1.0/)
                $found = false;
                foreach ($namespaces as $k => $v) {
                    if (str_contains($v, 'wordpress.org/export/')) {
                        $namespaces['wp'] = $v;
                        $found = true;
                        break;
                    }
                }
                if (! $found || $prefix !== 'wp') {
                    $namespaces[$prefix] = $uri;
                }
            }
        }

        return $namespaces;
    }

    /**
     * Parse categories from WXR file, sorting them by parent-child tree depth.
     */
    public function parseCategories(string $filePath, array $ns): array
    {
        $categories = [];
        $reader = new XMLReader;

        if (! $reader->open($filePath)) {
            throw new Exception('Không thể mở tệp XML để đọc danh mục: '.$filePath);
        }

        $wpUri = $ns['wp'] ?? 'http://wordpress.org/export/1.2/';

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'wp:category') {
                try {
                    $node = $reader->expand();
                    $dom = new DOMDocument;
                    $dom->appendChild($dom->importNode($node, true));
                    $sxml = simplexml_import_dom($dom);

                    if ($sxml !== false) {
                        $wpNs = $sxml->children('wp', true);
                        if (count($wpNs) === 0) {
                            $wpNs = $sxml->children($wpUri);
                        }
                        $slug = (string) $wpNs->category_nicename;
                        $name = (string) $wpNs->cat_name;
                        $parentSlug = (string) $wpNs->category_parent;
                        $description = (string) $wpNs->category_description;
                        $termId = (int) $wpNs->term_id;

                        if ($slug) {
                            $categories[$slug] = [
                                'term_id' => $termId,
                                'slug' => $slug,
                                'name' => $name ?: $slug,
                                'parent_slug' => $parentSlug,
                                'description' => $description,
                                'depth' => -1,
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Category parsing error: '.$e->getMessage());
                }
            }
        }
        $reader->close();

        // Calculate depth recursively
        $computeDepth = function ($slug) use (&$categories, &$computeDepth) {
            if (! isset($categories[$slug])) {
                return 0;
            }
            if ($categories[$slug]['depth'] !== -1) {
                return $categories[$slug]['depth'];
            }
            $parentSlug = $categories[$slug]['parent_slug'];
            if (empty($parentSlug) || ! isset($categories[$parentSlug])) {
                $categories[$slug]['depth'] = 0;

                return 0;
            }
            $parentDepth = $computeDepth($parentSlug);
            $categories[$slug]['depth'] = 1 + $parentDepth;

            return $categories[$slug]['depth'];
        };

        foreach (array_keys($categories) as $slug) {
            $computeDepth($slug);
        }

        // Sort: Parent categories (depth 0) first, children second
        uasort($categories, function ($a, $b) {
            return $a['depth'] <=> $b['depth'];
        });

        return $categories;
    }

    /**
     * Parse attachments from WXR file, building an attachment map in memory.
     */
    public function parseAttachments(string $filePath, array $ns, ?string &$warningMsg = null): array
    {
        $attachments = [];
        $reader = new XMLReader;

        if (! $reader->open($filePath)) {
            throw new Exception('Không thể mở tệp XML để đọc tệp đính kèm: '.$filePath);
        }

        $wpUri = $ns['wp'] ?? 'http://wordpress.org/export/1.2/';

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'item') {
                try {
                    $node = $reader->expand();
                    $dom = new DOMDocument;
                    $dom->appendChild($dom->importNode($node, true));
                    $sxml = simplexml_import_dom($dom);

                    if ($sxml !== false) {
                        $wpNs = $sxml->children('wp', true);
                        if (count($wpNs) === 0) {
                            $wpNs = $sxml->children($wpUri);
                        }
                        $postType = (string) $wpNs->post_type;

                        if ($postType === 'attachment') {
                            $postId = (int) $wpNs->post_id;
                            $attachmentUrl = (string) $wpNs->attachment_url;

                            // Find relative attached file path in postmeta
                            $attachedFile = '';
                            foreach ($wpNs->postmeta as $meta) {
                                if ((string) $meta->meta_key === '_wp_attached_file') {
                                    $attachedFile = (string) $meta->meta_value;
                                    break;
                                }
                            }

                            if ($postId) {
                                $attachments[$postId] = [
                                    'url' => $attachmentUrl,
                                    'file' => $attachedFile ?: basename($attachmentUrl),
                                ];
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Skip errors on single elements
                }
            }
        }
        $reader->close();

        // Memory usage safety warning
        if (count($attachments) > 20000) {
            $warningMsg = 'XML chứa lượng lớn tệp đính kèm ('.count($attachments).'). Hãy theo dõi lượng RAM tiêu thụ.';
        }

        return $attachments;
    }

    /**
     * Stream items (posts, pages) one by one using a PHP Generator.
     */
    public function streamItems(string $filePath, array $ns)
    {
        $reader = new XMLReader;

        if (! $reader->open($filePath)) {
            throw new Exception('Không thể mở tệp XML để đọc bài viết: '.$filePath);
        }

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->name === 'item') {
                try {
                    $node = $reader->expand();
                    $dom = new DOMDocument;
                    $dom->appendChild($dom->importNode($node, true));
                    $sxml = simplexml_import_dom($dom);

                    if ($sxml !== false) {
                        yield $sxml;
                    }
                } catch (\Throwable $e) {
                    // Yield null/skip failed XML blocks
                }
            }
        }
        $reader->close();
    }
}
