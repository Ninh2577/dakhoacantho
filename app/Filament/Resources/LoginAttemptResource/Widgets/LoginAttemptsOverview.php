<?php

namespace App\Filament\Resources\LoginAttemptResource\Widgets;

use App\Models\LoginAttempt;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoginAttemptsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayTotal = LoginAttempt::whereDate('created_at', today())->count();
        $todayFailed = LoginAttempt::where('successful', false)->whereDate('created_at', today())->count();
        $totalFailed = LoginAttempt::where('successful', false)->count();

        // Calculate rate of success vs failure today
        $failRate = $todayTotal > 0 ? round(($todayFailed / $todayTotal) * 100, 1) : 0;

        return [
            Stat::make('Tổng đăng nhập hôm nay', $todayTotal)
                ->description('Tất cả lượt kết nối hệ thống')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
                
            Stat::make('Đăng nhập thất bại hôm nay', $todayFailed)
                ->description($failRate . '% tỷ lệ lỗi hôm nay')
                ->descriptionIcon($todayFailed > 5 ? 'heroicon-m-shield-exclamation' : 'heroicon-m-check-badge')
                ->color($todayFailed > 5 ? 'danger' : 'warning'),

            Stat::make('Tổng số sự cố thất bại', $totalFailed)
                ->description('Theo dõi các IP bất thường')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalFailed > 20 ? 'danger' : 'gray'),
        ];
    }
}
