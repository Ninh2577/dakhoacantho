<?php

namespace App\Filament\Pages;

use App\Jobs\ImportWordPressXmlJob;
use App\Models\WordPressImportBatch;
use App\Models\WordPressImportLog;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\WithPagination;

class WordPressImporter extends Page implements HasForms
{
    use InteractsWithForms;
    use WithPagination;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationLabel = 'Nhập dữ liệu WordPress';

    protected static ?string $title = 'Nhập dữ liệu WordPress';

    protected static ?string $slug = 'wordpress-importer';

    protected static ?string $navigationGroup = 'Công cụ hệ thống';

    protected static ?int $navigationSort = 9;

    protected static string $view = 'filament.pages.wordpress-importer';

    // Form data
    public ?array $data = [];

    // Active batch viewer
    public ?int $activeBatchId = null;

    // Filters for logs
    public string $logSearch = '';

    public string $logAction = '';

    public string $logStatus = '';

    public function mount(): void
    {
        $this->form->fill([
            'old_domain' => 'https://dakhoacantho.com',
            'media_mode' => 'storage_uploads',
            'local_media_base_path' => 'public/wp-content/uploads',
            'import_post_types' => ['post', 'page'],
            'import_statuses' => ['publish'],
            'duplicate_mode' => 'skip',
            'dry_run' => true,
        ]);

        $this->activeBatchId = request()->query('batch_id') ? (int) request()->query('batch_id') : null;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cấu hình tệp tin & Domain nguồn')
                    ->description('Cung cấp tệp tin XML WordPress WXR và địa chỉ website cũ.')
                    ->schema([
                        FileUpload::make('xml_file')
                            ->label('Tệp XML WordPress (WXR)')
                            ->acceptedFileTypes(['text/xml', 'application/xml'])
                            ->disk('local')
                            ->directory('imports/wordpress')
                            ->required()
                            ->maxSize(51200) // Max 50MB
                            ->helperText('Chỉ chấp nhận tệp có phần mở rộng .xml được xuất từ tính năng Export của WordPress.'),

                        TextInput::make('old_domain')
                            ->label('Tên miền WordPress cũ')
                            ->placeholder('https://dakhoacantho.com')
                            ->required()
                            ->url()
                            ->helperText('Dùng để quét và thay thế ảnh/liên kết cũ trong nội dung bài viết.'),
                    ])->columns(2),

                Section::make('Xử lý hình ảnh & Đường dẫn đính kèm')
                    ->description('Xác định cách import hình ảnh nổi bật và ảnh trong nội dung bài viết.')
                    ->schema([
                        Select::make('media_mode')
                            ->label('Chế độ lưu trữ ảnh')
                            ->options([
                                'keep_remote' => 'Giữ nguyên URL cũ (Tải từ WordPress cũ)',
                                'public_wp_uploads' => 'Đổi sang thư mục /wp-content/uploads/...',
                                'storage_uploads' => 'Đổi sang Laravel Storage /storage/uploads/...',
                            ])
                            ->required()
                            ->live(),

                        TextInput::make('local_media_base_path')
                            ->label('Đường dẫn cục bộ ảnh cũ (Tùy chọn)')
                            ->placeholder('public/wp-content/uploads')
                            ->helperText('Đường dẫn thư mục chứa ảnh cũ trên máy chủ (ví dụ: public/wp-content/uploads). Trình import sẽ sao chép ảnh sang thư mục đích nếu có tệp tại đây.')
                            ->visible(fn ($get) => $get('media_mode') !== 'keep_remote'),
                    ])->columns(2),

                Section::make('Bộ lọc nội dung & Chế độ trùng lặp')
                    ->description('Lựa chọn kiểu dữ liệu muốn import và cách xử lý khi trùng lặp.')
                    ->schema([
                        CheckboxList::make('import_post_types')
                            ->label('Loại bài viết')
                            ->options([
                                'post' => 'Bài viết (Posts)',
                                'page' => 'Trang tĩnh (Pages)',
                                'attachment' => 'Tệp đính kèm (Attachments)',
                            ])
                            ->required()
                            ->columns(3),

                        CheckboxList::make('import_statuses')
                            ->label('Trạng thái bài viết')
                            ->options([
                                'publish' => 'Đã xuất bản (Publish)',
                                'draft' => 'Bản nháp (Draft)',
                            ])
                            ->required()
                            ->columns(2),

                        Select::make('duplicate_mode')
                            ->label('Xử lý bài viết trùng Slug')
                            ->options([
                                'skip' => 'Bỏ qua không cập nhật (Khuyên dùng)',
                                'update' => 'Cập nhật lại nội dung',
                                'unique' => 'Tạo slug mới duy nhất (Thêm hậu tố -wp-{id})',
                            ])
                            ->required(),

                        Section::make('Giới hạn & Thử nghiệm')
                            ->schema([
                                Toggle::make('dry_run')
                                    ->label('Chạy thử nghiệm (Dry run)')
                                    ->helperText('Mô phỏng toàn bộ tiến trình nhập dữ liệu, tạo bản sao lưu, ghi nhật ký, nhưng KHÔNG thay đổi cơ sở dữ liệu bài viết/danh mục.')
                                    ->default(true),

                                TextInput::make('limit')
                                    ->label('Giới hạn số lượng bài viết')
                                    ->numeric()
                                    ->placeholder('Không giới hạn')
                                    ->helperText('Nhập số lượng để kiểm tra thử nghiệm nhanh (Ví dụ: 5 bài viết).'),
                            ])->columns(2),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function startImport(): void
    {
        $formData = $this->form->getState();

        $batch = WordPressImportBatch::create([
            'file_path' => $formData['xml_file'],
            'original_file_name' => basename($formData['xml_file']),
            'old_domain' => $formData['old_domain'],
            'media_mode' => $formData['media_mode'],
            'local_media_base_path' => $formData['local_media_base_path'] ?? null,
            'import_post_types' => $formData['import_post_types'],
            'import_statuses' => $formData['import_statuses'],
            'duplicate_mode' => $formData['duplicate_mode'],
            'dry_run' => $formData['dry_run'],
            'limit' => $formData['limit'] ? (int) $formData['limit'] : null,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Dispatch background job
        ImportWordPressXmlJob::dispatch($batch->id);

        $this->activeBatchId = $batch->id;

        Notification::make()
            ->title('Bắt đầu tiến trình import!')
            ->body('Trình nhập liệu đang xử lý dưới nền. Bạn có thể theo dõi trực tiếp tại đây.')
            ->success()
            ->send();

        // Reset form
        $this->form->fill([
            'old_domain' => 'https://dakhoacantho.com',
            'media_mode' => 'storage_uploads',
            'local_media_base_path' => 'public/wp-content/uploads',
            'import_post_types' => ['post', 'page'],
            'import_statuses' => ['publish'],
            'duplicate_mode' => 'skip',
            'dry_run' => true,
        ]);
    }

    public function viewBatch(int $batchId): void
    {
        $this->activeBatchId = $batchId;
        $this->resetFilters();
    }

    public function backToList(): void
    {
        $this->activeBatchId = null;
        $this->resetFilters();
    }

    public function resetFilters(): void
    {
        $this->logSearch = '';
        $this->logAction = '';
        $this->logStatus = '';
        $this->resetPage();
    }

    public function getActiveBatchProperty(): ?WordPressImportBatch
    {
        return $this->activeBatchId ? WordPressImportBatch::find($this->activeBatchId) : null;
    }

    public function getLogsProperty()
    {
        if (! $this->activeBatchId) {
            return collect();
        }

        $query = WordPressImportLog::where('batch_id', $this->activeBatchId);

        if ($this->logSearch) {
            $query->where(function ($q) {
                $q->where('source_title', 'like', '%'.$this->logSearch.'%')
                    ->orWhere('source_slug', 'like', '%'.$this->logSearch.'%')
                    ->orWhere('message', 'like', '%'.$this->logSearch.'%')
                    ->orWhere('source_post_id', $this->logSearch);
            });
        }

        if ($this->logAction) {
            $query->where('action', $this->logAction);
        }

        if ($this->logStatus) {
            $query->where('status', $this->logStatus);
        }

        return $query->latest()->paginate(10);
    }

    public function getPastBatchesProperty()
    {
        return WordPressImportBatch::with('creator')->latest()->paginate(10);
    }

    protected function getViewData(): array
    {
        return [
            'activeBatch' => $this->getActiveBatchProperty(),
            'logs' => $this->getLogsProperty(),
            'pastBatches' => $this->getPastBatchesProperty(),
        ];
    }
}
