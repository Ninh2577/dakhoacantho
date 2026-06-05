<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class PatientVisitsChart extends ChartWidget
{
    protected static ?string $heading = 'Lượt tư vấn 7 ngày gần nhất';

    protected static ?string $maxHeight = '280px';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $days   = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));
        $labels = $days->map(fn ($d) => $d->format('d/m'))->toArray();

        $data = $days->map(function ($day) {
            return Consultation::whereDate('created_at', $day)->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Lượt tư vấn',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(30, 64, 175, 0.08)',
                    'borderColor'     => '#1e40af',
                    'borderWidth'     => 2,
                    'pointRadius'     => 4,
                    'fill'            => 'start',
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
