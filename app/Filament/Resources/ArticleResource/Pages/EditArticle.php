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
                ->url(fn ($record): string => $record->public_url)
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()->label('Xóa'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $article = $this->record;
        $article->fill($data);

        $analyzer = new \App\Services\ArticleSeoAnalyzerService();
        $result = $analyzer->analyze($article);

        $data['seo_score'] = $result['score'];
        $data['seo_checks'] = json_encode($result);

        return $data;
    }
}
