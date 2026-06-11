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
                    'x-on:click' => "window.dispatchEvent(new CustomEvent('sync-tinymce-editors'));"
                ])
                ->action('previewArticle'),
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
        if (empty($this->data['title'])) {
            \Filament\Notifications\Notification::make()
                ->title('Lỗi xem trước')
                ->body('Vui lòng nhập tiêu đề bài viết trước khi xem trước.')
                ->danger()
                ->send();
            return;
        }

        session()->put('article_preview_create', [
            'title' => $this->data['title'] ?? '',
            'slug' => $this->data['slug'] ?? '',
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
