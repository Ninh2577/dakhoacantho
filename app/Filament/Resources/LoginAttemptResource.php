<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginAttemptResource\Pages;
use App\Models\LoginAttempt;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoginAttemptResource extends Resource
{
    protected static ?string $model = LoginAttempt::class;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationGroup = 'Bảo mật';

    protected static ?string $navigationLabel = 'Lượt đăng nhập';

    protected static ?string $modelLabel = 'Lượt đăng nhập';

    protected static ?string $pluralModelLabel = 'Lượt đăng nhập';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('successful')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Thành công' : 'Thất bại')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Tài khoản / Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('Địa chỉ IP')
                    ->searchable()
                    ->fontFamily('mono')
                    ->color('info')
                    ->icon('heroicon-m-globe-alt')
                    ->copyable()
                    ->copyMessage('Đã sao chép IP thành công'),

                Tables\Columns\TextColumn::make('failure_reason')
                    ->label('Lý do lỗi')
                    ->placeholder('—')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? $state : null),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Thiết bị (User Agent)')
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian đăng nhập')
                    ->dateTime('d/m/Y H:i:s')
                    ->description(fn ($record) => $record->created_at->diffForHumans())
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('successful')
                    ->label('Kết quả')
                    ->options([
                        '1' => 'Thành công',
                        '0' => 'Thất bại',
                    ]),

                Filter::make('today')
                    ->label('Hôm nay')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today())),

                Filter::make('failed_today')
                    ->label('Thất bại hôm nay')
                    ->query(fn (Builder $query) => $query
                        ->where('successful', false)
                        ->whereDate('created_at', today())),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xoá đã chọn'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getWidgets(): array
    {
        return [
            LoginAttemptResource\Widgets\LoginAttemptsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoginAttempts::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = LoginAttempt::failed()->today()->count();

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
