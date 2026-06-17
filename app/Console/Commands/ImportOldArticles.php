<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleSeoAnalyzerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportOldArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:import-old 
                            {--dry-run : Simulate the migration without writing data or copying files} 
                            {--limit= : Limit the number of articles to import for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate articles and categories from the old WordPress database safely';

    // Statistics trackers
    protected $totalWpFound = 0;

    protected $importedCount = 0;

    protected $skippedCount = 0;

    protected $failedCount = 0;

    protected $missingImagesCount = 0;

    protected $createdCategoriesCount = 0;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info('=== Starting WordPress Migration ===');
        if ($dryRun) {
            $this->warn('!!! RUNNING IN DRY-RUN MODE (No database writes or file copies will occur) !!!');
        }

        // Initialize log file (clear old logs or create a header)
        $logFile = storage_path('logs/article-import.log');
        $header = sprintf("=== Import Started at %s (Dry Run: %s, Limit: %s) ===\n", date('Y-m-d H:i:s'), $dryRun ? 'YES' : 'NO', $limit ?? 'NONE');
        File::put($logFile, $header);

        // Step 1: Check Connection to old database
        try {
            DB::connection('old_mysql')->getPdo();
            $this->info('Connected successfully to old WordPress database (connection: old_mysql).');
        } catch (\Exception $e) {
            $this->error("Failed to connect to old database connection 'old_mysql'. Error: ".$e->getMessage());
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'DB_CONN_FAIL', null, $e->getMessage());

            return 1;
        }

        // Step 2: Database Backup (only if NOT dry-run)
        if (! $dryRun) {
            if ($this->runBackup() !== 0) {
                $this->error('Halting migration: Backup failed. Database safety is a critical priority.');

                return 1;
            }
        }

        // Step 3: Verify Public Storage Link (only if NOT dry-run)
        if (! $dryRun) {
            $storageLinkExists = File::exists(public_path('storage'));
            if (! $storageLinkExists) {
                $this->info('Creating public storage symlink...');
                try {
                    $this->call('storage:link');
                } catch (\Exception $e) {
                    $this->warn('Failed to create storage symlink automatically: '.$e->getMessage());
                }
            } else {
                $this->info('Public storage link exists and is stable.');
            }
        }

        // Step 4: Quantity Reconciliation & Post Status Reporting
        $this->info("Analyzing WordPress post counts by status (post_type = 'post')...");
        try {
            $statusCounts = DB::connection('old_mysql')
                ->table('bqtdbhah0_posts')
                ->where('post_type', 'post')
                ->select('post_status', DB::raw('count(*) as count'))
                ->groupBy('post_status')
                ->get();

            $headers = ['Post Status', 'Count'];
            $rows = [];
            foreach ($statusCounts as $sc) {
                $rows[] = [$sc->post_status, $sc->count];
            }
            $this->table($headers, $rows);
        } catch (\Exception $e) {
            $this->error('Failed to fetch post status counts: '.$e->getMessage());
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'STATUS_REPORT_FAIL', null, $e->getMessage());

            return 1;
        }

        // Step 5: Load and Map Categories Hierarchically
        $this->info('Fetching and mapping WordPress categories...');
        try {
            $wpCategories = DB::connection('old_mysql')
                ->table('bqtdbhah0_terms as t')
                ->join('bqtdbhah0_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'category')
                ->select('t.term_id', 't.name', 't.slug', 'tt.description', 'tt.parent')
                ->get();
        } catch (\Exception $e) {
            $this->error('Failed to load WordPress categories: '.$e->getMessage());
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'FETCH_CATEGORIES_FAIL', null, $e->getMessage());

            return 1;
        }

        $categoriesByWpId = [];
        foreach ($wpCategories as $wpCat) {
            $categoriesByWpId[$wpCat->term_id] = [
                'term_id' => $wpCat->term_id,
                'name' => $wpCat->name,
                'slug' => $wpCat->slug,
                'description' => $wpCat->description,
                'parent' => (int) $wpCat->parent,
                'depth' => -1, // placeholder
            ];
        }

        // Compute hierarchy depth recursively
        $computeDepth = function ($termId) use (&$categoriesByWpId, &$computeDepth) {
            if (! isset($categoriesByWpId[$termId])) {
                return 0;
            }
            if ($categoriesByWpId[$termId]['depth'] !== -1) {
                return $categoriesByWpId[$termId]['depth'];
            }
            if ($categoriesByWpId[$termId]['parent'] === 0) {
                $categoriesByWpId[$termId]['depth'] = 0;

                return 0;
            }
            $parentDepth = $computeDepth($categoriesByWpId[$termId]['parent']);
            $categoriesByWpId[$termId]['depth'] = 1 + $parentDepth;

            return $categoriesByWpId[$termId]['depth'];
        };

        foreach ($categoriesByWpId as $termId => $cat) {
            $computeDepth($termId);
        }

        // Category matching & creation mapping logic
        $targetCategoryIdMap = []; // wp_term_id => target_category_id

        // Ensure default category exists in target
        $defaultCategory = Category::where('slug', 'khong-phan-loai')
            ->orWhere('name', 'Chưa được phân loại')
            ->first();
        if (! $defaultCategory) {
            if (! $dryRun) {
                $defaultCategory = Category::create([
                    'name' => 'Chưa được phân loại',
                    'slug' => 'khong-phan-loai',
                    'parent_id' => -1,
                    'order' => 1,
                ]);
            } else {
                $defaultCategory = (object) ['id' => 1, 'name' => 'Chưa được phân loại', 'slug' => 'khong-phan-loai'];
            }
        }

        $migrateCategory = function ($wpTermId) use (
            &$categoriesByWpId,
            &$targetCategoryIdMap,
            &$migrateCategory,
            $dryRun,
            $defaultCategory
        ) {
            if (isset($targetCategoryIdMap[$wpTermId])) {
                return $targetCategoryIdMap[$wpTermId];
            }

            if (! isset($categoriesByWpId[$wpTermId])) {
                return $defaultCategory->id;
            }

            $wpCat = $categoriesByWpId[$wpTermId];

            // Resolve parent first recursively
            $parentTargetId = -1; // Default FilamentTree root parent ID
            if ($wpCat['parent'] > 0) {
                $parentTargetId = $migrateCategory($wpCat['parent']);
            }

            $name = $wpCat['name'];
            $slug = $wpCat['slug'] ?: Str::slug($name);

            // Match by slug first
            $targetCat = Category::where('slug', $slug)->first();

            // Match by name if slug not found
            if (! $targetCat) {
                $targetCat = Category::where('name', $name)->first();
            }

            if ($targetCat) {
                $targetCategoryIdMap[$wpTermId] = $targetCat->id;
                $this->logAction($wpTermId, $slug, $name, 'matched_category', $targetCat->id);

                return $targetCat->id;
            }

            // Create new category if not found
            if ($dryRun) {
                $simulatedId = 1000 + $wpTermId;
                $targetCategoryIdMap[$wpTermId] = $simulatedId;
                $this->logAction($wpTermId, $slug, $name, 'created_category (simulated)', $simulatedId);
                $this->createdCategoriesCount++;

                return $simulatedId;
            }

            // Generate unique slug in case of collision
            $originalSlug = $slug;
            $suffix = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$suffix;
                $suffix++;
            }

            $newCat = Category::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $wpCat['description'] ?? '',
                'parent_id' => $parentTargetId,
                'order' => 1,
            ]);

            $targetCategoryIdMap[$wpTermId] = $newCat->id;
            $this->logAction($wpTermId, $slug, $name, 'created_category', $newCat->id);
            $this->createdCategoriesCount++;

            return $newCat->id;
        };

        // Run migrations for all categories
        foreach ($categoriesByWpId as $termId => $cat) {
            $migrateCategory($termId);
        }
        $this->info("Categories mapping complete. Created {$this->createdCategoriesCount} new categories.");

        // Step 6: Query and Import Articles (using chunking to limit memory usage)
        $this->info('Fetching WordPress published posts...');
        try {
            $postsQuery = DB::connection('old_mysql')
                ->table('bqtdbhah0_posts')
                ->where('post_type', 'post')
                ->where('post_status', 'publish')
                ->orderBy('ID');

            if ($limit) {
                $postsQuery->limit($limit);
            }

            // Count total matching posts
            $this->totalWpFound = $postsQuery->count();
            $this->info("Found {$this->totalWpFound} posts matching criteria.");
        } catch (\Exception $e) {
            $this->error('Failed to query published posts: '.$e->getMessage());
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'QUERY_POSTS_FAIL', null, $e->getMessage());

            return 1;
        }

        if ($this->totalWpFound === 0) {
            $this->warn('No published posts found to migrate.');

            return 0;
        }

        // Initialize progress bar
        $progressBar = $this->output->createProgressBar($this->totalWpFound);
        $progressBar->start();

        // Process in chunks of 50 to maintain cursor performance and low memory
        $chunkSize = 50;
        $processedCount = 0;

        $postsQuery->chunk($chunkSize, function ($posts) use ($migrateCategory, $categoriesByWpId, $defaultCategory, $dryRun, $progressBar, &$processedCount, $limit) {
            // Bulk fetch term relationships for these posts
            $postIds = $posts->pluck('ID')->toArray();

            $relationships = DB::connection('old_mysql')
                ->table('bqtdbhah0_term_relationships as tr')
                ->join('bqtdbhah0_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
                ->whereIn('tr.object_id', $postIds)
                ->where('tt.taxonomy', 'category')
                ->select('tr.object_id', 'tt.term_id')
                ->get()
                ->groupBy('object_id');

            // Bulk fetch postmeta
            $postmetas = DB::connection('old_mysql')
                ->table('bqtdbhah0_postmeta')
                ->whereIn('post_id', $postIds)
                ->whereIn('meta_key', [
                    '_yoast_wpseo_title',
                    '_yoast_wpseo_metadesc',
                    '_yoast_wpseo_focuskw',
                    'rank_math_title',
                    'rank_math_description',
                    'rank_math_focus_keyword',
                    '_thumbnail_id',
                ])
                ->get()
                ->groupBy('post_id');

            // Collect thumbnail IDs to load their paths in bulk
            $thumbnailIds = [];
            foreach ($postmetas as $postId => $metaGroup) {
                $thumbMeta = $metaGroup->where('meta_key', '_thumbnail_id')->first();
                if ($thumbMeta && $thumbMeta->meta_value) {
                    $thumbnailIds[] = (int) $thumbMeta->meta_value;
                }
            }
            $thumbnailIds = array_unique(array_filter($thumbnailIds));

            $attachmentPaths = [];
            if (! empty($thumbnailIds)) {
                $attachmentPaths = DB::connection('old_mysql')
                    ->table('bqtdbhah0_postmeta')
                    ->whereIn('post_id', $thumbnailIds)
                    ->where('meta_key', '_wp_attached_file')
                    ->pluck('meta_value', 'post_id')
                    ->toArray();
            }

            foreach ($posts as $post) {
                if ($limit && $processedCount >= $limit) {
                    break;
                }

                $title = $post->post_title;
                $slug = $post->post_name ?: Str::slug($title);

                // Duplicate checking
                $slugExists = Article::where('slug', $slug)->exists();
                if ($slugExists) {
                    $this->logAction($post->ID, $slug, $title, 'skipped_duplicate', null, 'Article with same slug already exists.');
                    $this->skippedCount++;
                    $progressBar->advance();
                    $processedCount++;

                    continue;
                }

                // Determine category (deepest child preferred)
                $chosenCategoryId = $defaultCategory->id;
                $associatedWpTermIds = isset($relationships[$post->ID]) ? $relationships[$post->ID]->pluck('term_id')->toArray() : [];

                if (! empty($associatedWpTermIds)) {
                    $validCats = [];
                    foreach ($associatedWpTermIds as $termId) {
                        if (isset($categoriesByWpId[$termId])) {
                            $validCats[] = $categoriesByWpId[$termId];
                        }
                    }

                    if (! empty($validCats)) {
                        // Sort by depth descending
                        usort($validCats, function ($a, $b) {
                            return $b['depth'] <=> $a['depth'];
                        });
                        $deepestWpTermId = $validCats[0]['term_id'];
                        $chosenCategoryId = $migrateCategory($deepestWpTermId);
                    }
                }

                // Retrieve SEO Metadata
                $metaGroup = $postmetas->get($post->ID) ?: collect();

                $metaTitle = $metaGroup->where('meta_key', 'rank_math_title')->first()->meta_value ??
                             $metaGroup->where('meta_key', '_yoast_wpseo_title')->first()->meta_value ??
                             null;

                $metaDesc = $metaGroup->where('meta_key', 'rank_math_description')->first()->meta_value ??
                            $metaGroup->where('meta_key', '_yoast_wpseo_metadesc')->first()->meta_value ??
                            null;

                $focusKw = $metaGroup->where('meta_key', 'rank_math_focus_keyword')->first()->meta_value ??
                           $metaGroup->where('meta_key', '_yoast_wpseo_focuskw')->first()->meta_value ??
                           null;

                // Set SEO Fallbacks
                if (empty($metaTitle)) {
                    $metaTitle = $title;
                }
                if (empty($metaDesc)) {
                    $excerpt = trim($post->post_excerpt);
                    if ($excerpt !== '') {
                        $metaDesc = $excerpt;
                    } else {
                        // Strip HTML and take first 160 characters
                        $cleanText = strip_tags($post->post_content);
                        $metaDesc = mb_substr(preg_replace('/\s+/', ' ', $cleanText), 0, 160);
                    }
                }

                // Resolve featured image
                $thumbnailImage = null;
                $thumbnailMeta = $metaGroup->where('meta_key', '_thumbnail_id')->first();
                if ($thumbnailMeta && $thumbnailMeta->meta_value) {
                    $thumbPostId = (int) $thumbnailMeta->meta_value;
                    if (isset($attachmentPaths[$thumbPostId])) {
                        $wpAttachedFile = $attachmentPaths[$thumbPostId];
                        $thumbnailImage = $this->migrateMediaFile($wpAttachedFile, $post->ID, $slug, $title, $dryRun);
                    }
                }

                // Clean and normalize content
                $content = $post->post_content;

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

                // 4. Extract and copy inline content images
                $content = $this->migrateInlineContentImages($content, $post->ID, $slug, $title, $dryRun);

                // Prepare Article Data
                $articleData = [
                    'category_id' => $chosenCategoryId,
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content ?: '',
                    'thumbnail_image' => $thumbnailImage,
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDesc,
                    'is_published' => true,
                    'focus_keyword' => $focusKw,
                    'seo_slug' => $slug,
                    'canonical_url' => null,
                    'robots_index' => true,
                    'robots_follow' => true,
                    'og_title' => $metaTitle,
                    'og_description' => $metaDesc,
                    'og_image' => $thumbnailImage,
                    'twitter_title' => $metaTitle,
                    'twitter_description' => $metaDesc,
                    'twitter_image' => $thumbnailImage,
                    'created_at' => $post->post_date ?: now(),
                    'updated_at' => $post->post_modified ?: now(),
                ];

                // Integrate SEO analyzer inside try-catch block
                try {
                    $tempArticle = new Article($articleData);
                    $analyzer = new ArticleSeoAnalyzerService;
                    $seoResult = $analyzer->analyze($tempArticle);
                    $articleData['seo_score'] = $seoResult['score'] ?? 0;
                    $articleData['seo_checks'] = json_encode($seoResult);
                } catch (\Throwable $e) {
                    $this->logAction($post->ID, $slug, $title, 'seo_analyzer_error', null, $e->getMessage());
                    $articleData['seo_score'] = 0;
                    $articleData['seo_checks'] = null;
                }

                // Insert into Target Database if not in dry-run
                try {
                    if (! $dryRun) {
                        $newArticle = Article::create($articleData);
                        $targetId = $newArticle->id;
                        $this->logAction($post->ID, $slug, $title, 'imported_article', $targetId);
                    } else {
                        $targetId = 2000 + $post->ID; // Simulated target ID
                        $this->logAction($post->ID, $slug, $title, 'imported_article (simulated)', $targetId);
                    }
                    $this->importedCount++;
                } catch (\Exception $e) {
                    $this->logAction($post->ID, $slug, $title, 'failed_import', null, $e->getMessage());
                    $this->failedCount++;
                }

                $progressBar->advance();
                $processedCount++;
            }
        });

        $progressBar->finish();
        $this->newLine(2);

        // Step 7: Clear Caches (only if NOT dry-run and imported anything)
        if (! $dryRun && $this->importedCount > 0) {
            $this->info('Clearing application view and cache files...');
            try {
                $this->call('view:clear');
                $this->call('cache:clear');
                $this->info('Caches cleared successfully.');
            } catch (\Exception $e) {
                $this->warn('Failed to clear caches: '.$e->getMessage());
            }
        }

        // Display Migration Summary
        $this->info('=== Migration Summary ===');
        $summaryHeaders = ['Metric', 'Count'];
        $summaryRows = [
            ['Total Published WP Posts Found', $this->totalWpFound],
            ['Articles Successfully Imported', $this->importedCount],
            ['Articles Skipped (Already exists)', $this->skippedCount],
            ['Articles Failed (Database insert error)', $this->failedCount],
            ['Missing Images / Copy Failures', $this->missingImagesCount],
            ['Categories Created / Matched', $this->createdCategoriesCount],
        ];
        $this->table($summaryHeaders, $summaryRows);
        $this->info('Detailed audit logs are written to: storage/logs/article-import.log');
        $this->info('=== WordPress Migration Finished ===');

        return 0;
    }

    /**
     * Run the mysqldump database backup safely.
     */
    protected function runBackup(): int
    {
        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $backupFile = $backupDir.'/dakhoacantho_web_before_import.sql';

        $mysqlDumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (! File::exists($mysqlDumpPath)) {
            $mysqlDumpPath = 'mysqldump'; // Try to resolve via PATH
        }

        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'dakhoacantho_web');
        $dbUser = config('database.connections.mysql.username', 'root');
        $dbPass = config('database.connections.mysql.password', '');

        $passwordOption = $dbPass !== '' ? '-p'.escapeshellarg($dbPass) : '';

        // Safely build shell redirect command for Windows/PowerShell
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

        $this->info("Creating database backup file at: {$backupFile}");

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'BACKUP_FAIL', null, "mysqldump exit code: {$resultCode}");

            return $resultCode;
        }

        $this->info('Database backup created successfully.');
        $this->logAction('SYSTEM', 'N/A', 'N/A', 'BACKUP_SUCCESS', null, "Backup created at {$backupFile}");

        return 0;
    }

    /**
     * Helper to migrate media file from old WordPress uploads folder to Laravel public folder.
     */
    protected function migrateMediaFile(string $wpAttachedFile, int $wpPostId, string $slug, string $title, bool $dryRun): ?string
    {
        // URL decode and replace backslashes to normalize path
        $relPath = urldecode($wpAttachedFile);
        $relPath = str_replace('\\', '/', $relPath);
        $relPath = ltrim($relPath, '/');

        $srcUploadsDir = 'C:\\xampp\\htdocs\\dakhoacantho\\wp-content\\uploads\\';
        $srcFile = $srcUploadsDir.str_replace('/', '\\', $relPath);

        $dstFile = storage_path('app/public/uploads/'.$relPath);
        $targetDbPath = 'uploads/'.$relPath;

        if (! File::exists($srcFile)) {
            $this->logAction($wpPostId, $slug, $title, 'missing_image_warning', null, "Source image not found: {$srcFile}");
            $this->missingImagesCount++;

            return $targetDbPath; // Still return the expected target path so we don't drop image reference
        }

        // If file exists, copy it
        if (! $dryRun) {
            try {
                File::ensureDirectoryExists(dirname($dstFile));
                if (! File::exists($dstFile)) {
                    File::copy($srcFile, $dstFile);
                }
            } catch (\Exception $e) {
                $this->logAction($wpPostId, $slug, $title, 'image_copy_error', null, "Failed to copy image to: {$dstFile}. Error: ".$e->getMessage());
                $this->missingImagesCount++;
            }
        }

        return $targetDbPath;
    }

    /**
     * Parse and migrate inline images in post_content.
     */
    protected function migrateInlineContentImages(string $content, int $wpPostId, string $slug, string $title, bool $dryRun): string
    {
        // Find all img tags in the content
        preg_match_all('/src=["\']([^"\']+)["\']/is', $content, $matches);
        if (empty($matches[1])) {
            return $content;
        }

        foreach ($matches[1] as $imgUrl) {
            // Check if it belongs to WordPress uploads directory
            if (str_contains($imgUrl, 'wp-content/uploads/')) {
                $decodedUrl = urldecode($imgUrl);

                // Extract relative path after wp-content/uploads/
                $parts = explode('wp-content/uploads/', $decodedUrl);
                if (count($parts) < 2) {
                    continue;
                }

                $relPath = $parts[1];
                // Strip query parameters if any (e.g. ?v=1.2)
                $relPath = explode('?', $relPath)[0];
                $relPath = str_replace('\\', '/', $relPath);
                $relPath = ltrim($relPath, '/');

                // Copy the local file if it exists
                $srcUploadsDir = 'C:\\xampp\\htdocs\\dakhoacantho\\wp-content\\uploads\\';
                $srcFile = $srcUploadsDir.str_replace('/', '\\', $relPath);
                $dstFile = storage_path('app/public/uploads/'.$relPath);

                if (! File::exists($srcFile)) {
                    $this->logAction($wpPostId, $slug, $title, 'missing_inline_image', null, "Inline image file not found: {$srcFile}");
                    $this->missingImagesCount++;
                } else {
                    if (! $dryRun) {
                        try {
                            File::ensureDirectoryExists(dirname($dstFile));
                            if (! File::exists($dstFile)) {
                                File::copy($srcFile, $dstFile);
                            }
                        } catch (\Exception $e) {
                            $this->logAction($wpPostId, $slug, $title, 'inline_image_copy_error', null, 'Failed to copy inline image: '.$e->getMessage());
                            $this->missingImagesCount++;
                        }
                    }
                }

                // Replace in content
                // Resolve using relative path /storage/uploads/ which works fine on local and production
                $newImgUrl = '/storage/uploads/'.$relPath;
                $content = str_replace($imgUrl, $newImgUrl, $content);
            }
        }

        return $content;
    }

    /**
     * Log helper for detailed auditing.
     */
    protected function logAction($sourceId, $slug, $title, $action, $targetId = null, $message = null)
    {
        $logFile = storage_path('logs/article-import.log');
        $timestamp = date('Y-m-d H:i:s');
        $logLine = sprintf(
            "[%s] WP_ID: %s | Slug: %s | Title: %s | Action: %s | Target_ID: %s | Msg: %s\n",
            $timestamp,
            $sourceId,
            $slug,
            $title,
            strtoupper($action),
            $targetId ?? 'N/A',
            $message ?? 'N/A'
        );
        File::append($logFile, $logLine);
    }
}
