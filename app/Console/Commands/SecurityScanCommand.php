<?php

namespace App\Console\Commands;

use App\Models\FileScanResult;
use App\Services\Security\SecurityScannerService;
use Illuminate\Console\Command;

class SecurityScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:scan {--quick} {--full} {--check=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chạy quét bảo mật hệ thống';

    /**
     * Execute the console command.
     */
    public function handle(SecurityScannerService $scannerService): int
    {
        $quick = $this->option('quick');
        $full = $this->option('full');
        $check = $this->option('check');

        if (! $quick && ! $full && ! $check) {
            $this->error('Bạn phải chọn loại quét: --quick, --full hoặc --check=ten_khoa');

            return Command::FAILURE;
        }

        $this->info('Đang chuẩn bị quét bảo mật...');
        $scanId = null;

        if ($check) {
            $this->info("Đang chạy kiểm tra cụ thể: $check");
            $scannerService->runCheck($check);
            $scanId = $scannerService->getLatestSummary()['scan_id'];
        } elseif ($full) {
            $this->info('Đang chạy quét đầy đủ toàn bộ hệ thống...');
            $scanId = $scannerService->runFullScan();
        } else {
            $this->info('Đang chạy quét nhanh...');
            $scanId = $scannerService->runQuickScan();
        }

        if ($scanId) {
            $results = FileScanResult::byScan($scanId)->get();
            $threats = $results->where('type', '!=', FileScanResult::TYPE_OK)
                ->where('type', '!=', FileScanResult::TYPE_IGNORED)
                ->where('type', '!=', FileScanResult::TYPE_REVIEWED);

            $this->info("\n--- KẾT QUẢ QUÉT BẢO MẬT ---");
            $this->info("Scan ID: $scanId");
            $this->info('Tổng số hạng mục kiểm tra: '.$results->count());
            $this->info('Số mối đe dọa phát hiện: '.$threats->count());

            if ($threats->count() > 0) {
                foreach ($threats as $threat) {
                    $severityLabel = strtoupper($threat->severity);
                    $this->warn("[$severityLabel] [Group: {$threat->check_group}] [Target: {$threat->target}]");
                    $this->line("  Message: {$threat->message}");
                    if ($threat->recommendation) {
                        $this->line("  Recommendation: {$threat->recommendation}");
                    }
                }
                $this->warn("\nHãy truy cập trang quản trị Admin (/admin/security-scan) để xem chi tiết và xử lý.");
            } else {
                $this->info("\nHệ thống sạch! Không phát hiện mối đe dọa nào.");
            }
        }

        return Command::SUCCESS;
    }
}
