<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityEventResource\Pages;
use App\Models\SecurityEvent;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SecurityEventResource extends Resource
{
    protected static ?string $model = SecurityEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'Bảo mật';

    protected static ?string $navigationLabel = 'Sự kiện bảo mật';

    protected static ?string $modelLabel = 'Sự kiện bảo mật';

    protected static ?string $pluralModelLabel = 'Sự kiện bảo mật';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        // Read-only view — no creation from UI
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('severity')
                    ->label('Mức độ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'info' => 'gray',
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'info' => 'Thông tin',
                        'low' => 'Thấp',
                        'medium' => 'Trung bình',
                        'high' => 'Cao',
                        'critical' => 'Nghiêm trọng',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Loại')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'failed_login' => 'Đăng nhập thất bại',
                        'successful_login' => 'Đăng nhập thành công',
                        'brute_force' => 'Brute force',
                        'ip_blocked' => 'IP bị chặn',
                        'firewall_block' => 'Tường lửa chặn',
                        'suspicious_request' => 'Yêu cầu đáng ngờ',
                        'malicious_path' => 'Đường dẫn độc hại',
                        'scan_critical' => 'Quét phát hiện nguy hiểm',
                        'file_integrity' => 'Toàn vẹn file',
                        default => $state,
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->url)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('message')
                    ->label('Mô tả')
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('severity')
                    ->label('Mức độ')
                    ->options([
                        'info' => 'Thông tin',
                        'low' => 'Thấp',
                        'medium' => 'Trung bình',
                        'high' => 'Cao',
                        'critical' => 'Nghiêm trọng',
                    ]),

                SelectFilter::make('type')
                    ->label('Loại')
                    ->options([
                        'failed_login' => 'Đăng nhập thất bại',
                        'brute_force' => 'Brute force',
                        'ip_blocked' => 'IP bị chặn',
                        'firewall_block' => 'Tường lửa chặn',
                        'suspicious_request' => 'Yêu cầu đáng ngờ',
                        'malicious_path' => 'Đường dẫn độc hại',
                        'scan_critical' => 'Quét phát hiện nguy hiểm',
                    ]),

                Filter::make('today')
                    ->label('Hôm nay')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Chi tiết')
                    ->modalContent(fn (SecurityEvent $record) => view(
                        'filament.security.event-detail',
                        ['record' => $record]
                    )),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xoá đã chọn'),
                ]),
            ])
            ->poll('60s'); // Auto-refresh every 60 seconds
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecurityEvents::route('/'),
        ];
    }

    /**
     * Badge on nav item showing today's critical/high count.
     */
    public static function getNavigationBadge(): ?string
    {
        try {
            $count = SecurityEvent::criticalOrHigh()->today()->count();

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
