<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    public function getTitle(): string
    {
        return 'Sửa bài viết';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Xem trước')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url('#')
                ->extraAttributes([
                    'x-on:click' => "
                        \$event.preventDefault();
                        \$event.stopImmediatePropagation();
                        window.triggerArticlePreview(\$wire, \$el);
                    "
                ]),
            Actions\Action::make('save_draft')
                ->label('Lưu nháp')
                ->color('gray')
                ->action(function () {
                    $this->data['is_published'] = false;
                    $this->save();
                }),
            Actions\Action::make('publish')
                ->label($this->record->is_published ? 'Cập nhật' : 'Xuất bản')
                ->color('primary')
                ->action(function () {
                    $this->data['is_published'] = true;
                    $this->save();
                }),
            Actions\DeleteAction::make()->label('Xóa'),
        ];
    }

    public function previewArticle(?string $content = null): void
    {
        try {
            // If content is passed directly (from TinyMCE JS sync), use it.
            // This avoids a race condition where $this->data['content'] is stale.
            if ($content !== null) {
                $this->data['content'] = $content;
            }

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

            $previewUuid = (string) \Illuminate\Support\Str::uuid();

            \Illuminate\Support\Facades\Log::info('EditArticle PREVIEW_BEFORE_SAVE', [
                'preview_uuid' => $previewUuid,
                'session_driver' => config('session.driver'),
                'session_key_name' => 'article_preview_create',
                'session_id' => session()->getId(),
                'title' => $title,
                'slug' => $this->data['slug'] ?? ($this->record->slug ?? ''),
                'article_id' => $this->record->id ?? null,
            ]);

            session()->put('article_preview_create', [
                'preview_uuid'        => $previewUuid,
                'title'               => $title,
                'slug'                => $this->data['slug'] ?? ($this->record->slug ?? ''),
                'content'             => $this->data['content'] ?? '',
                'excerpt'             => $this->data['excerpt'] ?? '',
                'featured_image'      => $featuredImage,
                'category_id'         => $this->data['category_id'] ?? null,
                'author_id'           => $this->data['author_id'] ?? auth()->id(),
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

            // Force immediate session flush to DB before JS opens the preview tab.
            session()->save();

            $previewPayload = session('article_preview_create');
            $payloadSerialized = serialize($previewPayload);
            $payloadSize = strlen($payloadSerialized);

            $previewUrl = url('/admin/articles/preview-create?preview=' . $previewUuid);

            \Illuminate\Support\Facades\Log::info('EditArticle PREVIEW_SAVED', [
                'preview_uuid' => $previewUuid,
                'session_driver' => config('session.driver'),
                'session_key_name' => 'article_preview_create',
                'session_id' => session()->getId(),
                'auth_id' => auth()->id(),
                'has_preview' => session()->has('article_preview_create'),
                'payload_size' => $payloadSize,
                'cookie_header' => request()->header('cookie'),
                'session_cookie_name' => config('session.cookie'),
                'preview_url' => $previewUrl,
            ]);

            $this->dispatch('open-preview', url: $previewUrl);

        } catch (\Throwable $e) {
            $this->dispatch('open-preview-failed');
            \Log::error('EDIT_PREVIEW_ARTICLE_ERROR', [
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
            $this->getSaveFormAction()->label('Cập nhật'),
            $this->getCancelFormAction()->label('Hủy'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (blank($data['author_id'] ?? null)) {
            $data['author_id'] = auth()->id();
        }

        $article = $this->record;
        $article->fill($data);

        $analyzer = new \App\Services\ArticleSeoAnalyzerService();
        $result = $analyzer->analyze($article);

        $data['seo_score'] = $result['score'];
        $data['seo_checks'] = $result;

        return $data;
    }
}
