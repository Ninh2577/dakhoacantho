<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Consultation;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalConsultations = Consultation::count();
        $pendingCount = Consultation::where('status', 'pending')->count();
        $publishedArticles = Article::where('is_published', true)->count();
        $lowSeoArticles = Article::where(function ($q) {
            $q->where('seo_score', '<', 50)->orWhereNull('seo_score');
        })->count();
        $avgSeo = Article::whereNotNull('seo_score')->avg('seo_score');
        $newPatientsThisWeek = Patient::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Tổng lượt tư vấn', number_format($totalConsultations))
                ->description('Tất cả yêu cầu tư vấn đã nhận')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Tư vấn chờ xử lý', (string) $pendingCount)
                ->description($pendingCount > 0 ? 'Cần phản hồi sớm!' : 'Đã xử lý hết 🎉')
                ->descriptionIcon($pendingCount > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($pendingCount > 0 ? 'danger' : 'success'),

            Stat::make('Bệnh nhân mới (7 ngày)', (string) $newPatientsThisWeek)
                ->description('Bệnh nhân đã đăng ký trong tuần này')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary'),

            Stat::make('Bài viết đã xuất bản', (string) $publishedArticles)
                ->description('Đang công khai trên website')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make('Bài viết cần tối ưu SEO', (string) $lowSeoArticles)
                ->description('Điểm SEO < 50 hoặc chưa phân tích')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color($lowSeoArticles > 0 ? 'warning' : 'success'),

            Stat::make('Điểm SEO trung bình', $avgSeo ? number_format($avgSeo, 1).'/100' : '—')
                ->description($avgSeo ? ($avgSeo >= 80 ? 'SEO tốt 🟢' : ($avgSeo >= 50 ? 'SEO khá 🟡' : 'Cần cải thiện 🔴')) : 'Chưa có dữ liệu')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgSeo ? ($avgSeo >= 80 ? 'success' : ($avgSeo >= 50 ? 'warning' : 'danger')) : 'gray'),
        ];
    }
}
