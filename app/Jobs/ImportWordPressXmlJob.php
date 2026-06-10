<?php

namespace App\Jobs;

use App\Models\WordPressImportBatch;
use App\Services\WordPress\WxrParser;
use App\Services\WordPress\WordPressImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Exception;

class ImportWordPressXmlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    protected int $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $batchId)
    {
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     */
    public function handle(WxrParser $parser, WordPressImportService $importer): void
    {
        $batch = WordPressImportBatch::find($this->batchId);
        if (!$batch) {
            return;
        }

        $batch->update([
            'status'     => 'processing',
            'started_at' => now(),
        ]);

        $filePath = storage_path('app/' . $batch->file_path);
        if (!file_exists($filePath)) {
            $privatePath = storage_path('app/private/' . $batch->file_path);
            if (file_exists($privatePath)) {
                $filePath = $privatePath;
            }
        }

        if (!file_exists($filePath)) {
            $errorMsg = "Không tìm thấy tệp tin XML tại: " . $filePath;
            $batch->update([
                'status'        => 'failed',
                'finished_at'   => now(),
                'error_message' => $errorMsg,
            ]);
            $importer->logAction($batch->id, null, 'system', null, null, 'failed', 'error', $errorMsg);
            return;
        }

        try {
            // 1. Run Database Backup first (only for real runs)
            if (!$batch->dry_run) {
                $backupFile = null;
                $backupSuccess = $importer->runBackup($backupFile);
                if (!$backupSuccess) {
                    throw new Exception("Sao lưu cơ sở dữ liệu trước khi import thất bại! Hủy tiến trình để bảo vệ dữ liệu.");
                }
                $importer->logAction($batch->id, null, 'system', null, null, 'backup_success', 'success', 'Sao lưu cơ sở dữ liệu thành công tại: ' . basename($backupFile));
            }

            // 2. Detect namespaces dynamically
            $ns = WxrParser::detectNamespaces($filePath);
            $importer->logAction($batch->id, null, 'system', null, null, 'info', 'success', 'Đã phân tích XML namespaces.', $ns);

            // 3. Parse and Map Categories
            $wpCategories = $parser->parseCategories($filePath, $ns);
            $importer->logAction($batch->id, null, 'system', null, null, 'info', 'success', 'Tìm thấy ' . count($wpCategories) . ' danh mục trong tệp XML.');

            $slugToIdMap = [];
            foreach ($wpCategories as $slug => $wpCat) {
                // Determine target parent category ID
                $parentTargetId = null;
                $parentSlug = $wpCat['parent_slug'];
                if ($parentSlug && isset($slugToIdMap[$parentSlug])) {
                    $parentTargetId = $slugToIdMap[$parentSlug];
                }

                // Import category
                $targetId = $importer->importCategory($wpCat, $parentTargetId, $batch, $slugToIdMap);
                $slugToIdMap[$slug] = $targetId;
            }

            // 4. Parse Attachments (Media Map)
            $warningMsg = null;
            $attachmentMap = $parser->parseAttachments($filePath, $ns, $warningMsg);
            if ($warningMsg) {
                $importer->logAction($batch->id, null, 'system', null, null, 'warning', 'warning', $warningMsg);
            }
            $importer->logAction($batch->id, null, 'system', null, null, 'info', 'success', 'Đã tải bản đồ tệp đính kèm với ' . count($attachmentMap) . ' mục.');

            // 5. Stream and Import Posts / Pages
            $items = $parser->streamItems($filePath, $ns);
            
            // Calculate total items in WXR for tracking (quick pass or estimation)
            // Since streaming doesn't give size upfront, we do a quick count of <item> nodes in the file
            $totalCount = 0;
            $xmlReader = new \XMLReader();
            if ($xmlReader->open($filePath)) {
                while ($xmlReader->read()) {
                    if ($xmlReader->nodeType === \XMLReader::ELEMENT && $xmlReader->name === 'item') {
                        $totalCount++;
                    }
                }
                $xmlReader->close();
            }
            $batch->update(['total_items' => $totalCount]);

            $processedCount = 0;
            $limit = $batch->limit;

            foreach ($items as $item) {
                if ($item === null) {
                    continue;
                }

                if ($limit && $processedCount >= $limit) {
                    $importer->logAction($batch->id, null, 'system', null, null, 'info', 'success', "Đã dừng import do giới hạn số lượng bài viết: {$limit}");
                    break;
                }

                try {
                    // Check if it is a post or page before importing
                    $wpUri = $ns['wp'] ?? 'http://wordpress.org/export/1.2/';
                    $wpNs = $item->children($wpUri);
                    $postType = (string) $wpNs->post_type;

                    if (in_array($postType, $batch->import_post_types ?? [])) {
                        $importer->importItem($item, $ns, $batch, $slugToIdMap, $attachmentMap);
                        $processedCount++;
                    }
                } catch (\Throwable $e) {
                    Log::error("Failed to import WXR item ID {$postId}: " . $e->getMessage());
                    $importer->logAction(
                        $batch->id,
                        (string) ($wpNs->post_id ?? 'N/A'),
                        (string) ($wpNs->post_type ?? 'N/A'),
                        (string) ($wpNs->post_name ?? 'N/A'),
                        (string) ($item->title ?? 'N/A'),
                        'failed',
                        'error',
                        'Lỗi khi xử lý bài viết: ' . $e->getMessage()
                    );
                }
            }

            // Set final counts
            $batch->update([
                'status'      => 'completed',
                'finished_at' => now(),
            ]);

            $importer->logAction($batch->id, null, 'system', null, null, 'completed', 'success', 'Hoàn tất quá trình nhập dữ liệu WordPress.');

        } catch (\Throwable $e) {
            Log::error("WordPress Import Job failed: " . $e->getMessage());
            
            $batch->update([
                'status'        => 'failed',
                'finished_at'   => now(),
                'error_message' => $e->getMessage(),
            ]);

            $importer->logAction($batch->id, null, 'system', null, null, 'failed', 'error', 'Import thất bại: ' . $e->getMessage());
        } finally {
            // Rebuild Caches and clear optimization configurations as requested
            try {
                Artisan::call('optimize:clear');
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
            } catch (\Throwable $e) {
                Log::warning("Failed to clear caches after import job: " . $e->getMessage());
            }
        }
    }
}
