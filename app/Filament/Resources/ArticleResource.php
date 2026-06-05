<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Filament\Resources\ArticleResource\RelationManagers;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Bài viết';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    // Main Content Area (Left - Span 2)
                    Grid::make(1)->columnSpan(2)->schema([
                        Section::make('Nội dung bài viết')->schema([
                            Select::make('category_id')
                                ->relationship('category', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->live(onBlur: true),
                            RichEditor::make('content')
                                ->required()
                                ->fileAttachmentsDirectory('articles/attachments')
                                ->fileAttachmentsDisk('public')
                                ->columnSpanFull()
                                ->live(onBlur: true),
                            FileUpload::make('thumbnail_image')
                                ->image()
                                ->directory('articles/thumbnails')
                                ->disk('public')
                                ->live(),
                            Toggle::make('is_published')
                                ->required()
                                ->live(),
                        ]),
                    ]),

                    // Sidebar Area (Right - Span 1)
                    Grid::make(1)->columnSpan(1)->schema([
                        // The Real-time SEO Scorecard
                        ViewField::make('seo_scorecard')
                            ->view('filament.components.seo-scorecard')
                            ->columnSpanFull(),
                            
                        Section::make('Cấu hình SEO')->schema([
                            Tabs::make('SEO Config')->tabs([
                                Tabs\Tab::make('SEO cơ bản')->schema([
                                    TextInput::make('focus_keyword')
                                        ->label('Từ khóa chính (Focus Keyword)')
                                        ->live(debounce: 500),
                                    TextInput::make('meta_title')
                                        ->label('Meta Title')
                                        ->maxLength(255)
                                        ->live(debounce: 500)
                                        ->suffixAction(
                                            Forms\Components\Actions\Action::make('generateMetaTitle')
                                                ->icon('heroicon-m-sparkles')
                                                ->tooltip('Tự tạo Meta Title từ tiêu đề')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $title = $get('title') ?? '';
                                                    $set('meta_title', mb_substr($title, 0, 60));
                                                })
                                        )
                                        ->helperText('Tiêu đề hiển thị trên Google (Tốt nhất: 50-60 ký tự).'),
                                    Textarea::make('meta_description')
                                        ->label('Meta Description')
                                        ->rows(3)
                                        ->live(debounce: 500)
                                        ->hintAction(
                                            Forms\Components\Actions\Action::make('generateMetaDesc')
                                                ->icon('heroicon-m-sparkles')
                                                ->tooltip('Tự tạo Meta Description từ nội dung')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $content = $get('content') ?? '';
                                                    $plain = strip_tags($content);
                                                    $plain = preg_replace('/\s+/', ' ', $plain);
                                                    $set('meta_description', mb_substr(trim($plain), 0, 160));
                                                })
                                        )
                                        ->helperText('Mô tả hiển thị trên Google (Tốt nhất: 150-160 ký tự).'),
                                    TextInput::make('seo_slug')
                                        ->label('SEO Slug')
                                        ->live(debounce: 500),
                                    TextInput::make('canonical_url')
                                        ->label('Canonical URL')
                                        ->url()
                                        ->live(debounce: 500),
                                ]),
                                Tabs\Tab::make('Mạng xã hội')->schema([
                                    TextInput::make('og_title')
                                        ->label('Facebook Title')
                                        ->live(debounce: 500)
                                        ->suffixAction(
                                            Forms\Components\Actions\Action::make('syncSocial')
                                                ->icon('heroicon-m-arrow-path')
                                                ->tooltip('Đồng bộ tiêu đề & mô tả từ Meta SEO')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $metaTitle = $get('meta_title') ?? '';
                                                    $metaDesc = $get('meta_description') ?? '';
                                                    
                                                    $set('og_title', $metaTitle);
                                                    $set('og_description', $metaDesc);
                                                    $set('twitter_title', $metaTitle);
                                                    $set('twitter_description', $metaDesc);
                                                })
                                        )
                                        ->helperText('Tiêu đề hiển thị khi chia sẻ lên Facebook.'),
                                    Textarea::make('og_description')
                                        ->label('Facebook Description')
                                        ->rows(3)
                                        ->live(debounce: 500),
                                    FileUpload::make('og_image')
                                        ->label('Facebook Image')
                                        ->image()
                                        ->directory('articles/seo')
                                        ->disk('public')
                                        ->live(),
                                    TextInput::make('twitter_title')
                                        ->label('Twitter Title')
                                        ->live(debounce: 500),
                                    Textarea::make('twitter_description')
                                        ->label('Twitter Description')
                                        ->rows(3)
                                        ->live(debounce: 500),
                                    FileUpload::make('twitter_image')
                                        ->label('Twitter Image')
                                        ->image()
                                        ->directory('articles/seo')
                                        ->disk('public')
                                        ->live(),
                                ]),
                                Tabs\Tab::make('Nâng cao')->schema([
                                    Toggle::make('robots_index')
                                        ->label('Index (Cho phép lập chỉ mục)')
                                        ->default(true)
                                        ->live(),
                                    Toggle::make('robots_follow')
                                        ->label('Follow (Cho phép theo dõi link)')
                                        ->default(true)
                                        ->live(),
                                ]),
                            ]),
                        ]),
                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_image')
                    ->label('Ảnh')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->wrap()
                    ->description(fn (Article $record): string => 'slug: /' . $record->slug),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Chuyên khoa')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author')
                    ->label('Tác giả')
                    ->default(fn (Article $record): string => match ($record->category?->slug) {
                        'nam-khoa' => 'BS. Nguyễn Văn An',
                        'phu-khoa' => 'BS. Trần Thị Mai',
                        default => 'Quản trị viên',
                    }),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Công khai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seo_score')
                    ->label('Điểm SEO')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state !== null ? (string) $state : 'Chưa phân tích')
                    ->color(fn ($state): string => match (true) {
                        $state === null      => 'gray',
                        $state >= 80        => 'success',
                        $state >= 50        => 'warning',
                        default             => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label('Chuyên khoa'),
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Công khai',
                        '0' => 'Bản nháp',
                    ]),
                Tables\Filters\Filter::make('seo_good')
                    ->label('SEO tốt (≥80)')
                    ->query(fn ($query) => $query->where('seo_score', '>=', 80)),
                Tables\Filters\Filter::make('seo_medium')
                    ->label('SEO khá (50–79)')
                    ->query(fn ($query) => $query->whereBetween('seo_score', [50, 79])),
                Tables\Filters\Filter::make('seo_low')
                    ->label('SEO thấp (<50 hoặc chưa phân tích)')
                    ->query(fn ($query) => $query->where('seo_score', '<', 50)->orWhereNull('seo_score')),
            ])
            ->defaultPaginationPageOption(10)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Article $record): string => url('/' . $record->category?->slug . '/' . $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make()->label('Sửa'),
                Tables\Actions\DeleteAction::make()->label('Xóa'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ArticleResource\Widgets\ArticleStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
