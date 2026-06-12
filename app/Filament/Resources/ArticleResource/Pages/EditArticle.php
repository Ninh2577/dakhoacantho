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

    public function previewArticle(): void
    {
        \Log::info('EDIT_PREVIEW_ARTICLE_CALLED', [
            'title' => $this->data['title'] ?? null,
            'slug' => $this->data['slug'] ?? null,
            'all_data' => $this->data,
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

        session()->put('article_preview_create', [
            'title' => $title,
            'slug' => $this->data['slug'] ?? ($this->record->slug ?? ''),
            'content' => $this->data['content'] ?? '',
            'excerpt' => $this->data['excerpt'] ?? '',
            'featured_image' => $this->data['featured_image'] ?? null,
            'category_id' => $this->data['category_id'] ?? null,
            'meta_title' => $this->data['meta_title'] ?? null,
            'meta_description' => $this->data['meta_description'] ?? null,
            'canonical_url' => $this->data['canonical_url'] ?? null,
            'schema_type' => $this->data['schema_type'] ?? 'Article',
            'og_title' => $this->data['og_title'] ?? null,
            'og_description' => $this->data['og_description'] ?? null,
            'og_image' => $this->data['og_image'] ?? null,
            'twitter_title' => $this->data['twitter_title'] ?? null,
            'twitter_description' => $this->data['twitter_description'] ?? null,
            'twitter_image' => $this->data['twitter_image'] ?? null,
            'previewed_at' => now()->toDateTimeString(),
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
        $article = $this->record;
        $article->fill($data);

        $analyzer = new \App\Services\ArticleSeoAnalyzerService();
        $result = $analyzer->analyze($article);

        $data['seo_score'] = $result['score'];
        $data['seo_checks'] = $result;

        return $data;
    }
}
