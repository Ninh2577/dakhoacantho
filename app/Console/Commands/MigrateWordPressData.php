<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Article;

class MigrateWordPressData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:wordpress-data {--truncate : Truncate categories and articles tables before migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate WordPress categories, posts, and media from local WordPress site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Starting WordPress Migration ===");

        // Step 0: Check Truncation Option
        if ($this->option('truncate')) {
            $this->info("Truncating categories and articles tables...");
            Schema::disableForeignKeyConstraints();
            Article::truncate();
            Category::truncate();
            Schema::enableForeignKeyConstraints();
            $this->info("Target tables truncated successfully.");
        }

        // Step 1: Copy Media/Uploads folder
        $srcUploadsDir = 'C:\xampp\htdocs\dakhoacantho\wp-content\uploads';
        $dstUploadsDir = storage_path('app/public/uploads');

        if (!File::exists($srcUploadsDir)) {
            $this->error("WordPress uploads directory not found at: {$srcUploadsDir}");
            return 1;
        }

        $this->info("Copying WordPress media uploads to Laravel public storage...");
        if (!File::exists($dstUploadsDir)) {
            File::makeDirectory($dstUploadsDir, 0755, true);
        }
        File::copyDirectory($srcUploadsDir, $dstUploadsDir);
        $this->info("Media copy completed successfully.");

        // Step 2: Ensure Default Category exists
        $defaultCategory = Category::firstOrCreate(
            ['slug' => 'uncategorized'],
            [
                'name' => 'Chưa phân loại',
                'description' => 'Các bài viết chưa được phân loại',
            ]
        );

