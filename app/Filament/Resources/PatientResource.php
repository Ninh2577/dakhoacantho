<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Category;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Bệnh nhân';

    protected static ?string $modelLabel = 'bệnh nhân';

    protected static ?string $pluralModelLabel = 'bệnh nhân';

    protected static ?string $navigationGroup = 'Chăm sóc bệnh nhân';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([

                    // ─── Main column (2/3) ───────────────────────────────────
                    Grid::make(1)->columnSpan(2)->schema([

                        Section::make('👤 Thông tin cá nhân')
                            ->description('Thông tin nhận dạng cơ bản của bệnh nhân.')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('full_name')
                                        ->label('Họ và tên *')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('Nguyễn Văn A'),
                                    TextInput::make('phone')
                                        ->label('Số điện thoại')
                                        ->tel()
                                        ->maxLength(20)
                                        ->placeholder('0901 234 567'),
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->maxLength(255)
                                        ->placeholder('example@gmail.com'),
                                    Select::make('gender')
                                        ->label('Giới tính')
                                        ->options(Patient::genderOptions())
                                        ->placeholder('Chọn giới tính'),
                                    DatePicker::make('birth_date')
                                        ->label('Ngày sinh')
                                        ->displayFormat('d/m/Y')
                                        ->maxDate(now()),
                                    TextInput::make('age')
                                        ->label('Tuổi')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(120)
                                        ->placeholder('Tuổi (nếu không biết ngày sinh)'),
                                ]),
                                Textarea::make('address')
                                    ->label('Địa chỉ')
                                    ->rows(2)
                                    ->placeholder('Số nhà, đường, phường/xã, quận/huyện, tỉnh/TP')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('🏥 Nhu cầu khám & tư vấn')
                            ->description('Thông tin liên quan đến nhu cầu điều trị.')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('category_id')
                                        ->label('Chuyên khoa quan tâm')
                                        ->options(Category::whereNull('parent_id')->pluck('name', 'id'))
                                        ->searchable()
                                        ->placeholder('Chọn chuyên khoa'),
                                    Select::make('source')
                                        ->label('Nguồn khách')
                                        ->options(Patient::sourceOptions())
                                        ->placeholder('Biết đến từ đâu?'),
                                ]),
                                Textarea::make('notes')
                                    ->label('Ghi chú')
                                    ->rows(3)
                                    ->placeholder('Mô tả ngắn về triệu chứng hoặc nhu cầu tư vấn...')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                    // ─── Sidebar column (1/3) ─────────────────────────────────
                    Grid::make(1)->columnSpan(1)->schema([

                        Section::make('📋 Trạng thái & Theo dõi')
                            ->schema([
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options(Patient::statusOptions())
                                    ->required()
                                    ->default('new'),
                                Select::make('created_by')
                                    ->label('Người phụ trách')
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->placeholder('Chọn nhân viên')
                                    ->default(fn () => auth()->id()),
                                Forms\Components\DateTimePicker::make('last_contacted_at')
                                    ->label('Ngày liên hệ gần nhất')
                                    ->displayFormat('d/m/Y H:i')
                                    ->nullable(),
                            ]),

                        Section::make('📝 Ghi chú nội bộ')
                            ->description('Chỉ nhân viên nội bộ thấy.')
                            ->schema([
                                Textarea::make('internal_note')
                                    ->label('')
                                    ->rows(5)
                                    ->placeholder('Ghi chú nội bộ (không hiển thị ra ngoài)...')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Patient $record): string => $record->phone ?? '—'),
                TextColumn::make('gender')
                    ->label('Giới tính')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'male' => 'Nam',
                        'female' => 'Nữ',
                        'other' => 'Khác',
                        default => '—',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('category.name')
                    ->label('Chuyên khoa')
                    ->badge()
                    ->color('primary')
                    ->default('—'),
                TextColumn::make('source')
                    ->label('Nguồn')
                    ->default('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Patient::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => Patient::statusColor($state))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Phụ trách')
                    ->default('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Patient::statusOptions()),
                SelectFilter::make('category_id')
                    ->label('Chuyên khoa')
                    ->options(Category::whereNull('parent_id')->pluck('name', 'id')),
                SelectFilter::make('gender')
                    ->label('Giới tính')
                    ->options(Patient::genderOptions()),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('Chưa có bệnh nhân nào')
            ->emptyStateDescription('Thêm bệnh nhân mới hoặc chuyển đổi từ tư vấn.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Thêm bệnh nhân')
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('changeStatus')
                        ->label('Đổi trạng thái')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Select::make('status')
                                ->label('Trạng thái mới')
                                ->options(Patient::statusOptions())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update(['status' => $data['status']]);
                        }),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
