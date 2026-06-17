<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Filament\Widgets\ChartWidget;

class SpecialtiesChart extends ChartWidget
{
    protected static ?string $heading = 'Tư vấn theo chuyên khoa';

    protected static ?string $maxHeight = '280px';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $data = Consultation::whereNotNull('department')
            ->where('department', '!=', '')
            ->selectRaw('department, count(*) as total')
            ->groupBy('department')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [['label' => 'Chuyên khoa', 'data' => [0], 'backgroundColor' => ['#94a3b8']]],
                'labels' => ['Chưa có dữ liệu'],
            ];
        }

        $palette = ['#1e40af', '#38bdf8', '#93c5fd', '#bfdbfe', '#6366f1', '#a78bfa'];

        return [
            'datasets' => [
                [
                    'label' => 'Số tư vấn',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => array_slice($palette, 0, $data->count()),
                ],
            ],
            'labels' => $data->pluck('department')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
