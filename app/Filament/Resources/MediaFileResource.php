<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFileResource\Pages;
use App\Models\MediaFile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MediaFileResource extends Resource
{
    protected static ?string $model = MediaFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Thư viện Media';

    protected static ?string $modelLabel = 'tệp tin';

    protected static ?string $pluralModelLabel = 'thư viện media';

    protected static ?string $navigationGroup = 'Hệ thống';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tải lên tệp tin mới')
                    ->description('Tải tệp tin ảnh hoặc tài liệu mới lên thư viện.')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Tệp tin')
                            ->disk('public')
                            ->directory('uploads/filament')
                            ->required()
                            ->maxSize(10240) // 10MB
                            ->helperText('Hỗ trợ định dạng: hình ảnh (png, jpg, jpeg, gif, webp, svg) hoặc tài liệu (pdf, doc, docx, xls, xlsx).'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('preview')
                    ->label('Ảnh xem trước')
                    ->square()
                    ->size(50)
                    ->state(fn ($record) => $record->url)
                    ->defaultImageUrl(fn ($record) => str_contains($record->file_type, 'image') ? null : 'https://ui-avatars.com/api/?name=DOC&color=7F9CF5&background=EBF4FF')
                    ->action(
                        Tables\Actions\Action::make('view_image')
                            ->modalContent(fn ($record) => view('filament.components.image-preview', ['record' => $record]))
                            ->modalHeading(fn ($record) => $record->name)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Đóng')
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->label('Tên tệp')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('file_path')
                    ->label('Đường dẫn')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('file_type')
                    ->label('Định dạng')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => str_contains($state, 'image') ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('readable_size')
                    ->label('Dung lượng')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('file_size', $direction);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tải lên')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file_type')
                    ->label('Loại tệp')
                    ->options([
                        'image' => 'Hình ảnh',
                        'document' => 'Tài liệu (PDF, Word, Excel...)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'image') {
                            $query->where('file_type', 'like', 'image/%');
                        } elseif ($data['value'] === 'document') {
                            $query->where('file_type', 'not like', 'image/%');
                        }
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_url')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('success')
                    ->action(function ($record) {
                        Notification::make()
                            ->title('Đã sao chép liên kết thành công!')
                            ->body($record->url)
                            ->success()
                            ->send();
                    })
                    ->extraAttributes(fn ($record) => [
                        'onclick' => "navigator.clipboard.writeText('" . $record->url . "');",
                    ]),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFiles::route('/'),
            'create' => Pages\CreateMediaFile::route('/create'),
        ];
    }
}
