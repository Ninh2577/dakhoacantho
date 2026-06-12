<?php

namespace App\Console\Commands;

use App\Models\FileScanResult;
use Illuminate\Console\Command;

class SecurityCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp các kết quả quét bảo mật cũ (hơn 30 ngày) nhưng giữ lại kết quả quét mới nhất';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Đang dọn dẹp các kết quả quét cũ...');

        $latestScan = FileScanResult::orderBy('created_at', 'desc')->first();
        $latestScanId = $latestScan ? $latestScan->scan_id : null;

        $query = FileScanResult::where('created_at', '<', now()->subDays(30));
        
        if ($latestScanId) {
            $query->where('scan_id', '!=', $latestScanId);
        }

        $deletedCount = $query->delete();

        $this->info("Đã dọn dẹp xong! Số bản ghi cũ bị xóa: $deletedCount");
        if ($latestScanId) {
            $this->info("Kết quả quét mới nhất (Scan ID: $latestScanId) đã được giữ lại an toàn.");
        }

        return Command::SUCCESS;
    }
}
