<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Bài viết';

    protected static ?string $modelLabel = 'bài viết';

    protected static ?string $pluralModelLabel = 'bài viết';

    protected static ?string $navigationGroup = 'Quản lý nội dung';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('_prev_title')->dehydrated(false),
                Tabs::make('ArticleTabs')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make('Nội dung bài viết')
                            ->id('tab_content')
                            ->schema([
                                // Title + Slug bar
                                Section::make()->schema([
                                    TextInput::make('title')
                                        ->label('Tiêu đề bài viết')
                                        ->placeholder('Nhập tiêu đề bài viết...')
                                        ->rules(['required'])
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->extraInputAttributes(['style' => 'font-size:1.8rem;font-weight:700;padding:0.75rem 1rem;height:auto;line-height:1.3;'])
                                        ->afterStateHydrated(function (Forms\Set $set, ?string $state) {
                                            $set('_prev_title', $state);
                                        })
                                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state, ?Article $record) {
                                            $currentSlug = $get('slug');
                                            $prevTitle = $get('_prev_title') ?? '';
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
                                        ->rules(['required'])
                                        ->maxLength(255)
                                        ->unique(ignoreRecord: true)
                                        ->live(onBlur: true)
                                        ->prefix(fn () => rtrim(config('app.url'), '/').'/')
                                        ->suffix('.html')
                                        ->helperText('Không dấu, viết thường, ngăn cách bởi dấu gạch ngang.')
                                        ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                            if (! empty($state)) {
                                                $set('slug', Str::slug($state));
                                            }
                                        })
                                        ->hintAction(
                                            Forms\Components\Actions\Action::make('copySlug')
                                                ->icon('heroicon-m-clipboard')
                                                ->tooltip('Sao chép URL')
                                                ->extraAttributes([
                                                    'x-on:click' => '
                                                        const url = `'.rtrim(config('app.url'), '/').'/` + $wire.get(`data.slug`) + `.html`;
                                                        window.navigator.clipboard.writeText(url);
                                                        alert(`Đã sao chép URL bài viết vào clipboard: ` + url);
                                                    ',
                                                ])
                                                ->action(fn () => null)
                                        ),
                                ])->compact(),

                                // TinyMCE Editor (rendered directly without card section container)
                                ViewField::make('content')
                                    ->label('Nội dung bài viết')
                                    ->hiddenLabel()
                                    ->view('filament.components.tinymce-editor')
                                    ->rules(['required'])
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
                            ])
                            ->columns(1),

                        Tabs\Tab::make('Cấu hình & SEO')
                            ->id('tab_settings_seo')
                            ->schema([
                                Grid::make(2)->schema([
                                    // Left Column
                                    Grid::make(1)
                                        ->columnSpan(1)
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
                                                        ->url('#')
                                                        ->extraAttributes([
                                                            'x-on:click' => '
                                                             $event.preventDefault();
                                                             $event.stopImmediatePropagation();
                                                             window.triggerArticlePreview($wire, $el);
                                                         ',
                                                        ]),
                                                    Forms\Components\Actions\Action::make('save_draft_in_card')
                                                        ->label('Lưu nháp')
                                                        ->color('gray')
                                                        ->action(function ($livewire) {
                                                            $livewire->data['is_published'] = false;
                                                            if ($livewire instanceof CreateRecord) {
                                                                $livewire->create();
                                                            } else {
                                                                $livewire->save();
                                                            }
                                                        }),
                                                    Forms\Components\Actions\Action::make('publish_in_card')
                                                        ->label(fn ($record) => ($record?->is_published) ? 'Cập nhật' : 'Xuất bản')
                                                        ->color('primary')
                                                        ->action(function ($livewire, ?Article $record) {
                                                            if ($record === null || ! $record->is_published) {
                                                                $livewire->data['is_published'] = true;
                                                            }
                                                            if ($livewire instanceof CreateRecord) {
                                                                $livewire->create();
                                                            } else {
                                                                $livewire->save();
                                                            }
                                                        }),
                                                ])->fullWidth(),
                                            ]),

                                            // --- Category & Author Card ---
                                            Section::make('Danh mục & Tác giả')->schema([
                                                Select::make('category_id')
                                                    ->label('Chuyên khoa / Danh mục')
                                                    ->options(fn () => Category::getTreeOptions())
                                                    ->placeholder('Chọn danh mục...')
                                                    ->rules(['required'])
                                                    ->searchable()
                                                    ->preload(),

                                                Select::make('author_id')
                                                    ->label('Tác giả')
                                                    ->relationship(
                                                        name: 'author',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn ($query) => $query->orderBy('name')
                                                    )
                                                    ->default(fn () => auth()->id())
                                                    ->rules(['required'])
                                                    ->exists('users', 'id'),
                                            ]),

                                            // --- Thumbnail Card ---
                                            Section::make('Ảnh đại diện')->schema([
                                                FileUpload::make('thumbnail_image')
                                                    ->label('Ảnh đại diện')
                                                    ->image()
                                                    ->directory('articles/featured')
                                                    ->disk('public')
                                                    ->imagePreviewHeight('160')
                                                    ->panelAspectRatio('16:9')
                                                    ->panelLayout('integrated')
                                                    ->compress()
                                                    ->live(),
                                            ]),

                                            // --- Schema Card ---
                                            Section::make('Loại Schema (JSON-LD)')->schema([
                                                Select::make('schema_type')
                                                    ->label('Loại Schema')
                                                    ->options([
                                                        'Article' => 'Article (Bài viết thông thường)',
                                                        'BlogPosting' => 'BlogPosting (Blog)',
                                                        'MedicalWebPage' => 'MedicalWebPage (Trang y tế)',
                                                        'None' => 'None (Không dùng Schema)',
                                                    ])
                                                    ->default('Article')
                                                    ->live()
                                                    ->helperText('MedicalWebPage phù hợp cho bài y tế/phòng khám.'),
                                            ])->collapsed(),
                                        ]),

                                    // Right Column
                                    Grid::make(1)
                                        ->columnSpan(1)
                                        ->schema([
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
                                                            ->label('Từ khóa chính')
                                                            ->placeholder('VD: phòng khám đa khoa cần thơ')
                                                            ->live(debounce: 500)
                                                            ->helperText('Từ khóa bạn muốn bài viết xếp hạng trên Google.'),
                                                        TextInput::make('meta_title')
                                                            ->label('Tiêu đề SEO')
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
                                                            ->label('Mô tả SEO')
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
                                                                        $plain = preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8'));
                                                                        $set('meta_description', mb_substr(trim($plain), 0, 155));
                                                                    })
                                                            )
                                                            ->helperText('Tối ưu: 140-160 ký tự.'),
                                                        TextInput::make('canonical_url')
                                                            ->label('Đường dẫn chuẩn (Canonical)')
                                                            ->placeholder('https://...')
                                                            ->url()
                                                            ->live(debounce: 500)
                                                            ->helperText('Để trống = tự dùng URL bài viết hiện tại.'),
                                                    ]),
                                                    Tabs\Tab::make('Mạng xã hội')->schema([
                                                        TextInput::make('og_title')
                                                            ->label('Tiêu đề Facebook')
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
                                                            ->label('Mô tả Facebook')
                                                            ->rows(2)
                                                            ->live(debounce: 500),
                                                        FileUpload::make('og_image')
                                                            ->label('Ảnh Facebook')
                                                            ->image()
                                                            ->directory('articles/seo')
                                                            ->disk('public')
                                                            ->compress()
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
                                                            ->compress()
                                                            ->live(),
                                                    ]),
                                                    Tabs\Tab::make('Nâng cao')->schema([
                                                        TextInput::make('seo_slug')
                                                            ->label('Slug SEO riêng (nếu khác slug chính)')
                                                            ->placeholder('slug-seo-rieng')
                                                            ->live(debounce: 500)
                                                            ->helperText('Để trống = dùng slug chính.'),
                                                        Toggle::make('robots_index')
                                                            ->label('Cho phép công cụ tìm kiếm lập chỉ mục (Index)')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('robots_follow')
                                                            ->label('Cho phép theo dõi các liên kết (Follow)')
                                                            ->default(true)
                                                            ->live(),
                                                    ]),
                                                ]),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('STT')
                    ->rowIndex()
                    ->alignCenter(),
                Tables\Columns\ImageColumn::make('thumbnail_image')
                    ->label('Ảnh')
                    ->square()
                    ->circular()
                    ->size(40)
                    ->disk('public')
                    ->getStateUsing(function (Article $record): ?string {
                        if (empty($record->thumbnail_image)) {
                            return null;
                        }
                        $url = $record->thumbnail_image;
                        // If it's already a full URL, just return the path part relative to storage
                        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                            return $url;
                        }
                        // Strip leading slashes and storage prefix
                        $path = ltrim($url, '/');
                        if (str_starts_with($path, 'storage/')) {
                            $path = substr($path, strlen('storage/'));
                        }
                        return $path;
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->wrap()
                    ->weight('bold')
                    ->color('gray.800')
                    ->url(fn (Article $record) => route('filament.admin.resources.articles.edit', $record))
                    ->description(fn (Article $record): string => '/' . $record->slug, position: 'below')
                    ->limit(80),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->limit(30)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Chuyên khoa')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Tác giả')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->iconColor('gray')
                    ->placeholder('Chưa xác định')
                    ->default(fn (Article $record): ?string => match ($record->category?->slug) {
                        'nam-khoa' => 'BS. Nguyễn Văn An',
                        'phu-khoa' => 'BS. Trần Thị Mai',
                        default => 'Ban biên tập',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('is_published')
                    ->label('Trạng thái')
                    ->badge()
                    ->getStateUsing(fn (Article $record): string => $record->is_published ? 'Công khai' : 'Bản nháp')
                    ->color(fn (string $state): string => match ($state) {
                        'Công khai' => 'success',
                        'Bản nháp' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Công khai' => 'heroicon-m-check-circle',
                        'Bản nháp' => 'heroicon-m-document-text',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('seo_score')
                    ->label('Điểm SEO')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => ($state === null || $state === 0) ? 'Chưa cấu hình' : (string) $state)
                    ->color(fn ($state): string => match (true) {
                        $state === null || $state === 0 => 'gray',
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    })
                    ->icon(fn ($state): string => match (true) {
                        $state === null || $state === 0 => 'heroicon-m-question-mark-circle',
                        $state >= 80 => 'heroicon-m-sparkles',
                        $state >= 50 => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-x-circle',
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('views_this_month')
                    ->label('Lượt xem')
                    ->getStateUsing(function (Article $record): int {
                        try {
                            if (!\Illuminate\Support\Facades\Schema::hasTable('article_views')) {
                                return 0;
                            }
                            return \App\Models\ArticleView::where('article_id', $record->id)
                                ->where('created_at', '>=', now()->startOfMonth())
                                ->count();
                        } catch (\Exception $e) {
                            return 0;
                        }
                    })
                    ->icon('heroicon-m-eye')
                    ->iconColor('blue')
                    ->sortable(false)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->recordUrl(fn (Article $record) => route('filament.admin.resources.articles.edit', $record))
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Chuyên khoa')
                    ->options(fn () => Category::getTreeOptions())
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        $categoryId = (int) $data['value'];
                        $categoryIds = Category::getDescendantIdsAndSelf($categoryId);

                        return $query->whereIn('category_id', $categoryIds);
                    }),
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Công khai',
                        '0' => 'Bản nháp',
                    ]),
                Tables\Filters\SelectFilter::make('author_id')
                    ->label('Tác giả')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('seo_filter')
                    ->label('Điểm SEO')
                    ->options([
                        'not_configured' => 'Chưa cấu hình SEO',
                        'low' => 'SEO thấp (<50)',
                        'medium' => 'SEO khá (50–79)',
                        'good' => 'SEO tốt (≥80)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'not_configured' => $query->where(fn ($q) => $q->whereNull('seo_score')->orWhere('seo_score', 0)),
                            'low' => $query->where('seo_score', '>', 0)->where('seo_score', '<', 50),
                            'medium' => $query->whereBetween('seo_score', [50, 79]),
                            'good' => $query->where('seo_score', '>=', 80),
                            default => $query,
                        };
                    }),
                Tables\Filters\TernaryFilter::make('has_featured_image')
                    ->label('Ảnh đại diện')
                    ->placeholder('Tất cả')
                    ->trueLabel('Có ảnh đại diện')
                    ->falseLabel('Chưa có ảnh đại diện')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('featured_image'),
                        false: fn ($query) => $query->whereNull('featured_image'),
                    ),
            ])
            ->defaultPaginationPageOption(10)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('Xem trực tiếp')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (Article $record): string => $record->public_url)
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make()
                        ->label('Sửa bài')
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Xóa bài'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Thao tác')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading(fn (Collection $records) => 'Xác nhận xóa '.$records->count().' bài viết?')
                        ->modalDescription('Hành động này sẽ xóa vĩnh viễn các bài viết được chọn khỏi hệ thống. Bạn có chắc chắn muốn tiếp tục?')
                        ->action(function (Collection $records): void {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $record) {
                                    $record->delete();
                                }
                            });

                            Notification::make()
                                ->title('Đã xóa '.$records->count().' bài viết.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('publish')
                        ->label('Công khai hàng loạt')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $record) {
                                    $record->update([
                                        'is_published' => true,
                                        'published_at' => $record->published_at ?? now(),
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Đã công khai '.$records->count().' bài viết.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->role === 'admin'),

                    Tables\Actions\BulkAction::make('draft')
                        ->label('Nháp hàng loạt')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            DB::transaction(function () use ($records) {
                                foreach ($records as $record) {
                                    $record->update([
                                        'is_published' => false,
                                    ]);
                                }
                            });

                            Notification::make()
                                ->title('Đã chuyển '.$records->count().' bài viết về dạng bản nháp.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->role === 'admin'),

                    Tables\Actions\BulkAction::make('changeCategory')
                        ->label('Đổi danh mục hàng loạt')
                        ->icon('heroicon-o-folder')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Collection $records) => 'Đổi danh mục cho '.$records->count().' bài viết?')
                        ->modalDescription('Hành động này sẽ cập nhật danh mục mới cho tất cả các bài viết được chọn. Bạn có chắc chắn muốn tiếp tục?')
                        ->form([
                            Select::make('category_id')
                                ->label('Danh mục mới')
                                ->options(fn () => Category::getTreeOptions())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            DB::transaction(function () use ($records, $data) {
                                foreach ($records as $record) {
                                    $record->update([
                                        'category_id' => $data['category_id'],
                                    ]);
                                }
                            });

                            $categoryName = Category::find($data['category_id'])?->name ?? 'mới';

                            Notification::make()
                                ->title('Đã chuyển '.$records->count().' bài viết sang danh mục '.$categoryName.'.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                        ->visible(fn () => auth()->user()?->role === 'admin'),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'author']);
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
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
            $query = Article::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }
}
