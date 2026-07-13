<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;


class ListMediaFiles extends ListRecords
{
    protected static string $resource = MediaFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync_images')
                ->label('Đồng bộ ảnh')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->action(function () {
                    try {
                        Artisan::call('media:sync');
                        $output = Artisan::output();

                        $scannedCount = 0;
                        $syncedCount = 0;

                        if (preg_match('/Total scanned:\s*(\d+)/i', $output, $scannedMatches)) {
                            $scannedCount = (int) $scannedMatches[1];
                        }
                        if (preg_match('/Newly synced:\s*(\d+)/i', $output, $syncedMatches)) {
                            $syncedCount = (int) $syncedMatches[1];
                        }

                        Notification::make()
                            ->title('Đồng bộ thành công!')
                            ->body("Đã quét hoàn tất **" . number_format($scannedCount) . "** tệp tin.\nĐồng bộ mới thành công **" . number_format($syncedCount) . "** ảnh/tài liệu vào Thư viện.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Lỗi đồng bộ!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make()
                ->label('Tải lên tệp mới')
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
