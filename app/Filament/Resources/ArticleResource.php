<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Str;

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
                Forms\Components\Hidden::make('_prev_title')->dehydrated(false),
                Grid::make(3)->schema([

                    // =========================================================
                    // MAIN CONTENT AREA — Left (span 2)
                    // =========================================================
                    Grid::make(1)->columnSpan(2)->schema([

                        // Title + Slug bar
                        Section::make()->schema([
                            TextInput::make('title')
                                ->label('Tiêu đề bài viết')
                                ->placeholder('Nhập tiêu đề bài viết...')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->extraInputAttributes(['style' => 'font-size:1.8rem;font-weight:700;padding:0.75rem 1rem;height:auto;line-height:1.3;'])
                                ->afterStateHydrated(function (Forms\Set $set, ?string $state) {
                                    $set('_prev_title', $state);
                                })
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state, ?\App\Models\Article $record) {
                                    $currentSlug = $get('slug');
                                    $prevTitle    = $get('_prev_title') ?? '';
                                    $prevAutoSlug = Str::slug($prevTitle);
                                    if (empty($currentSlug) || $currentSlug === $prevAutoSlug) {
                                        $ignoreId = $record?->id;
                                        $set('slug', static::generateUniqueSlug($state ?? '', $ignoreId));
                                    }
                                    if (empty($get('meta_title'))) {
                                        $set('meta_title', mb_substr($state ?? '', 0, 60));
                                    }
                                    $set('_prev_title', $state);
                                }),

                            TextInput::make('slug')
                                ->label('Đường dẫn (slug)')
                                ->placeholder('duong-dan-bai-viet')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->live(onBlur: true)
                                ->prefix(fn () => rtrim(config('app.url'), '/') . '/')
                                ->suffix('.html')
                                ->helperText('Không dấu, viết thường, ngăn cách bởi dấu gạch ngang.')
                                ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                    if (!empty($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->hintAction(
                                    Forms\Components\Actions\Action::make('copySlug')
                                        ->icon('heroicon-m-clipboard')
                                        ->tooltip('Sao chép URL')
                                        ->extraAttributes([
                                            'x-on:click' => "
                                                const url = `" . rtrim(config('app.url'), "/") . "/` + \$wire.get(`data.slug`) + `.html`;
                                                window.navigator.clipboard.writeText(url);
                                                alert(`Đã sao chép URL bài viết vào clipboard: ` + url);
                                            "
                                        ])
                                        ->action(fn () => null)
                                ),
                        ])->compact(),

                        // TinyMCE Editor (rendered directly without card section container)
                        ViewField::make('content')
                            ->label('Nội dung bài viết')
                            ->hiddenLabel()
                            ->view('filament.components.tinymce-editor')
                            ->required()
                            ->columnSpanFull(),

                        // Excerpt
                        Section::make('Tóm tắt bài viết')->schema([
                            Textarea::make('excerpt')
                                ->label('Tóm tắt ngắn (Excerpt)')
                                ->placeholder('Nhập tóm tắt ngắn của bài viết...')
                                ->rows(3)
                                ->maxLength(500)
                                ->live(debounce: 500)
                                ->helperText('Dùng làm mô tả ngắn cho bài viết (SEO description fallback).')
                                ->columnSpanFull(),
                        ]),

                    ]),

                    // =========================================================
                    // SIDEBAR — Right (span 1)
                    // =========================================================
                    Grid::make(1)
                        ->columnSpan(1)
                        ->extraAttributes([
                            'class' => 'md:sticky md:top-[80px] md:self-start',
                        ])
                        ->schema([

                        // --- Publish Card ---
                        Section::make('Xuất bản')->schema([
                            Forms\Components\Placeholder::make('current_status')
                                ->label('Trạng thái')
                                ->content(fn ($record) => ($record?->is_published) ? 'Đã xuất bản' : 'Bản nháp'),

                            Toggle::make('is_published')
                                ->label('Công khai bài viết')
                                ->helperText('Bật để hiển thị bài viết trên website.')
                                ->live()
                                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?bool $state) {
                                    if ($state && empty($get('published_at'))) {
                                        $set('published_at', now()->format('Y-m-d\TH:i'));
                                    }
                                }),
                            DateTimePicker::make('published_at')
                                ->label('Ngày xuất bản')
                                ->displayFormat('d/m/Y H:i')
                                ->seconds(false)
                                ->visible(fn (Forms\Get $get) => (bool) $get('is_published')),
                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('preview_in_card')
                                     ->label('Xem trước')
                                     ->icon('heroicon-o-eye')
                                     ->color('info')
                                     ->extraAttributes([
                                         'x-on:click' => "
                                             \$event.preventDefault();
                                             \$event.stopImmediatePropagation();
                                             window.triggerArticlePreview(\$wire);
                                         "
                                     ])
                                     ->action(fn () => null),
                                Forms\Components\Actions\Action::make('save_draft_in_card')
                                    ->label('Lưu nháp')
                                    ->color('gray')
                                    ->action(function ($livewire) {
                                        $livewire->data['is_published'] = false;
                                        if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                            $livewire->create();
                                        } else {
                                            $livewire->save();
                                        }
                                    }),
                                Forms\Components\Actions\Action::make('publish_in_card')
                                    ->label(fn ($record) => ($record?->is_published) ? 'Cập nhật' : 'Xuất bản')
                                    ->color('primary')
                                    ->action(function ($livewire) {
                                        $livewire->data['is_published'] = true;
                                        if ($livewire instanceof \Filament\Resources\Pages\CreateRecord) {
                                            $livewire->create();
                                        } else {
                                            $livewire->save();
                                        }
                                    }),
                            ])->fullWidth(),
                        ]),

                        // --- Category Card ---
                        Section::make('Danh mục')->schema([
                            Select::make('category_id')
                                ->label('Chuyên khoa / Danh mục')
                                ->relationship('category', 'name')
                                ->placeholder('Chọn danh mục...')
                                ->required()
                                ->searchable()
                                ->preload(),
                        ]),

                        // --- Thumbnail Card ---
                        Section::make('Ảnh đại diện')->schema([
                            FileUpload::make('featured_image')
                                ->label('Ảnh đại diện')
                                ->image()
                                ->directory('articles/featured')
                                ->disk('public')
                                ->imagePreviewHeight('160')
                                ->panelAspectRatio('16:9')
                                ->panelLayout('integrated')
                                ->live(),
                        ]),

                        // --- SEO Scorecard ---
                        ViewField::make('seo_scorecard')
                            ->view('filament.components.seo-scorecard')
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('seo_checks')->default([])->dehydrated(false),

                        // --- SEO Config Card ---
                        Section::make('Cấu hình SEO')->schema([
                            Tabs::make('SEO Config')->tabs([
                                Tabs\Tab::make('SEO cơ bản')->schema([
                                    TextInput::make('focus_keyword')
                                        ->label('Từ khóa chính (Focus Keyword)')
                                        ->placeholder('VD: phòng khám đa khoa cần thơ')
                                        ->live(debounce: 500)
                                        ->helperText('Từ khóa bạn muốn bài viết xếp hạng trên Google.'),
                                    TextInput::make('meta_title')
                                        ->label('Tiêu đề SEO (Meta Title)')
                                        ->placeholder('Tiêu đề hiển thị trên Google')
                                        ->maxLength(60)
                                        ->live(debounce: 500)
                                        ->suffixAction(
                                            Forms\Components\Actions\Action::make('generateMetaTitle')
                                                ->icon('heroicon-m-sparkles')
                                                ->tooltip('Tạo từ tiêu đề bài viết')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $title = $get('title') ?? '';
                                                    $set('meta_title', mb_substr($title, 0, 60));
                                                })
                                        )
                                        ->helperText('Tối ưu: 50-60 ký tự.'),
                                    Textarea::make('meta_description')
                                        ->label('Mô tả SEO (Meta Description)')
                                        ->placeholder('Mô tả hiển thị dưới tiêu đề trên Google...')
                                        ->rows(3)
                                        ->maxLength(160)
                                        ->live(debounce: 500)
                                        ->hintAction(
                                            Forms\Components\Actions\Action::make('generateMetaDesc')
                                                ->icon('heroicon-m-sparkles')
                                                ->tooltip('Tạo từ nội dung bài viết')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $content = $get('content') ?? '';
                                                    $plain = preg_replace('/\s+/', ' ', strip_tags($content));
                                                    $set('meta_description', mb_substr(trim($plain), 0, 155));
                                                })
                                        )
                                        ->helperText('Tối ưu: 140-160 ký tự.'),
                                    TextInput::make('canonical_url')
                                        ->label('URL chuẩn (Canonical)')
                                        ->placeholder('https://...')
                                        ->url()
                                        ->live(debounce: 500)
                                        ->helperText('Để trống = tự dùng URL bài viết hiện tại.'),
                                ]),
                                Tabs\Tab::make('Mạng xã hội')->schema([
                                    TextInput::make('og_title')
                                        ->label('Tiêu đề Facebook (OG Title)')
                                        ->placeholder('Tiêu đề khi chia sẻ Facebook')
                                        ->live(debounce: 500)
                                        ->suffixAction(
                                            Forms\Components\Actions\Action::make('syncSocial')
                                                ->icon('heroicon-m-arrow-path')
                                                ->tooltip('Đồng bộ từ SEO cơ bản')
                                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                                    $set('og_title', $get('meta_title') ?? '');
                                                    $set('og_description', $get('meta_description') ?? '');
                                                    $set('twitter_title', $get('meta_title') ?? '');
                                                    $set('twitter_description', $get('meta_description') ?? '');
                                                })
                                        ),
                                    Textarea::make('og_description')
                                        ->label('Mô tả Facebook (OG Description)')
                                        ->rows(2)
                                        ->live(debounce: 500),
                                    FileUpload::make('og_image')
                                        ->label('Ảnh Facebook (OG Image)')
                                        ->image()
                                        ->directory('articles/seo')
                                        ->disk('public')
                                        ->live(),
                                    TextInput::make('twitter_title')
                                        ->label('Tiêu đề Twitter')
                                        ->live(debounce: 500),
                                    Textarea::make('twitter_description')
                                        ->label('Mô tả Twitter')
                                        ->rows(2)
                                        ->live(debounce: 500),
                                    FileUpload::make('twitter_image')
                                        ->label('Ảnh Twitter')
                                        ->image()
                                        ->directory('articles/seo')
                                        ->disk('public')
                                        ->live(),
                                ]),
                                Tabs\Tab::make('Nâng cao')->schema([
                                    TextInput::make('seo_slug')
                                        ->label('Slug SEO riêng (nếu khác slug chính)')
                                        ->placeholder('slug-seo-rieng')
                                        ->live(debounce: 500)
                                        ->helperText('Để trống = dùng slug chính.'),
                                    Toggle::make('robots_index')
                                        ->label('Cho phép Google lập chỉ mục (Index)')
                                        ->default(true)
                                        ->live(),
                                    Toggle::make('robots_follow')
                                        ->label('Cho phép Google theo dõi liên kết (Follow)')
                                        ->default(true)
                                        ->live(),
                                ]),
                            ]),
                        ]),

                        // --- Schema Card ---
                        Section::make('Loại Schema (JSON-LD)')->schema([
                            Select::make('schema_type')
                                ->label('Loại Schema')
                                ->options([
                                    'Article'        => 'Article (Bài viết thông thường)',
                                    'BlogPosting'    => 'BlogPosting (Blog)',
                                    'MedicalWebPage' => 'MedicalWebPage (Trang y tế)',
                                    'None'           => 'None (Không dùng Schema)',
                                ])
                                ->default('Article')
                                ->live()
                                ->helperText('MedicalWebPage phù hợp cho bài y tế/phòng khám.'),
                        ])->collapsed(),

                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
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
                        default    => 'Quản trị viên',
                    }),
                Tables\Columns\ToggleColumn::make('is_published')
                    ->label('Công khai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('seo_score')
                    ->label('Điểm SEO')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state !== null ? (string) $state : 'Chưa phân tích')
                    ->color(fn ($state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 80   => 'success',
                        $state >= 50   => 'warning',
                        default        => 'danger',
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
                    ->label('SEO thấp (<50)')
                    ->query(fn ($query) => $query->where('seo_score', '<', 50)->orWhereNull('seo_score')),
            ])
            ->defaultPaginationPageOption(10)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Xem')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Article $record): string => $record->public_url)
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['category']);
    }

    public static function getRelations(): array
    {
        return [];
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
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }

    public static function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        if (empty($slug)) {
            $slug = 'bai-viet';
        }

        $originalSlug = $slug;
        $count = 2;

        while (true) {
            $query = \App\Models\Article::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
