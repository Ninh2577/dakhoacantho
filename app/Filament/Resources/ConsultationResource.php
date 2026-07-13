<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultationResource\Pages;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ConsultationResource extends Resource
{
    protected static ?string $model = Consultation::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Tư vấn';

    protected static ?string $modelLabel = 'tư vấn';

    protected static ?string $pluralModelLabel = 'tư vấn';

    protected static ?string $navigationGroup = 'Chăm sóc bệnh nhân';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    // Main info
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('👤 Thông tin người gửi')
                            ->schema([
                                Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Họ tên')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('phone')
                                        ->label('Số điện thoại')
                                        ->required()
                                        ->tel()
                                        ->maxLength(20),
                                    Forms\Components\TextInput::make('department')
                                        ->label('Chuyên khoa')
                                        ->maxLength(255),
                                    Select::make('assigned_to')
                                        ->label('Người phụ trách')
                                        ->options(User::pluck('name', 'id'))
                                        ->searchable()
                                        ->placeholder('Chọn nhân viên xử lý'),
                                ]),
                                Forms\Components\Textarea::make('symptoms')
                                    ->label('Triệu chứng')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                        Section::make('📝 Ghi chú nội bộ')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('Ghi chú nội bộ')
                                    ->rows(4)
                                    ->placeholder('Ghi chú nội bộ sau khi liên hệ (bệnh nhân không thấy)...')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                    // Sidebar
                    Grid::make(1)->columnSpan(1)->schema([
                        Section::make('📋 Trạng thái xử lý')
                            ->schema([
                                Select::make('status')
                                    ->label('Trạng thái')
                                    ->options(Consultation::statusOptions())
                                    ->required()
                                    ->default('pending'),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ và tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Consultation $record): string => $record->phone),
                Tables\Columns\TextColumn::make('department')
                    ->label('Chuyên khoa')
                    ->badge()
                    ->color('info')
                    ->default('Chưa chọn'),
                Tables\Columns\TextColumn::make('symptoms')
                    ->label('Nội dung')
                    ->limit(60)
                    ->tooltip(fn (Consultation $record): string => $record->symptoms ?? '')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Consultation::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => Consultation::statusColor($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('Phụ trách')
                    ->default('—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('patient_id')
                    ->label('Đã thành BN')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(Consultation::statusOptions()),
                SelectFilter::make('assigned_to')
                    ->label('Người phụ trách')
                    ->options(User::pluck('name', 'id')),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('Chưa có tư vấn nào')
            ->emptyStateDescription('Các yêu cầu tư vấn từ website sẽ hiển thị tại đây.')
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),

                // ── Convert to Patient action ──────────────────────────
                Action::make('convertToPatient')
                    ->label(fn (Consultation $record): string => $record->patient_id
                        ? 'Xem bệnh nhân'
                        : 'Chuyển thành bệnh nhân')
                    ->icon(fn (Consultation $record): string => $record->patient_id
                        ? 'heroicon-o-arrow-top-right-on-square'
                        : 'heroicon-o-user-plus')
                    ->color(fn (Consultation $record): string => $record->patient_id ? 'success' : 'primary')
                    ->action(function (Consultation $record) {
                        // If already converted → redirect to patient edit
                        if ($record->patient_id) {
                            return redirect()->route('filament.admin.resources.patients.edit', [
                                'record' => $record->patient_id,
                            ]);
                        }

                        // Create patient from consultation data
                        $patient = Patient::create([
                            'full_name' => $record->name,
                            'phone' => $record->phone,
                            'notes' => $record->symptoms,
                            'status' => 'new',
                            'source' => 'Tư vấn online',
                            'consultation_id' => $record->id,
                            'created_by' => auth()->id(),
                        ]);

                        // Link consultation back to patient
                        $record->update([
                            'patient_id' => $patient->id,
                            'converted_to_patient_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Đã chuyển thành bệnh nhân!')
                            ->body("Bệnh nhân #{$patient->id} — {$patient->full_name} đã được tạo thành công.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(fn (Consultation $record): bool => ! $record->patient_id)
                    ->modalHeading('Chuyển tư vấn thành bệnh nhân?')
                    ->modalDescription('Thông tin tư vấn sẽ được sao chép để tạo hồ sơ bệnh nhân mới. Tư vấn gốc vẫn được giữ lại.')
                    ->modalSubmitActionLabel('Xác nhận chuyển đổi'),
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
                                ->options(Consultation::statusOptions())
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
            'index' => Pages\ListConsultations::route('/'),
            'create' => Pages\CreateConsultation::route('/create'),
            'edit' => Pages\EditConsultation::route('/{record}/edit'),
        ];
    }
}
