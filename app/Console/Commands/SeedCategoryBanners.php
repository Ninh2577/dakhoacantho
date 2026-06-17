<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedCategoryBanners extends Command
{
    protected $signature = 'app:seed-category-banners';

    protected $description = 'Download and set featured images for top-level categories';

    public function handle()
    {
        // Define relevant free image URLs for medical categories
        $imageMap = [
            'nam-khoa' => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?q=80&w=800&auto=format&fit=crop',
            'phu-khoa' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?q=80&w=800&auto=format&fit=crop',
            'ngoai-khoa' => 'https://images.unsplash.com/photo-1551076805-e1869033e561?q=80&w=800&auto=format&fit=crop',
            'benh-xa-hoi' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?q=80&w=800&auto=format&fit=crop',
            'xet-nghiem' => 'https://images.unsplash.com/photo-1579154204601-01588f351e67?q=80&w=800&auto=format&fit=crop',
            'vi-cong-dong' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=800&auto=format&fit=crop',
        ];

        $genericUrl = 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?q=80&w=800&auto=format&fit=crop';

        // Ensure directory exists
        if (! Storage::disk('public')->exists('category-banners')) {
            Storage::disk('public')->makeDirectory('category-banners');
        }

        $topCategories = Category::where('parent_id', -1)->get();

        if ($topCategories->isEmpty()) {
            $this->warn('No top-level categories found. Aborting.');

            return self::SUCCESS;
        }

        $this->info("Found {$topCategories->count()} top-level categories. Starting image download...");
        $this->newLine();

        foreach ($topCategories as $category) {
            $url = $imageMap[$category->slug] ?? $genericUrl;
            $label = isset($imageMap[$category->slug]) ? 'mapped' : 'generic fallback';

            $this->line("→ [{$label}] Downloading image for <comment>{$category->name}</comment> (slug: {$category->slug})...");

            try {
                $response = Http::timeout(30)->get($url);

                if ($response->successful()) {
                    $filename = 'category-banners/'.Str::random(10).'.jpg';
                    Storage::disk('public')->put($filename, $response->body());
                    $category->update(['featured_image' => $filename]);
                    $this->info("  ✓ Done → saved as {$filename}");
                } else {
                    $this->error("  ✗ HTTP {$response->status()} received for {$category->name}");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Exception for {$category->name}: ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->info('✅ Finished seeding category banners!');

        return self::SUCCESS;
    }
}
