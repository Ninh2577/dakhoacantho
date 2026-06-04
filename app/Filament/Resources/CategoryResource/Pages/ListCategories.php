<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use SolutionForest\FilamentTree\Resources\Pages\TreePage;
use SolutionForest\FilamentTree\Actions;

class ListCategories extends TreePage
{
    protected static string $resource = CategoryResource::class;

    protected static int $maxDepth = 3;

    protected function getTreeActions(): array
    {
        return [
            Actions\EditAction::make()
                ->url(fn ($record) => CategoryResource::getUrl('edit', ['record' => $record])),
            Actions\DeleteAction::make(),
        ];
    }

    public static function tree(\SolutionForest\FilamentTree\Components\Tree $tree): \SolutionForest\FilamentTree\Components\Tree
    {
        return CategoryResource::tree($tree);
    }
}
