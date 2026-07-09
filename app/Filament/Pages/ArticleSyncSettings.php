<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ArticleSyncSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static string $view = 'filament.pages.article-sync-settings';

    protected static ?string $title = 'Cấu hình API Đồng bộ';

    protected static ?string $navigationLabel = 'Cấu hình API Đồng bộ';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 10;

    public ?string $apiUrl = null;
    public ?string $syncToken = null;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    public function mount(): void
    {
        $this->apiUrl = url('/api/v1/sync/articles');
        $this->syncToken = config('services.sync.token') ?: env('SYNC_API_TOKEN');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('regenerateToken')
                ->label('Tạo lại Token mới')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $newToken = Str::random(40);
                    $this->updateEnvToken($newToken);
                    
                    $this->syncToken = $newToken;
                    
                    // Clear config cache to apply immediately in current request
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                    
                    Notification::make()
                        ->title('Đã tạo mới Token thành công!')
                        ->body('Vui lòng copy Token mới này cấu hình vào website nhận.')
                        ->success()
                        ->send();
                })
        ];
    }

    protected function updateEnvToken(string $value): void
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match('/^SYNC_API_TOKEN=.*/m', $content)) {
                $content = preg_replace('/^SYNC_API_TOKEN=.*/m', "SYNC_API_TOKEN={$value}", $content);
            } else {
                $content .= "\nSYNC_API_TOKEN={$value}\n";
            }
            file_put_contents($path, $content);
        }
    }
}
