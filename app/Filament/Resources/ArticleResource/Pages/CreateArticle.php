<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    public function getTitle(): string
    {
        return 'Thêm bài viết';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
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
            Actions\Action::make('save_draft')
                ->label('Lưu nháp')
                ->color('gray')
                ->action(function () {
                    $this->data['is_published'] = false;
                    $this->create();
                }),
            Actions\Action::make('publish')
                ->label('Xuất bản')
                ->color('primary')
                ->action(function () {
                    $this->data['is_published'] = true;
                    $this->create();
                }),
        ];
    }

    public function previewArticle(): void
    {
        try {
            \Log::info('CREATE_PREVIEW_ARTICLE_CALLED', [
                'title' => $this->data['title'] ?? null,
                'slug'  => $this->data['slug'] ?? null,
            ]);

            $title = $this->data['title'] ?? '';
            if (empty($title)) {
                $title = 'Bản xem trước';
                \Filament\Notifications\Notification::make()
                    ->title('Cảnh báo xem trước')
                    ->body('Tiêu đề đang trống, sử dụng tên tạm "Bản xem trước" để hiển thị.')
                    ->warning()
                    ->send();
            }

            // FileUpload fields may return array on some hosting environments — extract scalar
            $featuredImage  = $this->extractFileUploadPath($this->data['featured_image'] ?? null);
            $ogImage        = $this->extractFileUploadPath($this->data['og_image'] ?? null);
            $twitterImage   = $this->extractFileUploadPath($this->data['twitter_image'] ?? null);

            session()->put('article_preview_create', [
                'title'               => $title,
                'slug'                => $this->data['slug'] ?? '',
                'content'             => $this->data['content'] ?? '',
                'excerpt'             => $this->data['excerpt'] ?? '',
                'featured_image'      => $featuredImage,
                'category_id'         => $this->data['category_id'] ?? null,
                'meta_title'          => $this->data['meta_title'] ?? null,
                'meta_description'    => $this->data['meta_description'] ?? null,
                'canonical_url'       => $this->data['canonical_url'] ?? null,
                'schema_type'         => $this->data['schema_type'] ?? 'Article',
                'og_title'            => $this->data['og_title'] ?? null,
                'og_description'      => $this->data['og_description'] ?? null,
                'og_image'            => $ogImage,
                'twitter_title'       => $this->data['twitter_title'] ?? null,
                'twitter_description' => $this->data['twitter_description'] ?? null,
                'twitter_image'       => $twitterImage,
                'previewed_at'        => now()->toDateTimeString(),
            ]);

            $this->dispatch('open-preview', url: url('/admin/articles/preview-create'));

            \Filament\Notifications\Notification::make()
                ->title('Bản xem trước đã sẵn sàng')
                ->body('Nếu trình duyệt không tự động mở tab mới, hãy bấm nút bên dưới.')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('open')
                        ->label('Mở bản xem trước')
                        ->url(url('/admin/articles/preview-create'), shouldOpenInNewTab: true)
                        ->button()
                        ->color('primary'),
                ])
                ->success()
                ->send();

        } catch (\Throwable $e) {
            \Log::error('CREATE_PREVIEW_ARTICLE_ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            \Filament\Notifications\Notification::make()
                ->title('Lỗi xem trước')
                ->body('Không thể tạo bản xem trước: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Extract a scalar file path from a FileUpload field value.
     * Filament FileUpload may return a string, an array with one path, or null.
     */
    private function extractFileUploadPath(mixed $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        if (is_string($value)) {
            return $value ?: null;
        }
        if (is_array($value)) {
            // Flatten and get first non-empty scalar value
            $first = collect($value)
                ->flatten()
                ->filter(fn ($item) => is_string($item) && $item !== '')
                ->first();
            return $first ?? null;
        }
        return null;
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()->label('Tạo bài viết'),
            $this->getCreateAnotherFormAction()->label('Tạo và thêm bài khác'),
            $this->getCancelFormAction()->label('Hủy'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $article = new \App\Models\Article($data);
        $analyzer = new \App\Services\ArticleSeoAnalyzerService();
        $result = $analyzer->analyze($article);

        $data['seo_score'] = $result['score'];
        $data['seo_checks'] = $result;

        return $data;
    }
}
