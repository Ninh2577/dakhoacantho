<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class PatientVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Patient Visits Trend';

    protected static ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Lượt khám bệnh nhân trong tuần qua',
                    'data' => [65, 78, 52, 89, 94, 73, 85],
                    'backgroundColor' => 'rgba(30, 64, 175, 0.1)',
                    'borderColor' => '#1e40af',
                    'fill' => 'start',
                ],
            ],
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