        // Step 3: Migrate Categories
        $this->info("Fetching categories from WordPress database...");
        $wpCategories = DB::connection('wordpress')
            ->table('bqtdbhah0_terms as t')
            ->join('bqtdbhah0_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'category')
            ->select('t.term_id', 't.name', 't.slug', 'tt.description')
            ->get();

        $this->info("Migrating " . $wpCategories->count() . " categories...");
        $categoryBar = $this->output->createProgressBar($wpCategories->count());
        $categoryBar->start();

        foreach ($wpCategories as $wpCat) {
            Category::updateOrCreate(
                ['id' => $wpCat->term_id],
                [
                    'name' => $wpCat->name,
                    'slug' => $wpCat->slug ?: Str::slug($wpCat->name),
                    'description' => $wpCat->description ?: null,
                ]
            );
            $categoryBar->advance();
        }
        $categoryBar->finish();
        $this->newLine();
        $this->info("Categories migration completed!");

        // Step 4: Migrate Articles (Posts)
        $this->info("Fetching published posts from WordPress database...");
        $wpPosts = DB::connection('wordpress')
            ->table('bqtdbhah0_posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->get();

        if ($wpPosts->isEmpty()) {
            $this->warn("No posts found in the WordPress database to migrate.");
            return 0;
        }

        $wpPostIds = $wpPosts->pluck('ID')->toArray();

        $this->info("Fetching relationships and metadata in bulk...");
        
        // Fetch category relationships
        $relationships = DB::connection('wordpress')
            ->table('bqtdbhah0_term_relationships as tr')
            ->join('bqtdbhah0_term_taxonomy as tt', 'tr.term_taxonomy_id', '=', 'tt.term_taxonomy_id')
            ->whereIn('tr.object_id', $wpPostIds)
            ->where('tt.taxonomy', 'category')
            ->select('tr.object_id', 'tt.term_id')
            ->get()
            ->groupBy('object_id');

        // Fetch post metadata (SEO & Thumbnails)
        $postmeta = DB::connection('wordpress')
            ->table('bqtdbhah0_postmeta')
            ->whereIn('post_id', $wpPostIds)
            ->whereIn('meta_key', [
                '_yoast_wpseo_title',
                '_yoast_wpseo_metadesc',
                'rank_math_title',
                'rank_math_description',
                '_thumbnail_id'
            ])
            ->get()
            ->groupBy('post_id');

        // Collect thumbnail IDs to fetch their attachment file paths
        $thumbnailIds = $postmeta->flatMap(function ($metaItems) {
            return $metaItems->where('meta_key', '_thumbnail_id')->pluck('meta_value');
        })->filter()->unique()->toArray();

        $attachmentPaths = [];
        if (!empty($thumbnailIds)) {
            $attachmentPaths = DB::connection('wordpress')
                ->table('bqtdbhah0_postmeta')
                ->whereIn('post_id', $thumbnailIds)
                ->where('meta_key', '_wp_attached_file')
                ->pluck('meta_value', 'post_id')
                ->toArray();
        }

        $this->info("Migrating " . $wpPosts->count() . " articles...");
        $articleBar = $this->output->createProgressBar($wpPosts->count());
        $articleBar->start();

        foreach ($wpPosts as $post) {
            // Determine category
            $postCategoryIds = $relationships->get($post->ID);
            $categoryId = $defaultCategory->id;
            if ($postCategoryIds && $postCategoryIds->isNotEmpty()) {
                // Find first valid category id
                foreach ($postCategoryIds as $rel) {
                    if (Category::where('id', $rel->term_id)->exists()) {
                        $categoryId = $rel->term_id;
                        break;
                    }
                }
            }

            // Get post meta
            $meta = $postmeta->get($post->ID) ?: collect();
            
            // SEO Title
            $metaTitle = null;
            $yoastTitle = $meta->where('meta_key', '_yoast_wpseo_title')->first();
            $rankMathTitle = $meta->where('meta_key', 'rank_math_title')->first();
            if ($yoastTitle && $yoastTitle->meta_value) {
                $metaTitle = $yoastTitle->meta_value;
            } elseif ($rankMathTitle && $rankMathTitle->meta_value) {
                $metaTitle = $rankMathTitle->meta_value;
            }
            
            // SEO Description
            $metaDescription = null;
            $yoastDesc = $meta->where('meta_key', '_yoast_wpseo_metadesc')->first();
            $rankMathDesc = $meta->where('meta_key', 'rank_math_description')->first();
            if ($yoastDesc && $yoastDesc->meta_value) {
                $metaDescription = $yoastDesc->meta_value;
            } elseif ($rankMathDesc && $rankMathDesc->meta_value) {
                $metaDescription = $rankMathDesc->meta_value;
            }

            // Resolve featured image (thumbnail)
            $thumbnailImage = null;
            $thumbnailMeta = $meta->where('meta_key', '_thumbnail_id')->first();
            if ($thumbnailMeta && $thumbnailMeta->meta_value) {
                $thumbPostId = $thumbnailMeta->meta_value;
                if (isset($attachmentPaths[$thumbPostId])) {
                    // WordPress attached file is typically '2021/05/filename.jpg'
                    // We map this to 'uploads/2021/05/filename.jpg'
                    $thumbnailImage = 'uploads/' . ltrim($attachmentPaths[$thumbPostId], '/\\');
                }
            }

            // Process article content: Replace wp-content/uploads/ with storage/uploads/
            $content = $post->post_content;
            
            // URL cleanup inside the post content
            $content = str_replace('http://localhost/dakhoacantho/wp-content/uploads/', '/storage/uploads/', $content);
            $content = str_replace('https://localhost/dakhoacantho/wp-content/uploads/', '/storage/uploads/', $content);
            $content = str_replace('/wp-content/uploads/', '/storage/uploads/', $content);
            $content = str_replace('wp-content/uploads/', 'storage/uploads/', $content);
            $content = str_replace('wp-content\\/uploads\\/', 'storage\\/uploads\\/', $content);

            Article::updateOrCreate(
                ['id' => $post->ID],
                [
                    'category_id' => $categoryId,
                    'title' => $post->post_title,
                    'slug' => $post->post_name ?: Str::slug($post->post_title),
                    'content' => $content ?: '',
                    'thumbnail_image' => $thumbnailImage,
                    'meta_title' => $metaTitle,
                    'meta_description' => $metaDescription,
                    'is_published' => true,
                    'created_at' => $post->post_date,
                    'updated_at' => $post->post_modified,
                ]
            );

            $articleBar->advance();
        }

        $articleBar->finish();
        $this->newLine();
        $this->info("Articles migration completed!");
        $this->info("=== WordPress Migration Finished Successfully! ===");

        return 0;
    }
}
