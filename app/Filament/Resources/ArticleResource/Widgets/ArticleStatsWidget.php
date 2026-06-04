<?php

namespace App\Filament\Resources\ArticleResource\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ArticleStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('ĐÃ XUẤT BẢN', Article::where('is_published', true)->count())
                ->description('Bài viết đã công khai')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('BẢN NHÁP', Article::where('is_published', false)->count())
                ->description('Bài viết đang biên tập')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),
            Stat::make('LƯỢT XEM THÁNG NÀY', '12.5k')
                ->description('Tăng 15% so với tháng trước')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}
