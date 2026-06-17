<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $this->info('=== Starting WordPress Migration ===');

        // Step 0: Check Truncation Option
        if ($this->option('truncate')) {
            $this->info('Truncating categories and articles tables...');
            Schema::disableForeignKeyConstraints();
            Article::truncate();
            Category::truncate();
            Schema::enableForeignKeyConstraints();
            $this->info('Target tables truncated successfully.');
        }

        // Step 1: Copy Media/Uploads folder
        $srcUploadsDir = 'C:\xampp\htdocs\dakhoacantho\wp-content\uploads';
        $dstUploadsDir = storage_path('app/public/uploads');

        if (! File::exists($srcUploadsDir)) {
            $this->error("WordPress uploads directory not found at: {$srcUploadsDir}");

            return 1;
        }

        $this->info('Copying WordPress media uploads to Laravel public storage...');
        if (! File::exists($dstUploadsDir)) {
            File::makeDirectory($dstUploadsDir, 0755, true);
        }
        File::copyDirectory($srcUploadsDir, $dstUploadsDir);
        $this->info('Media copy completed successfully.');

        // Step 2: Ensure Default Category exists
        $defaultCategory = Category::firstOrCreate(
            ['slug' => 'uncategorized'],
            [
                'name' => 'Chưa phân loại',
                'description' => 'Các bài viết chưa được phân loại',
            ]
        );

        // Step 3: Migrate Categories
        $this->info('Fetching categories from WordPress database...');
        $wpCategories = DB::connection('wordpress')
            ->table('bqtdbhah0_terms as t')
            ->join('bqtdbhah0_term_taxonomy as tt', 't.term_id', '=', 'tt.term_id')
            ->where('tt.taxonomy', 'category')
            ->select('t.term_id', 't.name', 't.slug', 'tt.description', 'tt.parent')
            ->get();

        $this->info('Migrating '.$wpCategories->count().' categories (Pass 1 - Basic Info)...');
        $categoryBar = $this->output->createProgressBar($wpCategories->count());
        $categoryBar->start();

        foreach ($wpCategories as $wpCat) {
            Category::updateOrCreate(
                ['id' => $wpCat->term_id],
                [
                    'name' => $wpCat->name,
                    'slug' => $wpCat->slug ?: Str::slug($wpCat->name),
                    'description' => $wpCat->description ?: null,
                    'parent_id' => null,
                ]
            );
            $categoryBar->advance();
        }
        $categoryBar->finish();
        $this->newLine();

        $this->info('Linking parent-child relationships (Pass 2 - Hierarchy)...');
        $hierarchyBar = $this->output->createProgressBar($wpCategories->count());
        $hierarchyBar->start();

        foreach ($wpCategories as $wpCat) {
            if ($wpCat->parent > 0) {
                if (Category::where('id', $wpCat->parent)->exists()) {
                    $category = Category::find($wpCat->term_id);
                    if ($category) {
                        $category->update(['parent_id' => $wpCat->parent]);
                    }
                }
            }
            $hierarchyBar->advance();
        }
        $hierarchyBar->finish();
        $this->newLine();
        $this->info('Categories migration completed!');

        // Step 4: Migrate Articles (Posts)
        $this->info('Fetching published posts from WordPress database...');
        $wpPosts = DB::connection('wordpress')
            ->table('bqtdbhah0_posts')
            ->where('post_type', 'post')
            ->where('post_status', 'publish')
            ->get();

        if ($wpPosts->isEmpty()) {
            $this->warn('No posts found in the WordPress database to migrate.');

            return 0;
        }

        $wpPostIds = $wpPosts->pluck('ID')->toArray();

        $this->info('Fetching relationships and metadata in bulk...');

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
                '_thumbnail_id',
            ])
            ->get()
            ->groupBy('post_id');

        // Collect thumbnail IDs to fetch their attachment file paths
        $thumbnailIds = $postmeta->flatMap(function ($metaItems) {
            return $metaItems->where('meta_key', '_thumbnail_id')->pluck('meta_value');
        })->filter()->unique()->toArray();

        $attachmentPaths = [];
        if (! empty($thumbnailIds)) {
            $attachmentPaths = DB::connection('wordpress')
                ->table('bqtdbhah0_postmeta')
                ->whereIn('post_id', $thumbnailIds)
                ->where('meta_key', '_wp_attached_file')
                ->pluck('meta_value', 'post_id')
                ->toArray();
        }

        $this->info('Migrating '.$wpPosts->count().' articles...');
        $articleBar = $this->output->createProgressBar($wpPosts->count());
        $articleBar->start();

        foreach ($wpPosts as $post) {
            // Determine category
            $postCategoryIds = $relationships->get($post->ID);
            $categoryId = $defaultCategory->id;
            if ($postCategoryIds && $postCategoryIds->isNotEmpty()) {
                $cats = Category::whereIn('id', $postCategoryIds->pluck('term_id'))->get();
                if ($cats->isNotEmpty()) {
                    // Try to find the deepest child category (which has a parent)
                    $childCat = $cats->whereNotNull('parent_id')->first();
                    if ($childCat) {
                        $categoryId = $childCat->id;
                    } else {
                        $categoryId = $cats->first()->id;
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
                    $thumbnailImage = 'uploads/'.ltrim($attachmentPaths[$thumbPostId], '/\\');
                }
            }

            // Process article content: Clean shortcodes and fix domain URLs
            $content = $post->post_content;

            // 1. Convert WordPress [caption] shortcodes into standard HTML <figure> tags
            $content = preg_replace_callback('/\[caption[^\]]*\](.*?)\[\/caption\]/is', function ($matches) {
                $innerContent = $matches[1];

                return '<figure class="wp-caption flex flex-col items-center justify-center my-6 p-2 bg-slate-50 border border-slate-100 rounded-2xl max-w-full mx-auto">'
                     .trim($innerContent)
                     .'</figure>';
            }, $content);

            // 2. Normalize and clean image paths
            $content = str_replace([
                'http://dakhoacantho.com/wp-content/uploads/',
                'https://dakhoacantho.com/wp-content/uploads/',
                'http://localhost/dakhoacantho/wp-content/uploads/',
                'https://localhost/dakhoacantho/wp-content/uploads/',
                '/wp-content/uploads/',
                'wp-content/uploads/',
                'wp-content\\/uploads\\/',
                'http://dakhoacantho.com/storage/uploads/',
                'https://dakhoacantho.com/storage/uploads/',
                'http://localhost/dakhoacantho/storage/uploads/',
                'https://localhost/dakhoacantho/storage/uploads/',
            ], [
                '/storage/uploads/',
                '/storage/uploads/',
                '/storage/uploads/',
                '/storage/uploads/',
                '/storage/uploads/',
                'storage/uploads/',
                'storage\\/uploads\\/',
                '/storage/uploads/',
                '/storage/uploads/',
                '/storage/uploads/',
                '/storage/uploads/',
            ], $content);

            // 3. Convert absolute internal domain links to root relative links
            $content = str_replace([
                'http://dakhoacantho.com/',
                'https://dakhoacantho.com/',
                'http://localhost/dakhoacantho/',
                'https://localhost/dakhoacantho/',
            ], [
                '/',
                '/',
                '/',
                '/',
            ], $content);

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
        $this->info('Articles migration completed!');
        $this->info('=== WordPress Migration Finished Successfully! ===');

        return 0;
    }
}
