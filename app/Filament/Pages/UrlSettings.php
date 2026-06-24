<?php

namespace App\Filament\Pages;

use App\Jobs\RecompileUrlPathsJob;
use App\Models\Article;
use App\Models\Category;
use App\Models\Setting;
use App\Models\UrlSettingHistory;
use App\Services\UrlRoutingService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class UrlSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Cấu hình URL động';

    protected static ?string $title = 'Cấu hình URL động';

    protected static ?string $slug = 'url-settings';

    protected static ?string $navigationGroup = 'Công cụ hệ thống';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.url-settings';

    public ?array $data = [];

    // Preview state
    public bool $hasPreviewed = false;

    public int $conflictCount = 0;

    public int $redirectCount = 0;

    public array $conflicts = [];

    public array $previewExamples = [];

    // Job active state
    public ?int $activeHistoryId = null;

    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }

    public function mount(): void
    {
        $this->form->fill([
            'article_pattern' => Setting::get('url_pattern_article') ?: '{slug}',
            'category_pattern' => Setting::get('url_pattern_category') ?: '{categories}',
        ]);

        // Check if there is an active running recompile job
        $running = UrlSettingHistory::whereIn('status', ['pending', 'processing'])->latest()->first();
        if ($running) {
            $this->activeHistoryId = $running->id;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Cấu hình định dạng URL')
                    ->description('Tùy chỉnh định dạng đường dẫn cho Bài viết và Chuyên khoa/Danh mục. Sử dụng các thẻ placeholder hợp lệ.')
                    ->schema([
                        TextInput::make('article_pattern')
                            ->label('Định dạng URL bài viết')
                            ->required()
                            ->placeholder('{categories}/{slug}.html')
                            ->helperText('Hỗ trợ các thẻ: {slug}, {category}, {categories}. Ví dụ: {categories}/{slug}.html hoặc category/{slug}'),

                        TextInput::make('category_pattern')
                            ->label('Định dạng URL chuyên khoa/danh mục')
                            ->required()
                            ->placeholder('category/{categories}')
                            ->helperText('Hỗ trợ các thẻ: {slug}, {categories}. Ví dụ: category/{categories} hoặc {slug}'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    /**
     * Apply a quick suggestion pattern to the form inputs.
     */
    public function selectSuggestion(string $artPat, string $catPat): void
    {
        $this->form->fill([
            'article_pattern' => $artPat,
            'category_pattern' => $catPat,
        ]);
        $this->hasPreviewed = false;
    }

    /**
     * Run in-memory simulation to check for conflicts and build examples list.
     */
    public function previewChanges(): void
    {
        $formData = $this->form->getState();
        $artPattern = trim($formData['article_pattern']);
        $catPattern = trim($formData['category_pattern']);

        $service = app(UrlRoutingService::class);

        // 1. Validate pattern allowlist
        if (! $service->validateArticlePattern($artPattern)) {
            Notification::make()
                ->title('Định dạng bài viết không hợp lệ')
                ->body('Vui lòng chọn hoặc nhập định dạng bài viết trong danh sách được hỗ trợ.')
                ->danger()
                ->send();

            return;
        }

        if (! $service->validateCategoryPattern($catPattern)) {
            Notification::make()
                ->title('Định dạng danh mục không hợp lệ')
                ->body('Vui lòng chọn hoặc nhập định dạng danh mục trong danh sách được hỗ trợ.')
                ->danger()
                ->send();

            return;
        }

        // 2. Perform conflict check
        $check = $service->checkConflicts($artPattern, $catPattern);
        $this->conflicts = $check['conflicts'];
        $this->conflictCount = $check['conflict_count'];

        // 3. Build Preview Examples (top 10 categories + top 10 articles)
        $this->previewExamples = [];
        $this->redirectCount = 0;

        $categories = Category::take(10)->get();
        foreach ($categories as $cat) {
            $oldPath = $cat->url_path ?: $cat->full_path;
            $newPath = $service->compileCategoryPath($cat, $catPattern);
            if ($oldPath !== $newPath && ! empty($oldPath)) {
                $this->redirectCount++;
            }
            $this->previewExamples[] = [
                'type' => 'Danh mục',
                'name' => $cat->name,
                'old' => '/'.ltrim($oldPath, '/'),
                'new' => '/'.ltrim($newPath, '/'),
            ];
        }

        $articles = Article::with('category')->take(10)->get();
        foreach ($articles as $art) {
            $oldPath = $art->url_path ?: $art->slug;
            $newPath = $service->compileArticlePath($art, $artPattern);
            if ($oldPath !== $newPath && ! empty($oldPath)) {
                $this->redirectCount++;
            }
            $this->previewExamples[] = [
                'type' => 'Bài viết',
                'name' => $art->title,
                'old' => '/'.ltrim($oldPath, '/'),
                'new' => '/'.ltrim($newPath, '/'),
            ];
        }

        $this->hasPreviewed = true;

        Notification::make()
            ->title('Đã tạo bản xem trước!')
            ->body("Tìm thấy {$this->conflictCount} xung đột.")
            ->success()
            ->send();
    }

    /**
     * Dispatch queue job to perform full recompile.
     */
    public function applyChanges(): void
    {
        if (! $this->hasPreviewed || $this->conflictCount > 0) {
            Notification::make()
                ->title('Không thể áp dụng cấu trúc')
                ->body('Bạn cần chạy xem trước và xử lý toàn bộ xung đột trước khi áp dụng.')
                ->danger()
                ->send();

            return;
        }

        $formData = $this->form->getState();
        $artPattern = trim($formData['article_pattern']);
        $catPattern = trim($formData['category_pattern']);

        // Check if there is already a processing job to prevent concurrency issues
        $running = UrlSettingHistory::whereIn('status', ['pending', 'processing'])->first();
        if ($running) {
            Notification::make()
                ->title('Thao tác bị khóa')
                ->body('Có một tiến trình biên dịch đang chạy dưới nền. Vui lòng đợi.')
                ->warning()
                ->send();

            return;
        }

        // Create setting history run
        $history = UrlSettingHistory::create([
            'old_article_pattern' => Setting::get('url_pattern_article'),
            'new_article_pattern' => $artPattern,
            'old_category_pattern' => Setting::get('url_pattern_category'),
            'new_category_pattern' => $catPattern,
            'conflict_count' => $this->conflictCount,
            'redirect_count' => $this->redirectCount,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        // Dispatch background job
        RecompileUrlPathsJob::dispatch($artPattern, $catPattern, $history->id);

        $this->activeHistoryId = $history->id;

        Notification::make()
            ->title('Đã đưa tiến trình vào hàng đợi!')
            ->body('Hệ thống đang tiến hành backup cơ sở dữ liệu và biên dịch đường dẫn dưới nền.')
            ->success()
            ->send();
    }

    /**
     * Retrieve status of active recompilation run.
     */
    public function getActiveHistoryProperty(): ?UrlSettingHistory
    {
        return $this->activeHistoryId ? UrlSettingHistory::find($this->activeHistoryId) : null;
    }

    /**
     * Revert the last compile.
     */
    public function triggerRollback(): void
    {
        $exitCode = Artisan::call('urls:rollback-last');

        if ($exitCode === 0) {
            Notification::make()
                ->title('Khôi phục thành công!')
                ->body('Đường dẫn URL đã được khôi phục về phiên bản trước.')
                ->success()
                ->send();

            // Refresh form
            $this->mount();
            $this->hasPreviewed = false;
        } else {
            Notification::make()
                ->title('Khôi phục thất bại')
                ->body('Vui lòng kiểm tra nhật ký hoặc chạy lệnh trong console.')
                ->danger()
                ->send();
        }
    }
}
