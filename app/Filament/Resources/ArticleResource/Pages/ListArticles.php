<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;

    public function getTitle(): string
    {
        return 'Bài viết';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Thêm bài viết'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ArticleResource\Widgets\ArticleStatsWidget::class,
        ];
    }
}
