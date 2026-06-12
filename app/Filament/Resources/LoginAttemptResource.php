<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginAttemptResource\Pages;
use App\Models\LoginAttempt;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class LoginAttemptResource extends Resource
{
    protected static ?string $model = LoginAttempt::class;

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
                Tables\Columns\IconColumn::make('successful')
                    ->label('Kết quả')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('failure_reason')
                    ->label('Lý do thất bại')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->user_agent)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Thời gian')
                    ->dateTime('d/m/Y H:i:s')
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
