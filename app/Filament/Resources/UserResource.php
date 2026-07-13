<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $activeNavigationIcon = 'heroicon-m-user-circle';

    protected static ?string $navigationLabel = 'Người dùng';

    protected static ?string $modelLabel = 'người dùng';

    protected static ?string $pluralModelLabel = 'người dùng';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Họ và tên')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255)
                    ->placeholder(fn (string $context): string => $context === 'edit' ? 'Để trống nếu không muốn đổi' : ''),
                Forms\Components\Select::make('role')
                    ->label('Vai trò')
                    ->options(function () {
                        $options = ['admin' => 'Quản trị viên'];
                        $customRoles = Setting::get('custom_roles', []);
                        
                        if (empty($customRoles)) {
                            $options['doctor'] = 'Bác sĩ';
                            $options['editor'] = 'Biên tập viên';
                        } else {
                            foreach ($customRoles as $role) {
                                if (!empty($role['slug']) && !empty($role['name'])) {
                                    $options[$role['slug']] = $role['name'];
                                }
                            }
                        }
                        return $options;
                    })
                    ->required()
                    ->default('editor'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Tables\Columns\ImageColumn::make('avatar')
                        ->label('Ảnh đại diện')
                        ->circular()
                        ->size(80)
                        ->alignCenter()
                        ->default(fn (User $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF&size=128')
                        ->extraAttributes(['class' => 'flex justify-center pt-6']),

                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->label('Họ và tên')
                            ->searchable()
                            ->sortable()
                            ->weight('bold')
                            ->size('lg')
                            ->alignCenter(),

                        Tables\Columns\TextColumn::make('email')
                            ->label('Email')
                            ->searchable()
                            ->sortable()
                            ->color('gray')
                            ->size('sm')
                            ->alignCenter(),

                        Tables\Columns\TextColumn::make('role')
                            ->label('Vai trò')
                            ->badge()
                            ->alignCenter()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'doctor' => 'success',
                                'editor' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'Quản trị viên',
                                'doctor' => 'Bác sĩ',
                                'editor' => 'Biên tập viên',
                                default => (function() use ($state) {
                                    $customRoles = Setting::get('custom_roles', []);
                                    foreach ($customRoles as $role) {
                                        if (($role['slug'] ?? '') === $state) {
                                            return $role['name'] ?? $state;
                                        }
                                    }
                                    return $state;
                                })(),
                            }),

                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->color('gray')
                            ->size('xs')
                            ->icon('heroicon-m-calendar')
                            ->alignCenter(),
                    ])->space(3)->extraAttributes(['class' => 'p-6 flex flex-col items-center justify-center gap-y-2']),
                ]),
            ])
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'xl' => 4,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Vai trò')
                    ->options(function () {
                        $options = ['admin' => 'Quản trị viên'];
                        $customRoles = Setting::get('custom_roles', []);
                        
                        if (empty($customRoles)) {
                            $options['doctor'] = 'Bác sĩ';
                            $options['editor'] = 'Biên tập viên';
                        } else {
                            foreach ($customRoles as $role) {
                                if (!empty($role['slug']) && !empty($role['name'])) {
                                    $options[$role['slug']] = $role['name'];
                                }
                            }
                        }
                        return $options;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn (Collection $records) => $records->filter(fn ($record) => $record->id !== auth()->id())->each->delete()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
