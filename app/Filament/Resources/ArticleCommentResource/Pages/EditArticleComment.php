<?php

namespace App\Filament\Resources\ArticleCommentResource\Pages;

use App\Filament\Resources\ArticleCommentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleComment extends EditRecord
{
    protected static string $resource = ArticleCommentResource::class;

    public function getTitle(): string
    {
        return 'Sửa bình luận';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
