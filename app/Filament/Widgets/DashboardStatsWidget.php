<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingCount = Consultation::where('status', 'pending')->count();

        return [
            Stat::make('Total Patients', '12,482')
                ->description('8.4% so với tháng trước')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Appointments Today', '84')
                ->description('12 lịch hẹn đang chờ duyệt')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            Stat::make('Revenue (Monthly)', 'đ1.2B')
                ->description('14% tăng trưởng doanh thu')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pending Consultations', (string)$pendingCount)
                ->description('Yêu cầu phản hồi sớm')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($pendingCount > 0 ? 'danger' : 'success'),
        ];
    }
}
