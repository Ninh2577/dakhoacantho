<?php

namespace App\Filament\Pages;

use App\Models\FileScanResult;
use App\Services\Security\SecurityScannerService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

class SecurityScan extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static string $view = 'filament.pages.security-scan';

    protected static ?string $title = 'Quét bảo mật';

    protected static ?string $navigationLabel = 'Quét bảo mật';

    protected static ?string $navigationGroup = 'Bảo mật';

    protected static ?int $navigationSort = 15;

    public ?array $summary = [];

    public function mount(SecurityScannerService $scannerService): void
    {
        $this->updateSummary($scannerService);
    }

    protected function updateSummary(SecurityScannerService $scannerService): void
    {
        $this->summary = $scannerService->getLatestSummary();
    }

    /**
     * Define header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('runQuickScan')
                ->label('Chạy quét nhanh')
                ->color('primary')
                ->icon('heroicon-o-bolt')
                ->action(function (SecurityScannerService $scannerService) {
                    $scannerService->runQuickScan();
                    $this->updateSummary($scannerService);

                    Notification::make()
                        ->title('Đã hoàn thành quét nhanh!')
                        ->success()
                        ->send();
                }),

            Action::make('runFullScan')
                ->label('Chạy quét đầy đủ')
                ->color('warning')
                ->icon('heroicon-o-magnifying-glass')
                ->requiresConfirmation()
                ->modalHeading('Cảnh báo tài nguyên & timeout')
                ->modalDescription('Quét đầy đủ sẽ duyệt qua tất cả tệp nguồn (bao gồm quét mã độc và đối chiếu thay đổi tệp). Hành động này có thể tốn CPU/Bộ nhớ và gây timeout trên trình duyệt. Khuyến nghị chạy qua dòng lệnh: rtk php artisan security:scan --full. Bạn có thực sự muốn chạy ngay trên web?')
                ->action(function (SecurityScannerService $scannerService) {
                    $scannerService->runFullScan();
                    $this->updateSummary($scannerService);

                    Notification::make()
                        ->title('Đã hoàn thành quét đầy đủ!')
                        ->success()
                        ->send();
                }),

            Action::make('createBaseline')
                ->label('Tạo baseline')
                ->color('gray')
                ->icon('heroicon-o-document-duplicate')
                ->action(function (SecurityScannerService $scannerService) {
                    $result = $scannerService->generateBaseline();
                    $this->updateSummary($scannerService);

                    Notification::make()
                        ->title('Đã lưu baseline thành công!')
                        ->body("Đã lưu hash của {$result['total_files']} tệp tin nguồn.")
                        ->success()
                        ->send();
                }),

            Action::make('cleanup')
                ->label('Dọn kết quả cũ')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Dọn dẹp kết quả quét cũ')
                ->modalDescription('Bạn có muốn xóa toàn bộ kết quả quét cũ hơn 30 ngày? Lưu ý: Kết quả quét mới nhất sẽ luôn được giữ lại.')
                ->action(function (SecurityScannerService $scannerService) {
                    $latestScan = FileScanResult::orderBy('created_at', 'desc')->first();
                    $latestScanId = $latestScan ? $latestScan->scan_id : null;

                    $query = FileScanResult::where('created_at', '<', now()->subDays(30));
                    if ($latestScanId) {
                        $query->where('scan_id', '!=', $latestScanId);
                    }
                    $deleted = $query->delete();
                    $this->updateSummary($scannerService);

                    Notification::make()
                        ->title("Đã dọn dẹp thành công!")
                        ->body("Đã xóa {$deleted} bản ghi cũ.")
                        ->success()
                        ->send();
                }),
        ];
    }

    /**
     * Define results table.
     */
    public function table(Table $table): Table
    {
        $scanId = $this->summary['scan_id'] ?? 'none';

        return $table
            ->query(
                FileScanResult::query()->where('scan_id', $scanId)
            )
            ->columns([
                TextColumn::make('check_group')
                    ->label('Nhóm kiểm tra')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('target')
                    ->label('Mục tiêu / Tệp tin')
                    ->placeholder('—')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('severity')
                    ->label('Mức độ')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'info'     => 'gray',
                        'low'      => 'info',
                        'medium'   => 'warning',
                        'high'     => 'danger',
                        'critical' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'info'     => 'Thông tin',
                        'low'      => 'Thấp',
                        'medium'   => 'Trung bình',
                        'high'     => 'Cao',
                        'critical' => 'Nguy hiểm',
                        default    => $state,
                    })
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Nội dung phát hiện')
                    ->wrap()
                    ->limit(100),

                TextColumn::make('type')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'ok'         => 'success',
                        'suspicious' => 'danger',
                        'modified'   => 'warning',
                        'new'        => 'info',
                        'deleted'    => 'danger',
                        'reviewed'   => 'gray',
                        'ignored'    => 'gray',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'ok'         => 'An toàn',
                        'suspicious' => 'Đáng ngờ',
                        'modified'   => 'Đã sửa đổi',
                        'new'        => 'Tệp mới',
                        'deleted'    => 'Đã bị xóa',
                        'reviewed'   => 'Đã duyệt',
                        'ignored'    => 'Bỏ qua',
                        default      => $state,
                    })
                    ->sortable(),
            ])
            ->actions([
                TableAction::make('view_details')
                    ->label('Chi tiết')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading('Chi tiết kết quả quét bảo mật')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Đóng')
                    ->modalContent(fn (FileScanResult $record) => view(
                        'filament.security.scan-detail',
                        [
                            'record' => $record,
                            'guidance' => app(\App\Services\Security\SecurityFindingGuidanceService::class)->build($record)
                        ]
                    )),

                TableAction::make('markReviewed')
                    ->label('Đã duyệt')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->action(function (FileScanResult $record) {
                        $record->markReviewed();
                        Notification::make()
                            ->title('Đã đánh dấu là đã duyệt.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (FileScanResult $record) => 
                        !in_array($record->type, ['ok', 'reviewed', 'ignored'])
                    ),

                TableAction::make('markIgnored')
                    ->label('Bỏ qua')
                    ->icon('heroicon-m-eye-slash')
                    ->color('warning')
                    ->form([
                        TextInput::make('ignored_reason')
                            ->label('Lý do bỏ qua')
                            ->placeholder('Nhập lý do (Ví dụ: False positive, tệp tin tự tạo...)')
                            ->required(),
                    ])
                    ->action(function (FileScanResult $record, array $data) {
                        $user = auth()->user();
                        $by = $user ? "{$user->name} ({$user->email})" : 'Hệ thống';
                        
                        $meta = $record->meta ?: [];
                        $meta['ignored_by'] = $by;

                        $record->update([
                            'type' => FileScanResult::TYPE_IGNORED,
                            'ignored_at' => now(),
                            'ignored_reason' => $data['ignored_reason'],
                            'meta' => $meta
                        ]);

                        Notification::make()
                            ->title('Đã đánh dấu bỏ qua cảnh báo.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (FileScanResult $record) => 
                        !in_array($record->type, ['ok', 'ignored'])
                    ),
            ])
            ->defaultSort('severity', 'desc')
            ->emptyStateHeading('Chưa chạy quét bảo mật')
            ->emptyStateDescription('Nhấp vào nút "Chạy quét nhanh" hoặc "Chạy quét đầy đủ" phía trên để bắt đầu quét bảo mật hệ thống.')
            ->poll('30s');
    }
}
