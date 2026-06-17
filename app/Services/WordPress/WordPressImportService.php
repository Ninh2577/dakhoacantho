<?php

namespace App\Services\WordPress;

use App\Models\Article;
use App\Models\Category;
use App\Models\WordPressImportBatch;
use App\Models\WordPressImportLog;
use App\Services\ArticleSeoAnalyzerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WordPressImportService
{
    protected array $omittedFields = [];

    /**
     * Run safe timestamped database backup.
     */
    public function runBackup(?string &$backupFile = null): bool
    {
        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $backupFile = $backupDir."/dakhoacantho_web_before_import_{$timestamp}.sql";

        // Determine mysqldump path
        $mysqlDumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (! File::exists($mysqlDumpPath)) {
            $mysqlDumpPath = 'mysqldump'; // Fallback to PATH environment
        }

        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'dakhoacantho_web');
        $dbUser = config('database.connections.mysql.username', 'root');
        $dbPass = config('database.connections.mysql.password', '');

        // Handle empty password on Windows to avoid hanging
        if ($dbPass === '') {
            $passwordOption = '';
        } else {
            $passwordOption = '--password='.escapeshellarg($dbPass);
        }

        $command = sprintf(
            '"%s" -h %s -P %s -u %s %s %s > "%s"',
            $mysqlDumpPath,
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            $passwordOption,
            escapeshellarg($dbName),
            $backupFile
        );

        exec($command, $output, $resultCode);

        return $resultCode === 0;
    }

    /**
     * Import a single category.
     */
    public function importCategory(array $wpCat, ?int $parentTargetId, WordPressImportBatch $batch, array &$slugToIdMap): int
    {
        $slug = $wpCat['slug'];
        $name = $wpCat['name'];
        $description = $wpCat['description'];
        $termId = $wpCat['term_id'];

        $rootParentId = Category::whereNull('parent_id')->exists() ? null : -1;
        $resolvedParentId = $parentTargetId ?? $rootParentId;

        // 1. Match by slug
        $category = Category::where('slug', $slug)->first();

        // 2. Match by normalized name
        if (! $category) {
            $normalizedName = $this->normalizeString($name);
            $category = Category::all()->first(function ($cat) use ($normalizedName) {
                return $this->normalizeString($cat->name) === $normalizedName;
            });
        }

        if ($category) {
            if ($batch->dry_run) {
                $this->logAction($batch->id, $termId, 'category', $slug, $name, 'dry_run', 'success', 'Sẽ khớp danh mục đã có: '.$category->name);
            } else {
                $this->logAction($batch->id, $termId, 'category', $slug, $name, 'skipped', 'success', 'Khớp với danh mục có sẵn: '.$category->name, ['target_id' => $category->id]);
            }

            return $category->id;
        }

        // Create if not dry run
        if ($batch->dry_run) {
            $this->logAction($batch->id, $termId, 'category', $slug, $name, 'dry_run', 'success', 'Sẽ tạo danh mục mới: '.$name);

            return 9999 + $termId;
        }

        // Generate unique slug in case of collision
        $originalSlug = $slug;
        $suffix = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$suffix;
            $suffix++;
        }

        $newCategory = Category::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'parent_id' => $resolvedParentId,
            'order' => 1,
        ]);

        $this->logAction($batch->id, $termId, 'category', $slug, $name, 'imported', 'success', 'Đã tạo danh mục mới', ['target_id' => $newCategory->id]);

        return $newCategory->id;
    }

    /**
     * Import a single post/page item.
     */
    public function importItem(\SimpleXMLElement $item, array $ns, WordPressImportBatch $batch, array $slugToIdMap, array $attachmentMap)
    {
        $wpUri = $ns['wp'] ?? 'http://wordpress.org/export/1.2/';
        $contentUri = $ns['content'] ?? 'http://purl.org/rss/1.0/modules/content/';
        $excerptUri = $ns['excerpt'] ?? 'http://wordpress.org/export/1.2/excerpt/';
        $dcUri = $ns['dc'] ?? 'http://purl.org/dc/elements/1.1/';

        $wpNs = $item->children($wpUri);
        $contentNs = $item->children($contentUri);
        $excerptNs = $item->children($excerptUri);
        $dcNs = $item->children($dcUri);

        $postId = (int) $wpNs->post_id;
        $postType = (string) $wpNs->post_type;
        $title = (string) $item->title;
        $slug = (string) $wpNs->post_name ?: Str::slug($title);
        $status = (string) $wpNs->status;

        // Skip post types not selected
        $selectedPostTypes = $batch->import_post_types ?? [];
        if (! in_array($postType, $selectedPostTypes)) {
            return;
        }

        // Skip statuses not selected
        $selectedStatuses = $batch->import_statuses ?? [];
        if (! in_array($status, $selectedStatuses)) {
            return;
        }

        $batch->increment('processed_items');

        // Extract categories and resolve deepest
        $postCategories = [];
        if (isset($item->category)) {
            foreach ($item->category as $catNode) {
                $domain = (string) $catNode['domain'];
                $nicename = (string) $catNode['nicename'];
                if ($domain === 'category' && $nicename) {
                    $postCategories[] = $nicename;
                }
            }
        }

        $chosenCategoryId = null;
        $rootParentId = Category::whereNull('parent_id')->exists() ? null : -1;

        if (! empty($postCategories)) {
            // Find matched target IDs
            $matchedIds = [];
            foreach ($postCategories as $pCatSlug) {
                if (isset($slugToIdMap[$pCatSlug])) {
                    $matchedIds[] = $slugToIdMap[$pCatSlug];
                }
            }

            if (! empty($matchedIds)) {
                // Pick the first matched ID (usually deepest child, as categories are sorted by depth ascending)
                $chosenCategoryId = $matchedIds[count($matchedIds) - 1];
            }
        }

        // Fallback Category
        if (! $chosenCategoryId) {
            $defaultCat = Category::where('slug', 'chua-phan-loai')
                ->orWhere('name', 'Chưa được phân loại')
                ->first();
            if (! $defaultCat) {
                if ($batch->dry_run) {
                    $chosenCategoryId = 9999;
                } else {
                    $defaultCat = Category::create([
                        'name' => 'Chưa được phân loại',
                        'slug' => 'chua-phan-loai',
                        'parent_id' => $rootParentId ?? -1,
                        'order' => 1,
                    ]);
                    $chosenCategoryId = $defaultCat->id;
                }
            } else {
                $chosenCategoryId = $defaultCat->id;
            }
        }

        // Extract metadata
        $metas = [];
        if (isset($wpNs->postmeta)) {
            foreach ($wpNs->postmeta as $meta) {
                $key = (string) $meta->meta_key;
                $value = (string) $meta->meta_value;
                $metas[$key] = $value;
            }
        }

        // Resolve thumbnail
        $thumbnailImage = null;
        $thumbnailId = isset($metas['_thumbnail_id']) ? (int) $metas['_thumbnail_id'] : null;
        if ($thumbnailId && isset($attachmentMap[$thumbnailId])) {
            $relativeAttachedFile = $attachmentMap[$thumbnailId]['file'];

            // Check local copy if path provided
            $copied = $this->copyMediaFile($relativeAttachedFile, $batch->local_media_base_path, $batch->media_mode, $batch);
            if (! $copied && ! empty($batch->local_media_base_path)) {
                $batch->increment('missing_media_items');
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'missing_media', 'warning', 'Không tìm thấy ảnh nổi bật cục bộ: '.$relativeAttachedFile);
            }

            // Set DB value
            if ($batch->media_mode === 'storage_uploads') {
                $thumbnailImage = 'uploads/'.ltrim($relativeAttachedFile, '/');
            } elseif ($batch->media_mode === 'public_wp_uploads') {
                $thumbnailImage = 'wp-content/uploads/'.ltrim($relativeAttachedFile, '/');
            } else {
                $thumbnailImage = $attachmentMap[$thumbnailId]['url'];
            }
        }

        // Parse content
        $content = (string) $contentNs->encoded;

        // 1. Convert WordPress [caption] shortcodes into standard HTML <figure>
        $content = preg_replace_callback('/\[caption[^\]]*\](.*?)\[\/caption\]/is', function ($matches) {
            return '<figure class="wp-caption flex flex-col items-center justify-center my-6 p-2 bg-slate-50 border border-slate-100 rounded-2xl max-w-full mx-auto">'
                 .trim($matches[1])
                 .'</figure>';
        }, $content);

        // 2. Clean content: Strip script tags
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);

        // 3. Clean content: Strip inline event handlers
        $content = preg_replace('/\bon[a-z]+\s*=\s*(["\'])[^\'"]*?\1/is', '', $content);
        $content = preg_replace('/\bon[a-z]+\s*=\s*[^\s>]+/is', '', $content);

        // 4. Rewrite inline images/links and check local file copies
        $content = $this->rewriteContentUrls($content, $batch->old_domain, $batch->media_mode);

        // Detect inline media copy if base path exists
        if (! empty($batch->local_media_base_path) && $batch->media_mode !== 'keep_remote') {
            preg_match_all('/(?:wp-content\/uploads|storage\/uploads)\/([^\s"\'\>\?#]+)/i', $content, $matches);
            if (! empty($matches[1])) {
                foreach ($matches[1] as $matchPath) {
                    $copied = $this->copyMediaFile($matchPath, $batch->local_media_base_path, $batch->media_mode, $batch);
                    if (! $copied) {
                        $batch->increment('missing_media_items');
                        $this->logAction($batch->id, $postId, $postType, $slug, $title, 'missing_media', 'warning', 'Không tìm thấy tệp ảnh nội dung cục bộ: '.$matchPath);
                    }
                }
            }
        }

        // Extract excerpt
        $excerpt = (string) $excerptNs->encoded;

        // Resolve SEO metadata fields
        $metaTitle = $metas['rank_math_title'] ?? $metas['_yoast_wpseo_title'] ?? null;
        $metaDesc = $metas['rank_math_description'] ?? $metas['_yoast_wpseo_metadesc'] ?? null;
        $focusKw = $metas['rank_math_focus_keyword'] ?? $metas['_yoast_wpseo_focuskw'] ?? null;
        $canonicalUrl = $metas['rank_math_canonical_url'] ?? $metas['_yoast_wpseo_canonical'] ?? null;

        $ogTitle = $metas['rank_math_facebook_title'] ?? $metas['_yoast_wpseo_opengraph-title'] ?? null;
        $ogDesc = $metas['rank_math_facebook_description'] ?? $metas['_yoast_wpseo_opengraph-description'] ?? null;

        $twitterTitle = $metas['rank_math_twitter_title'] ?? $metas['_yoast_wpseo_twitter-title'] ?? null;
        $twitterDesc = $metas['rank_math_twitter_description'] ?? $metas['_yoast_wpseo_twitter-description'] ?? null;

        // Fallbacks
        if (empty($metaTitle)) {
            $metaTitle = $title;
        }
        if (empty($metaDesc)) {
            $cleanText = strip_tags($content);
            $metaDesc = mb_substr(preg_replace('/\s+/', ' ', $cleanText), 0, 160);
        }
        $seoSlug = $slug;

        // Safety checking of fields
        $existingColumns = Schema::getColumnListing('articles');
        $articleData = [];

        $setField = function ($columnName, $value) use (&$articleData, $existingColumns, $batch) {
            if (in_array($columnName, $existingColumns)) {
                $articleData[$columnName] = $value;
            } else {
                $this->logOmittedField($batch->id, $columnName);
            }
        };

        $setField('category_id', $chosenCategoryId);
        $setField('title', $title);
        $setField('slug', $slug);
        $setField('content', $content);
        $setField('thumbnail_image', $thumbnailImage);
        $setField('meta_title', $metaTitle);
        $setField('meta_description', $metaDesc);
        $setField('is_published', ($status === 'publish'));
        $setField('focus_keyword', $focusKw);
        $setField('seo_slug', $seoSlug);
        $setField('canonical_url', $canonicalUrl);
        $setField('robots_index', true);
        $setField('robots_follow', true);

        $setField('og_title', $ogTitle ?: $metaTitle);
        $setField('og_description', $ogDesc ?: $metaDesc);
        $setField('og_image', $thumbnailImage);

        $setField('twitter_title', $twitterTitle ?: $metaTitle);
        $setField('twitter_description', $twitterDesc ?: $metaDesc);
        $setField('twitter_image', $thumbnailImage);

        // Created / Updated timestamp fallback
        $setField('created_at', (string) $wpNs->post_date ?: now());
        $setField('updated_at', (string) $wpNs->post_modified ?: now());

        // Check against Reserved Slugs
        $reservedSlugs = [
            'admin', 'login', 'logout', 'register', 'lien-he', 'tim-kiem', 'category', 'categories',
            'bai-viet', 'articles', 'nam-khoa', 'phu-khoa', 'ngoai-khoa', 'benh-xa-hoi', 'xet-nghiem',
            'vi-cong-dong', 'gioi-thieu', 'chinh-sach-bao-mat', 'dieu-khoan-su-dung', 'sitemap', 'sitemap.xml',
        ];

        $isReserved = in_array(strtolower($slug), $reservedSlugs);
        $existingArticle = Article::where('slug', $slug)->first();

        if ($isReserved) {
            if ($batch->duplicate_mode === 'unique') {
                $slug = $slug.'-wp-'.$postId;
                $setField('slug', $slug);
                $setField('seo_slug', $slug);
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'warning', 'warning', 'Slug trùng với route hệ thống đã đặt. Đổi thành: '.$slug);
                $isReserved = false;
            } elseif ($batch->duplicate_mode === 'skip') {
                $batch->increment('skipped_items');
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'skipped', 'warning', 'Bỏ qua bài viết có slug trùng với route hệ thống.');

                return;
            } else { // update mode
                if (! $existingArticle) {
                    $batch->increment('skipped_items');
                    $this->logAction($batch->id, $postId, $postType, $slug, $title, 'skipped', 'warning', 'Bỏ qua cập nhật vì slug trùng với route hệ thống và không có bài viết tương ứng.');

                    return;
                }
            }
        }

        // Duplicate handling
        if ($existingArticle && ! $isReserved) {
            if ($batch->duplicate_mode === 'skip') {
                $batch->increment('skipped_items');
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'skipped', 'success', 'Bỏ qua vì bài viết trùng slug đã tồn tại.');

                return;
            } elseif ($batch->duplicate_mode === 'unique') {
                $originalSlug = $slug;
                $slug = $originalSlug.'-wp-'.$postId;

                $suffix = 1;
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$suffix;
                    $suffix++;
                }

                $setField('slug', $slug);
                $setField('seo_slug', $slug);
            } else {
                // Update mode
                if ($batch->dry_run) {
                    $this->logAction($batch->id, $postId, $postType, $slug, $title, 'dry_run', 'success', 'Sẽ cập nhật bài viết có sẵn: '.$existingArticle->title);
                    $batch->increment('updated_items');

                    return;
                }

                // Run SEO analyzer if column and service exist
                if (in_array('seo_score', $existingColumns)) {
                    try {
                        $tempArticle = new Article($articleData);
                        $analyzer = new ArticleSeoAnalyzerService;
                        $seoResult = $analyzer->analyze($tempArticle);
                        $setField('seo_score', $seoResult['score'] ?? 0);
                        $setField('seo_checks', json_encode($seoResult));
                    } catch (\Throwable $e) {
                        $setField('seo_score', 0);
                        $setField('seo_checks', null);
                        $this->logAction($batch->id, $postId, $postType, $slug, $title, 'warning', 'warning', 'Lỗi phân tích SEO: '.$e->getMessage());
                    }
                }

                // Save update
                $existingArticle->update($articleData);
                $batch->increment('updated_items');
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'updated', 'success', 'Đã cập nhật bài viết thành công.', ['target_id' => $existingArticle->id]);

                return;
            }
        }

        // Create new article
        if ($batch->dry_run) {
            $this->logAction($batch->id, $postId, $postType, $slug, $title, 'dry_run', 'success', 'Sẽ tạo bài viết mới: '.$title);
            $batch->increment('imported_items');

            return;
        }

        // Run SEO analyzer
        if (in_array('seo_score', $existingColumns)) {
            try {
                $tempArticle = new Article($articleData);
                $analyzer = new ArticleSeoAnalyzerService;
                $seoResult = $analyzer->analyze($tempArticle);
                $setField('seo_score', $seoResult['score'] ?? 0);
                $setField('seo_checks', json_encode($seoResult));
            } catch (\Throwable $e) {
                $setField('seo_score', 0);
                $setField('seo_checks', null);
                $this->logAction($batch->id, $postId, $postType, $slug, $title, 'warning', 'warning', 'Lỗi phân tích SEO: '.$e->getMessage());
            }
        }

        try {
            $newArticle = Article::create($articleData);
            $batch->increment('imported_items');
            $this->logAction($batch->id, $postId, $postType, $slug, $title, 'imported', 'success', 'Đã thêm bài viết mới thành công.', ['target_id' => $newArticle->id]);
        } catch (\Throwable $e) {
            $batch->increment('failed_items');
            $this->logAction($batch->id, $postId, $postType, $slug, $title, 'failed', 'error', 'Thất bại khi ghi DB: '.$e->getMessage());
        }
    }

    /**
     * Rewrite content image URLs safely.
     */
    public function rewriteContentUrls(string $content, string $oldDomain, string $mediaMode): string
    {
        if ($mediaMode === 'keep_remote') {
            return $content;
        }

        $oldDomainHost = parse_url($oldDomain, PHP_URL_HOST);
        if (! $oldDomainHost) {
            $oldDomainHost = $oldDomain;
        }
        $oldDomainHost = preg_replace('/^www\./', '', $oldDomainHost); // e.g. dakhoacantho.com

        // Pattern for absolute links to uploads
        $patternAbsolute = '/(?:https?:)?\/\/(?:www\.)?'.preg_quote($oldDomainHost, '/').'\/wp-content\/uploads\/([^\s"\'\>\?#]+)/i';
        // Pattern for relative links to uploads
        $patternRelative = '/\/wp-content\/uploads\/([^\s"\'\>\?#]+)/i';

        $targetBase = ($mediaMode === 'storage_uploads') ? 'storage/uploads/' : 'wp-content/uploads/';

        // Replace absolute URLs
        $content = preg_replace_callback($patternAbsolute, function ($matches) use ($targetBase) {
            $filePath = urldecode($matches[1]);

            return url($targetBase.$filePath);
        }, $content);

        // Replace relative URLs
        $content = preg_replace_callback($patternRelative, function ($matches) use ($targetBase) {
            $filePath = urldecode($matches[1]);

            return url($targetBase.$filePath);
        }, $content);

        return $content;
    }

    /**
     * Log omitted fields once per batch.
     */
    protected function logOmittedField($batchId, $field)
    {
        $key = $batchId.'_'.$field;
        if (! isset($this->omittedFields[$key])) {
            $this->omittedFields[$key] = true;
            $this->logAction($batchId, null, 'system', null, null, 'warning', 'warning', "Cột '{$field}' không tồn tại trong bảng 'articles'. Bỏ qua thuộc tính này.");
        }
    }

    /**
     * Helper to copy local media attachments.
     */
    protected function copyMediaFile(string $relativeFile, ?string $localMediaBasePath, string $mediaMode, WordPressImportBatch $batch): bool
    {
        if (empty($localMediaBasePath)) {
            return false;
        }

        $relativeFile = urldecode($relativeFile);
        $relativeFile = str_replace('\\', '/', $relativeFile);
        $relativeFile = ltrim($relativeFile, '/');

        // Check multiple possible paths relative to Laravel root folder
        $sourcePath = base_path(rtrim($localMediaBasePath, '/\\').'/'.$relativeFile);

        if (! File::exists($sourcePath)) {
            return false;
        }

        if ($mediaMode === 'storage_uploads') {
            $destPath = storage_path('app/public/uploads/'.$relativeFile);
        } else {
            $destPath = public_path('wp-content/uploads/'.$relativeFile);
        }

        if ($batch->dry_run) {
            return true;
        }

        try {
            File::ensureDirectoryExists(dirname($destPath));
            if (! File::exists($destPath)) {
                File::copy($sourcePath, $destPath);
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Normalize Vietnamese strings for comparison.
     */
    protected function normalizeString(string $str): string
    {
        $str = mb_strtolower(trim($str));
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = preg_replace('/[^a-z0-9\s]/', '', $str);
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Create log action.
     */
    public function logAction($batchId, $sourcePostId, $sourcePostType, $sourceSlug, $sourceTitle, $action, $status, $message, $context = null)
    {
        WordPressImportLog::create([
            'batch_id' => $batchId,
            'source_post_id' => $sourcePostId,
            'source_post_type' => $sourcePostType,
            'source_slug' => $sourceSlug,
            'source_title' => $sourceTitle,
            'action' => $action,
            'status' => $status,
            'message' => $message,
            'context' => $context,
        ]);
    }
}
