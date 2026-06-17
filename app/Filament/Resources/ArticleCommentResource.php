<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCommentResource\Pages;
use App\Models\ArticleComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArticleCommentResource extends Resource
{
    protected static ?string $model = ArticleComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Bình luận bài viết';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\Grid::make(1)->columnSpan(2)->schema([
                        Forms\Components\Section::make('👤 Thông tin người bình luận')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Họ tên')
                                        ->required()
                                        ->maxLength(100),
                                    Forms\Components\TextInput::make('phone')
                                        ->label('Số điện thoại')
                                        ->tel()
                                        ->maxLength(20)
                                        ->placeholder('Không có số điện thoại'),
                                    Forms\Components\Select::make('article_id')
                                        ->label('Bài viết')
                                        ->relationship('article', 'title')
                                        ->required()
                                        ->searchable()
                                        ->preload()
                                        ->columnSpanFull(),
                                ]),
                                Forms\Components\Textarea::make('content')
                                    ->label('Nội dung')
                                    ->required()
                                    ->rows(6)
                                    ->maxLength(1000)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                    Forms\Components\Grid::make(1)->columnSpan(1)->schema([
                        Forms\Components\Section::make('📋 Trạng thái kiểm duyệt')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Trạng thái')
                                    ->options(ArticleComment::statusOptions())
                                    ->required()
                                    ->default('pending'),
                            ]),
                        Forms\Components\Section::make('🌐 Siêu dữ liệu (Metadata)')
                            ->schema([
                                Forms\Components\TextInput::make('ip_address')
                                    ->label('Địa chỉ IP')
                                    ->disabled(),
                                Forms\Components\TextInput::make('user_agent')
                                    ->label('User Agent')
                                    ->disabled(),
                            ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Họ tên')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (ArticleComment $record): string => $record->phone ?? 'Không có SĐT'),
                Tables\Columns\TextColumn::make('article.title')
                    ->label('Bài viết')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(fn (ArticleComment $record): string => $record->article->title ?? ''),
                Tables\Columns\TextColumn::make('content')
                    ->label('Nội dung')
                    ->limit(60)
                    ->tooltip(fn (ArticleComment $record): string => $record->content ?? ''),
                Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ArticleComment::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => ArticleComment::statusColor($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày gửi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(ArticleComment::statusOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Sửa'),

                Tables\Actions\Action::make('approve')
                    ->label('Duyệt')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->hidden(fn (ArticleComment $record): bool => $record->status === 'approved')
                    ->action(function (ArticleComment $record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()
                            ->title('Đã duyệt bình luận!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Từ chối')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->hidden(fn (ArticleComment $record): bool => $record->status === 'rejected')
                    ->action(function (ArticleComment $record) {
                        $record->update(['status' => 'rejected']);
                        Notification::make()
                            ->title('Đã từ chối bình luận!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-o-fire')
                    ->color('gray')
                    ->hidden(fn (ArticleComment $record): bool => $record->status === 'spam')
                    ->action(function (ArticleComment $record) {
                        $record->update(['status' => 'spam']);
                        Notification::make()
                            ->title('Đã đánh dấu spam!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approveBulk')
                        ->label('Duyệt hàng loạt')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'approved'])),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('Chưa có bình luận nào')
            ->emptyStateDescription('Các bình luận từ người đọc sẽ hiển thị tại đây.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticleComments::route('/'),
            'create' => Pages\CreateArticleComment::route('/create'),
            'edit' => Pages\EditArticleComment::route('/{record}/edit'),
        ];
    }
}
