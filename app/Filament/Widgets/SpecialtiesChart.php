<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class SpecialtiesChart extends ChartWidget
{
    protected static ?string $heading = 'Specialties Distribution';

    protected static ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Specialties',
                    'data' => [45, 30, 25],
                    'backgroundColor' => [
                        '#1e40af', // Nam Khoa
                        '#38bdf8', // Phụ Khoa
                        '#93c5fd', // Khám Tổng Quát
                    ],
                ],
            ],
            'labels' => ['Nam Khoa', 'Phụ Khoa', 'Khám Tổng Quát'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
