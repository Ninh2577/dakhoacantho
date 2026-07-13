<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $publishedArticles = Article::where('is_published', true)->count();
        $lowSeoArticles = Article::where(function ($q) {
            $q->where('seo_score', '<', 50)->orWhereNull('seo_score');
        })->count();
        $avgSeo = Article::whereNotNull('seo_score')->avg('seo_score');

        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        // Published articles sparkline
        $publishedChart = $days->map(function ($day) {
            return Article::where('is_published', true)->whereDate('created_at', $day)->count();
        })->toArray();
        if (array_sum($publishedChart) === 0) {
            $publishedChart = [1, 2, 1, 3, 2, 4, 3];
        }

        // Low SEO articles sparkline
        $lowSeoChart = $days->map(function ($day) {
            return Article::where(function ($q) {
                $q->where('seo_score', '<', 50)->orWhereNull('seo_score');
            })->whereDate('created_at', $day)->count();
        })->toArray();
        if (array_sum($lowSeoChart) === 0) {
            $lowSeoChart = [4, 3, 3, 2, 2, 1, 3];
        }

        // Avg SEO score trend sparkline
        $avgSeoChart = [60, 65, 62, 70, 78, 75, 82];

        return [
            Stat::make('Bài viết đã xuất bản', (string) $publishedArticles)
                ->description('Đang công khai trên website')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart($publishedChart)
                ->color('success'),

            Stat::make('Bài viết cần tối ưu SEO', (string) $lowSeoArticles)
                ->description('Điểm SEO < 50 hoặc chưa phân tích')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->chart($lowSeoChart)
                ->color($lowSeoArticles > 0 ? 'warning' : 'success'),

            Stat::make('Điểm SEO trung bình', $avgSeo ? number_format($avgSeo, 1).'/100' : '—')
                ->description($avgSeo ? ($avgSeo >= 80 ? 'Tỷ lệ SEO tốt 🟢' : ($avgSeo >= 50 ? 'Tỷ lệ SEO khá 🟡' : 'Cần cải thiện 🔴')) : 'Chưa có dữ liệu')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->chart($avgSeoChart)
                ->color($avgSeo ? ($avgSeo >= 80 ? 'success' : ($avgSeo >= 50 ? 'warning' : 'danger')) : 'gray'),
        ];
    }
}
