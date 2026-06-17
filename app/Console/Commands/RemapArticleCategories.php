<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RemapArticleCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:remap-categories
                            {--dry-run : Preview changes without updating}
                            {--limit= : Limit number of articles to process}
                            {--slug= : Process only one new article by slug}
                            {--old-post-id= : Process only one old WordPress post ID}
                            {--only-wrong : Only update articles where current category differs from target category}
                            {--confidence=high : Minimum confidence level: high|medium}
                            {--force : Force execution without confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remap imported article categories based on old WordPress hierarchy and title/slug keywords';

    // Summary statistics
    protected $totalOldPosts = 0;

    protected $matchedArticlesCount = 0;

    protected $changedCount = 0;

    protected $unchangedCount = 0;

    protected $skippedNoMatchCount = 0;

    protected $skippedNoCategoryCount = 0;

    protected $skippedLowConfidenceCount = 0;

    protected $missingTargetCategoriesCount = 0;

    protected $failedRowsCount = 0;

    // Transition statistics tracker: [current_name => [target_name => count]]
    protected $transitions = [];

    // Manual mapping dictionary for known slugs
    protected $manualDictionary = [
        'nam-khoa' => 'nam-khoa',
        'phu-khoa' => 'phu-khoa',
        'ngoai-khoa' => 'ngoai-khoa',
        'benh-xa-hoi' => 'benh-xa-hoi',
        'xet-nghiem' => 'xet-nghiem',
        'vi-cong-dong' => 'vi-cong-dong',
        'bao-quy-dau' => 'bao-quy-dau',
        'cat-bao-quy-dau' => 'cat-bao-quy-dau',
        'viem-bao-quy-dau' => 'viem-bao-quy-dau',
        'hep-bao-quy-dau' => 'hep-bao-quy-dau',
        'dai-bao-quy-dau' => 'dai-bao-quy-dau',
        'tinh-trung-yeu' => 'tinh-trung-yeu',
        'xuat-tinh-som' => 'xuat-tinh-som',
        'yeu-sinh-ly' => 'yeu-sinh-ly',
        'benh-tinh-hoan' => 'benh-tinh-hoan',
        'u-xo-tu-cung' => 'u-xo-tu-cung',
        'viem-phu-khoa' => 'viem-phu-khoa',
        'roi-loan-kinh-nguyet' => 'roi-loan-kinh-nguyet',
        'ke-hoach-hoa-gia-dinh' => 'ke-hoach-hoa-gia-dinh',
        'sui-mao-ga' => 'sui-mao-ga',
        'benh-lau' => 'benh-lau',
        'giang-mai' => 'giang-mai',
        'benh-tri' => 'benh-tri',
        'ap-xe-hau-mon' => 'ap-xe-hau-mon',
        'nut-ke-hau-mon' => 'nut-ke-hau-mon',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $slugFilter = $this->option('slug');
        $oldPostIdFilter = $this->option('old-post-id') ? (int) $this->option('old-post-id') : null;
        $onlyWrong = $this->option('only-wrong');
        $minConfidence = $this->option('confidence') ?: 'high';
        $force = $this->option('force');

        $this->info('=== Starting Article Category Remapping ===');
        if ($dryRun) {
            $this->warn('!!! RUNNING IN DRY-RUN MODE (No database updates will occur) !!!');
        }

        // Initialize log files
        $logFile = storage_path('logs/article-category-remap.log');
        $reviewCsvFile = storage_path('logs/article-category-remap-review.csv');

        $headerText = sprintf("=== Category Remap Started at %s (Dry Run: %s, Limit: %s, Slug: %s, OldPostID: %s, Confidence: %s) ===\n",
            date('Y-m-d H:i:s'),
            $dryRun ? 'YES' : 'NO',
            $limit ?? 'NONE',
            $slugFilter ?? 'NONE',
            $oldPostIdFilter ?? 'NONE',
            $minConfidence
        );
        File::put($logFile, $headerText);

        // Put CSV Header
        $csvHeader = "old_post_id,new_article_id,slug,title,current_category,proposed_category,confidence,reason\n";
        File::put($reviewCsvFile, $csvHeader);

        // Connection Check
        try {
            DB::connection('old_mysql')->getPdo();
            $this->info('Connected successfully to old WordPress database.');
        } catch (\Exception $e) {
            $this->error("Failed to connect to 'old_mysql' connection. Error: ".$e->getMessage());
            $this->logAction('SYSTEM', 'N/A', 'N/A', 'DB_CONN_FAIL', null, $e->getMessage());

            return 1;
        }

        // Safe Confirmation before full real run
        $isFullRealRun = (! $dryRun && ! $limit && ! $slugFilter && ! $oldPostIdFilter);
        if ($isFullRealRun && ! $force) {
            $confirmed = $this->confirm('Do you really want to run the FULL category remapping? This will modify article records in the database.', false);
            if (! $confirmed) {
                $this->error('Command aborted by user.');

                return 0;
            }
        }

        // Database Backup (only if NOT dry-run)
        if (! $dryRun) {
            if ($this->runBackup() !== 0) {
                $this->error('Halting migration: Backup failed. Database safety is a critical priority.');

                return 1;
            }
        }

        // Step 1: Fetch and index Target Categories
        $targetCategories = Category::all();
        $targetCategoriesBySlug = $targetCategories->keyBy('slug');
        $targetCategoriesByName = $targetCategories->keyBy(function ($cat) {
            return $this->normalizeString($cat->name);
        });

        // Step 2: Fetch and build old WP Category Hierarchy depth
        $this->info('Loading WordPress categories hierarchy...');
        try {
            $wpCategories = DB::connection('old_mysql')
                ->table('bqtdbhah0_terms as t')
                ->join('bqtdbhah0_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
                ->where('tt.taxonomy', 'category')
                ->select('t.term_id', 't.name', 't.slug', 'tt.parent')
                ->get();
        } catch (\Exception $e) {
            $this->error('Failed to fetch old WordPress categories: '.$e->getMessage());

            return 1;
        }

        $wpCategoriesById = [];
        foreach ($wpCategories as $wpCat) {
            $wpCategoriesById[$wpCat->term_id] = [
                'term_id' => $wpCat->term_id,
                'name' => $wpCat->name,
                'slug' => $wpCat->slug,
                'parent' => (int) $wpCat->parent,
                'depth' => -1,
            ];
        }

        // Depth analyzer helper
        $computeDepth = function ($termId) use (&$wpCategoriesById, &$computeDepth) {
            if (! isset($wpCategoriesById[$termId])) {
                return 0;
            }
            if ($wpCategoriesById[$termId]['depth'] !== -1) {
                return $wpCategoriesById[$termId]['depth'];
            }
            if ($wpCategoriesById[$termId]['parent'] === 0) {
                $wpCategoriesById[$termId]['depth'] = 0;

                return 0;
            }
            $parentDepth = $computeDepth($wpCategoriesById[$termId]['parent']);
            $wpCategoriesById[$termId]['depth'] = 1 + $parentDepth;

            return $wpCategoriesById[$termId]['depth'];
        };

        foreach ($wpCategoriesById as $termId => $cat) {
            $computeDepth($termId);
        }

        // Step 3: Fetch WordPress posts based on filters
        $this->info('Querying WordPress posts...');
        $postsQuery = DB::connection('old_mysql')
            ->table('bqtdbhah0_posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->orderBy('ID');

        if ($oldPostIdFilter) {
            $postsQuery->where('ID', $oldPostIdFilter);
        }

        $wpPosts = $postsQuery->get();
        $this->totalOldPosts = $wpPosts->count();
        $this->info("Found {$this->totalOldPosts} published WordPress posts.");

        // Bulk load term relationships
        $postIds = $wpPosts->pluck('ID')->toArray();
        $wpRelationships = DB::connection('old_mysql')
            ->table('bqtdbhah0_term_relationships as tr')
            ->join('bqtdbhah0_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->whereIn('tr.object_id', $postIds)
            ->where('tt.taxonomy', 'category')
            ->select('tr.object_id', 'tt.term_id')
            ->get()
            ->groupBy('object_id');

        $this->info('Remapping articles...');
        $progressBar = $this->output->createProgressBar($wpPosts->count());
        $progressBar->start();

        // Keep track of dry run proposals to print in the terminal
        $dryRunProposals = [];

        foreach ($wpPosts as $post) {
            if ($limit && ($this->matchedArticlesCount + $this->skippedNoMatchCount) >= $limit) {
                break;
            }

            $wpTitle = $post->post_title;
            $wpSlug = $post->post_name;

            // Target Article Match Resolution
            $targetArticle = null;

            // Priority 1: Match by slug
            if ($wpSlug) {
                if ($slugFilter && $wpSlug !== $slugFilter) {
                    // Skip if slug filter is active and doesn't match
                    $progressBar->advance();

                    continue;
                }
                $targetArticle = Article::where('slug', $wpSlug)->first();
            }

            // Priority 2: Match by normalized title
            if (! $targetArticle && $wpTitle) {
                $normalizedWpTitle = $this->normalizeString($wpTitle);

                // We'll query target articles with similar titles
                $targetArticles = Article::all(); // load in memory for matching since count is small
                foreach ($targetArticles as $art) {
                    if ($this->normalizeString($art->title) === $normalizedWpTitle) {
                        if ($slugFilter && $art->slug !== $slugFilter) {
                            continue;
                        }
                        $targetArticle = $art;
                        break;
                    }
                }
            }

            if (! $targetArticle) {
                $this->logAction($post->ID, $wpSlug, $wpTitle, 'skipped_no_match', null, 'Target article not found in new database.');
                $this->skippedNoMatchCount++;
                $progressBar->advance();

                continue;
            }

            // Article is matched
            $this->matchedArticlesCount++;

            $currentCategoryId = $targetArticle->category_id;
            $currentCategoryName = $targetArticle->category ? $targetArticle->category->name : 'N/A';

            // Resolve Category mapping from WordPress relationships
            $resolvedTargetCategoryId = null;
            $confidence = 'low';
            $selectedWpCategoryName = 'None';
            $reason = 'No categories found in old post';

            $associatedWpTermIds = isset($wpRelationships[$post->ID]) ? $wpRelationships[$post->ID]->pluck('term_id')->toArray() : [];

            if (! empty($associatedWpTermIds)) {
                $validCats = [];
                foreach ($associatedWpTermIds as $termId) {
                    if (isset($wpCategoriesById[$termId])) {
                        $validCats[] = $wpCategoriesById[$termId];
                    }
                }

                if (! empty($validCats)) {
                    // Sort by depth descending, tie-break by title keyword match strength, then by term_id
                    usort($validCats, function ($a, $b) use ($wpTitle, $wpSlug) {
                        if ($b['depth'] !== $a['depth']) {
                            return $b['depth'] <=> $a['depth'];
                        }
                        // tie break check
                        $aNormName = $this->normalizeString($a['name']);
                        $bNormName = $this->normalizeString($b['name']);

                        $titleNorm = $this->normalizeString($wpTitle);
                        $slugNorm = str_replace('-', ' ', $this->normalizeString($wpSlug));

                        $aMatch = (str_contains($titleNorm, $aNormName) || str_contains($slugNorm, $aNormName));
                        $bMatch = (str_contains($titleNorm, $bNormName) || str_contains($slugNorm, $bNormName));

                        if ($aMatch && ! $bMatch) {
                            return -1;
                        }
                        if (! $aMatch && $bMatch) {
                            return 1;
                        }

                        return $a['term_id'] <=> $b['term_id'];
                    });

                    $selectedWpCat = $validCats[0];
                    $selectedWpCategoryName = $selectedWpCat['name'];
                    $selectedWpSlug = $selectedWpCat['slug'];

                    // Target matching resolution prioritization order:
                    // 1. Exact slug match
                    if (isset($targetCategoriesBySlug[$selectedWpSlug])) {
                        $resolvedTargetCategoryId = $targetCategoriesBySlug[$selectedWpSlug]->id;
                        $confidence = 'high';
                        $reason = 'Matched old category slug directly';
                    }
                    // 2. Normalized slug match
                    if (! $resolvedTargetCategoryId) {
                        $normalizedWpSlug = $this->normalizeString(str_replace('-', ' ', $selectedWpSlug));
                        foreach ($targetCategories as $targetCat) {
                            $normTargetSlug = $this->normalizeString(str_replace('-', ' ', $targetCat->slug));
                            if ($normTargetSlug === $normalizedWpSlug) {
                                $resolvedTargetCategoryId = $targetCat->id;
                                $confidence = 'high';
                                $reason = 'Matched normalized old category slug';
                                break;
                            }
                        }
                    }
                    // 3. Exact Vietnamese name match
                    if (! $resolvedTargetCategoryId) {
                        foreach ($targetCategories as $targetCat) {
                            if ($targetCat->name === $selectedWpCategoryName) {
                                $resolvedTargetCategoryId = $targetCat->id;
                                $confidence = 'high';
                                $reason = 'Matched old category name exactly';
                                break;
                            }
                        }
                    }
                    // 4. Normalized Vietnamese name match
                    if (! $resolvedTargetCategoryId) {
                        $normWpCatName = $this->normalizeString($selectedWpCategoryName);
                        if (isset($targetCategoriesByName[$normWpCatName])) {
                            $resolvedTargetCategoryId = $targetCategoriesByName[$normWpCatName]->id;
                            $confidence = 'high';
                            $reason = 'Matched normalized category name';
                        }
                    }
                    // 5. Manual dictionary mapping
                    if (! $resolvedTargetCategoryId && isset($this->manualDictionary[$selectedWpSlug])) {
                        $mappedSlug = $this->manualDictionary[$selectedWpSlug];
                        if (isset($targetCategoriesBySlug[$mappedSlug])) {
                            $resolvedTargetCategoryId = $targetCategoriesBySlug[$mappedSlug]->id;
                            $confidence = 'high';
                            $reason = 'Matched via manual category mapping dictionary';
                        }
                    }
                }
            }

            // Keyword Fallback matching if old category resolution failed, is root, or vague
            $isRootOrVague = false;
            if ($resolvedTargetCategoryId) {
                $targetCatInstance = Category::find($resolvedTargetCategoryId);
                if ($targetCatInstance && $targetCatInstance->parent_id === -1) {
                    $isRootOrVague = true; // mapped to a root category, check if we can get a specific child via keywords
                }
            }

            if (! $resolvedTargetCategoryId || $isRootOrVague) {
                // Apply keyword-based fallback rules
                $fallbackResult = $this->resolveCategoryByKeywords($wpTitle, $wpSlug, $targetCategoriesBySlug);
                if ($fallbackResult) {
                    $resolvedTargetCategoryId = $fallbackResult['category_id'];
                    $confidence = $fallbackResult['confidence'];
                    $reason = $fallbackResult['reason'];
                }
            }

            // Skip low-confidence matches or non-matched if threshold is met
            $minConfidenceLevel = $minConfidence === 'high' ? 2 : 1;
            $currentConfidenceLevel = $confidence === 'high' ? 2 : ($confidence === 'medium' ? 1 : 0);

            if ($resolvedTargetCategoryId === null || $currentConfidenceLevel < $minConfidenceLevel) {
                $this->logAction($post->ID, $wpSlug, $wpTitle, 'skipped_low_confidence', $resolvedTargetCategoryId, "Confidence: {$confidence}. Reason: {$reason}");
                $this->logToReviewCsv($post->ID, $targetArticle->id, $wpSlug, $wpTitle, $currentCategoryName, $resolvedTargetCategoryId ? Category::find($resolvedTargetCategoryId)->name : 'Unmapped', $confidence, $reason);
                $this->skippedLowConfidenceCount++;
                $progressBar->advance();

                continue;
            }

            $targetCategoryInstance = Category::find($resolvedTargetCategoryId);
            if (! $targetCategoryInstance) {
                $this->logAction($post->ID, $wpSlug, $wpTitle, 'missing_target_category', $resolvedTargetCategoryId, 'Target Category ID does not exist in target DB.');
                $this->missingTargetCategoriesCount++;
                $progressBar->advance();

                continue;
            }

            $targetCategoryName = $targetCategoryInstance->name;

            // Apply updates
            if ($currentCategoryId === $resolvedTargetCategoryId) {
                $this->logAction($post->ID, $wpSlug, $wpTitle, 'unchanged', $resolvedTargetCategoryId, "Category is already correct: {$targetCategoryName}");
                $this->unchangedCount++;
            } else {
                // Category needs to change!
                if ($onlyWrong && $currentCategoryId === $resolvedTargetCategoryId) {
                    // Do nothing since they are same
                }

                // Transition statistics tracking
                if (! isset($this->transitions[$currentCategoryName])) {
                    $this->transitions[$currentCategoryName] = [];
                }
                if (! isset($this->transitions[$currentCategoryName][$targetCategoryName])) {
                    $this->transitions[$currentCategoryName][$targetCategoryName] = 0;
                }
                $this->transitions[$currentCategoryName][$targetCategoryName]++;

                if ($dryRun) {
                    $proposalLine = sprintf(
                        '[DRY-RUN] current: %s -> target: %s | slug: %s | confidence: %s',
                        $currentCategoryName,
                        $targetCategoryName,
                        $wpSlug,
                        $confidence
                    );
                    $this->logAction($post->ID, $wpSlug, $wpTitle, 'dry-run-change', $resolvedTargetCategoryId, "Proposed: {$currentCategoryName} -> {$targetCategoryName}. Reason: {$reason}");

                    if (count($dryRunProposals) < 20) {
                        $dryRunProposals[] = $proposalLine;
                    }
                    $this->changedCount++;
                } else {
                    try {
                        $targetArticle->category_id = $resolvedTargetCategoryId;
                        $targetArticle->save();

                        $this->logAction($post->ID, $wpSlug, $wpTitle, 'changed', $resolvedTargetCategoryId, "Remapped: {$currentCategoryName} -> {$targetCategoryName}. Reason: {$reason}");
                        $this->changedCount++;
                    } catch (\Exception $e) {
                        $this->logAction($post->ID, $wpSlug, $wpTitle, 'failed_update', $resolvedTargetCategoryId, $e->getMessage());
                        $this->failedRowsCount++;
                    }
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Print Dry Run proposals if available
        if ($dryRun && ! empty($dryRunProposals)) {
            $this->info('--- Top 20 Proposed High-Confidence Changes (Dry Run Preview) ---');
            foreach ($dryRunProposals as $proposal) {
                $this->line($proposal);
            }
            $this->newLine();
        }

        // Post-migration clearing of cache & optimize files
        if (! $dryRun && $this->changedCount > 0) {
            $this->info('Clearing caches and optimizations...');
            try {
                $this->call('optimize:clear');
                $this->call('cache:clear');
                $this->call('view:clear');
                $this->info('Caches cleared successfully.');
            } catch (\Exception $e) {
                $this->warn('Failed to clear caches: '.$e->getMessage());
            }
        }

        // Print final summary
        $this->info('=== Category Remap Summary ===');
        $summaryHeaders = ['Metric', 'Count'];
        $summaryRows = [
            ['Total WordPress Posts Checked', $this->totalOldPosts],
            ['Matched Laravel Articles', $this->matchedArticlesCount],
            ['Categories Changed / Updated', $this->changedCount],
            ['Categories Unchanged (Already correct)', $this->unchangedCount],
            ['Skipped (No match in target DB)', $this->skippedNoMatchCount],
            ['Skipped (Vague or missing category)', $this->skippedNoCategoryCount],
            ['Skipped (Low confidence for manual review)', $this->skippedLowConfidenceCount],
            ['Missing Target Category Errors', $this->missingTargetCategoriesCount],
            ['Failed Updates', $this->failedRowsCount],
        ];
        $this->table($summaryHeaders, $summaryRows);

        // Display transition stats
        if (! empty($this->transitions)) {
            $this->newLine();
            $this->info('=== Category Transitions Map ===');
            $transitionHeaders = ['From Category', 'To Category', 'Count'];
            $transitionRows = [];
            foreach ($this->transitions as $fromCat => $toCats) {
                foreach ($toCats as $toCat => $cnt) {
                    $transitionRows[] = [$fromCat, $toCat, $cnt];
                }
            }
            $this->table($transitionHeaders, $transitionRows);
        }

        $this->info('Audit log written to: storage/logs/article-category-remap.log');
        $this->info('Review CSV list written to: storage/logs/article-category-remap-review.csv');
        $this->info('=== Category Remap Process Completed ===');

        return 0;
    }

    /**
     * Run safe timestamped database backup.
     */
    protected function runBackup(): int
    {
        $backupDir = storage_path('backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Ymd_His');
        $backupFile = $backupDir."/dakhoacantho_web_before_category_remap_{$timestamp}.sql";

        $mysqlDumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
        if (! File::exists($mysqlDumpPath)) {
            $mysqlDumpPath = 'mysqldump'; // fallback
        }

        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'dakhoacantho_web');
        $dbUser = config('database.connections.mysql.username', 'root');
        $dbPass = config('database.connections.mysql.password', '');

        $passwordOption = $dbPass !== '' ? '-p'.escapeshellarg($dbPass) : '';

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

        $this->info("Creating timestamped database backup: {$backupFile}");

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
     * Helper to resolve Category by Keywords in the article's title/slug.
     * Specific child categories are prioritized over generic root categories.
     */
    protected function resolveCategoryByKeywords(string $title, string $slug, $targetCategoriesBySlug): ?array
    {
        $normTitle = $this->normalizeString($title);
        $normSlug = str_replace('-', ' ', $this->normalizeString($slug));

        // Group definitions: keyword patterns mapping to target slug, ordered from most specific to least specific
        $rules = [
            // Cắt Bao Quy Đầu (high priority child)
            [
                'keywords' => ['cat bao quy dau', 'cắt bao quy đầu', 'phau thuat bao quy dau', 'phẫu thuật bao quy đầu'],
                'target' => 'cat-bao-quy-dau',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Cắt Bao Quy Đầu action',
            ],
            // Viêm Bao Quy Đầu
            [
                'keywords' => ['viem bao quy dau', 'viêm bao quy đầu'],
                'target' => 'viem-bao-quy-dau',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Viêm Bao Quy Đầu pathology',
            ],
            // Hẹp Bao Quy Đầu
            [
                'keywords' => ['hep bao quy dau', 'hẹp bao quy đầu'],
                'target' => 'hep-bao-quy-dau',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Hẹp Bao Quy Đầu pathology',
            ],
            // Dài Bao Quy Đầu
            [
                'keywords' => ['dai bao quy dau', 'dài bao quy đầu'],
                'target' => 'dai-bao-quy-dau',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Dài Bao Quy Đầu pathology',
            ],
            // Bao Quy Đầu (general child)
            [
                'keywords' => ['bao quy dau', 'bao quy đầu'],
                'target' => 'bao-quy-dau',
                'confidence' => 'medium',
                'reason' => 'Keyword matched general Bao Quy Đầu',
            ],
            // Xuất Tinh Sớm
            [
                'keywords' => ['xuat tinh som', 'xuất tinh sớm'],
                'target' => 'xuat-tinh-som',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Xuất Tinh Sớm symptom',
            ],
            // Tinh Trùng Yếu
            [
                'keywords' => ['tinh trung yeu', 'tinh trùng yếu', 'tinh trung loang', 'tinh trùng loãng'],
                'target' => 'tinh-trung-yeu',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Tinh Trùng Yếu symptom',
            ],
            // Yếu Sinh Lý
            [
                'keywords' => ['yeu sinh ly', 'yếu sinh lý', 'cuong duong', 'cương dương', 'liet duong', 'liệt dương'],
                'target' => 'yeu-sinh-ly',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Yếu Sinh Lý pathology',
            ],
            // Bệnh Tinh Hoàn
            [
                'keywords' => ['tinh hoan', 'tinh hoàn', 'mao tinh', 'mào tinh', 'uot biu', 'ướt bìu'],
                'target' => 'benh-tinh-hoan',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Bệnh Tinh Hoàn pathology',
            ],
            // U Xơ Tử Cung
            [
                'keywords' => ['u xo tu cung', 'u xơ tử cung', 'u nang buong trung', 'u nang buồng trứng'],
                'target' => 'u-xo-tu-cung',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific U Xơ Tử Cung / Buồng Trứng pathology',
            ],
            // Viêm Phụ Khoa
            [
                'keywords' => ['viem phu khoa', 'viêm phụ khoa', 'viem am dao', 'viêm âm đạo', 'viem am ho', 'viêm âm hộ', 'viem lo tuyen', 'viêm lộ tuyến', 'viem co tu cung', 'viêm cổ tử cung'],
                'target' => 'viem-phu-khoa',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Viêm Phụ Khoa pathology',
            ],
            // Rối Loạn Kinh Nguyệt
            [
                'keywords' => ['roi loan kinh nguyet', 'rối loạn kinh nguyệt', 'cham kinh', 'trễ kinh', 'rong kinh', 'kinh nguyet', 'kinh nguyệt', 'dau bung kinh', 'đau bụng kinh'],
                'target' => 'roi-loan-kinh-nguyet',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Rối Loạn Kinh Nguyệt pathology',
            ],
            // Sùi Mào Gà
            [
                'keywords' => ['sui mao ga', 'sùi mào gà', 'hpv'],
                'target' => 'sui-mao-ga',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Sùi Mào Gà disease',
            ],
            // Bệnh Lậu
            [
                'keywords' => ['benh lau', 'bệnh lậu'],
                'target' => 'benh-lau',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Bệnh Lậu disease',
            ],
            // Giang Mai
            [
                'keywords' => ['giang mai', 'syphilis'],
                'target' => 'giang-mai',
                'confidence' => 'high',
                'reason' => 'Keyword matched specific Giang Mai disease',
            ],
            // Xét Nghiệm
            [
                'keywords' => ['xet nghiem', 'xét nghiệm', 'kiem tra suc khoe', 'kiểm tra sức khỏe', 'kham suc khoe', 'khám sức khỏe'],
                'target' => 'xet-nghiem',
                'confidence' => 'high',
                'reason' => 'Keyword matched Xét Nghiệm action',
            ],
            // general fallbacks to roots:
            [
                'keywords' => ['nam khoa', 'nam giới', 'nam gioi', 'dan ong', 'đàn ông'],
                'target' => 'nam-khoa',
                'confidence' => 'medium',
                'reason' => 'Keyword matched general Nam Khoa root',
            ],
            [
                'keywords' => ['phu khoa', 'phụ khoa', 'phu nu', 'phụ nữ', 'chi em', 'chị em', 'pha thai', 'phá thai', 'dinh chi thai', 'đình chỉ thai'],
                'target' => 'phu-khoa',
                'confidence' => 'medium',
                'reason' => 'Keyword matched general Phụ Khoa root',
            ],
            [
                'keywords' => ['benh xa hoi', 'bệnh xã hội', 'lay truyen', 'lây truyền'],
                'target' => 'benh-xa-hoi',
                'confidence' => 'medium',
                'reason' => 'Keyword matched general Bệnh Xã Hội root',
            ],
        ];

        foreach ($rules as $rule) {
            foreach ($rule['keywords'] as $kw) {
                if (str_contains($normTitle, $kw) || str_contains($normSlug, $kw)) {
                    $slugTarget = $rule['target'];
                    if (isset($targetCategoriesBySlug[$slugTarget])) {
                        return [
                            'category_id' => $targetCategoriesBySlug[$slugTarget]->id,
                            'confidence' => $rule['confidence'],
                            'reason' => $rule['reason'],
                        ];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Normalize string for comparison.
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
     * Log helper for remapping trace.
     */
    protected function logAction($sourceId, $slug, $title, $action, $targetId = null, $message = null)
    {
        $logFile = storage_path('logs/article-category-remap.log');
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

    /**
     * Log helper for manual review CSV.
     */
    protected function logToReviewCsv($oldPostId, $newArticleId, $slug, $title, $currentCategory, $proposedCategory, $confidence, $reason)
    {
        $reviewCsvFile = storage_path('logs/article-category-remap-review.csv');

        // Escape CSV values
        $escapeCsv = function ($val) {
            $val = str_replace('"', '""', $val);
            if (str_contains($val, ',') || str_contains($val, '"') || str_contains($val, "\n")) {
                return '"'.$val.'"';
            }

            return $val;
        };

        $line = sprintf(
            "%s,%s,%s,%s,%s,%s,%s,%s\n",
            $oldPostId,
            $newArticleId,
            $escapeCsv($slug),
            $escapeCsv($title),
            $escapeCsv($currentCategory),
            $escapeCsv($proposedCategory),
            $confidence,
            $escapeCsv($reason)
        );

        File::append($reviewCsvFile, $line);
    }
}
