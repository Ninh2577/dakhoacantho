<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MediaFile;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class SyncMediaFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing uploads directory with media_files table (optimized)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');

        $uploadsPath = storage_path('app/public/uploads');

        if (!is_dir($uploadsPath)) {
            $this->error("Uploads directory not found at: {$uploadsPath}");
            return 1;
        }

        $this->info("Loading existing media files path from database...");
        $existingPaths = MediaFile::pluck('file_path')->flip()->toArray();
        $this->info("Loaded " . count($existingPaths) . " existing files from database.");

        $this->info("Scanning uploads directory recursively...");

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadsPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        
        $batch = [];
        $batchSize = 500;
        $syncedCount = 0;
        $totalScanned = 0;
        $now = now();

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $totalScanned++;
            if ($totalScanned % 5000 === 0) {
                $this->info("Scanned {$totalScanned} files...");
            }

            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowedExtensions)) {
                continue;
            }

            $realPath = $file->getRealPath();
            $publicStoragePath = storage_path('app/public');
            
            // Normalize slashes for Windows
            $realPath = str_replace('\\', '/', $realPath);
            $publicStoragePath = str_replace('\\', '/', $publicStoragePath);
            
            $relativePath = str_replace($publicStoragePath . '/', '', $realPath);

            // O(1) in-memory check
            if (!isset($existingPaths[$relativePath])) {
                $mimeType = 'application/octet-stream';
                $mimeMap = [
                    'png' => 'image/png',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'xls' => 'application/vnd.ms-excel',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ];
                if (isset($mimeMap[$extension])) {
                    $mimeType = $mimeMap[$extension];
                }

                $batch[] = [
                    'name' => $file->getFilename(),
                    'file_path' => $relativePath,
                    'file_type' => $mimeType,
                    'file_size' => $file->getSize(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                // Prevent future duplicate in the same run
                $existingPaths[$relativePath] = true;

                if (count($batch) >= $batchSize) {
                    $inserted = MediaFile::insertOrIgnore($batch);
                    $syncedCount += $inserted;
                    $batch = [];
                }
            }
        }

        // Insert remaining batch
        if (count($batch) > 0) {
            $inserted = MediaFile::insertOrIgnore($batch);
            $syncedCount += $inserted;
        }

        // Clean up obsolete database records (files in DB but missing on disk)
        $this->info("Checking for obsolete media records in database...");
        $allDbFiles = MediaFile::select(['id', 'file_path'])->get();
        $obsoleteIds = [];
        $publicStoragePath = storage_path('app/public');
        
        foreach ($allDbFiles as $dbFile) {
            $fullPath = $publicStoragePath . '/' . $dbFile->file_path;
            if (!file_exists($fullPath)) {
                $obsoleteIds[] = $dbFile->id;
            }
        }
        
        $deletedCount = 0;
        if (count($obsoleteIds) > 0) {
            $this->info("Deleting " . count($obsoleteIds) . " obsolete database records...");
            $chunks = array_chunk($obsoleteIds, 1000);
            foreach ($chunks as $chunk) {
                $deletedCount += MediaFile::whereIn('id', $chunk)->delete();
            }
        }

        $this->info("Synchronization completed!");
        $this->info("Total scanned: {$totalScanned} files.");
        $this->info("Newly synced: {$syncedCount} files.");
        $this->info("Obsolete cleaned: {$deletedCount} files.");

        return 0;
    }
}
