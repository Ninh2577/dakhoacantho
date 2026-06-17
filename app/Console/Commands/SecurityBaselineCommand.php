<?php

namespace App\Console\Commands;

use App\Services\Security\SecurityScannerService;
use Illuminate\Console\Command;

class SecurityBaselineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:baseline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo baseline tệp tin cho hệ thống quét bảo mật';

    /**
     * Execute the console command.
     */
    public function handle(SecurityScannerService $scannerService): int
    {
        $this->info('Đang tạo baseline cho tệp tin mã nguồn...');

        $result = $scannerService->generateBaseline();

        $this->info('Đã tạo baseline thành công!');
        $this->info('Tổng số tệp đã lưu: '.$result['total_files']);
        $this->info('Đường dẫn lưu baseline: '.$result['baseline_path']);

        return Command::SUCCESS;
    }
}
