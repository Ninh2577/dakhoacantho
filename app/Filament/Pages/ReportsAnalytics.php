<?php

namespace App\Filament\Pages;

use App\Models\Article;
use App\Models\Consultation;
use App\Models\Patient;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ReportsAnalytics extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Báo cáo & Phân tích';
    protected static ?int    $navigationSort  = 6;
    protected static string  $view            = 'filament.pages.reports-analytics';
    protected static ?string $title           = 'Báo cáo & Phân tích';
    protected static ?string $slug            = 'reports-analytics';

    public string $range = '7'; // days

    public function mount(): void
    {
        $this->range = request()->get('range', '7');
    }

    public function setRange(string $range): void
    {
        $this->range = $range;
    }

    protected function getDateFrom(): Carbon
    {
        return match ($this->range) {
            'today'  => Carbon::today(),
            '30'     => Carbon::today()->subDays(29),
            'month'  => Carbon::today()->startOfMonth(),
            default  => Carbon::today()->subDays(6), // 7 days
        };
    }

    public function getStatsProperty(): array
    {
        $from = $this->getDateFrom();

        $totalConsultations    = Consultation::count();
        $pendingConsultations  = Consultation::where('status', 'pending')->count();
        $processedConsultations = Consultation::whereIn('status', ['contacted', 'booked', 'visited'])->count();
        $newPatients           = Patient::where('created_at', '>=', $from)->count();
        $totalPatients         = Patient::count();

        // Conversion rate: patients created from consultations
        $converted   = Consultation::whereNotNull('patient_id')->count();
        $convRate    = $totalConsultations > 0 ? round($converted / $totalConsultations * 100, 1) : 0;

        $totalArticles     = Article::count();
        $publishedArticles = Article::where('is_published', true)->count();
        $avgSeo            = Article::whereNotNull('seo_score')->avg('seo_score');

        return compact(
            'totalConsultations', 'pendingConsultations', 'processedConsultations',
            'newPatients', 'totalPatients', 'convRate',
            'totalArticles', 'publishedArticles', 'avgSeo'
        );
    }

    public function getConsultationTrendProperty(): array
    {
        $from = $this->getDateFrom();
        $days = [];
        $current = $from->copy();
        while ($current->lte(Carbon::today())) {
            $days[] = $current->copy();
            $current->addDay();
        }

        return [
            'labels' => collect($days)->map(fn ($d) => $d->format('d/m'))->toArray(),
            'data'   => collect($days)->map(fn ($d) => Consultation::whereDate('created_at', $d)->count())->toArray(),
        ];
    }

    public function getPendingConsultationsProperty()
    {
        return Consultation::where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getLowSeoArticlesProperty()
    {
        return Article::where(fn ($q) => $q->where('seo_score', '<', 50)->orWhereNull('seo_score'))
            ->with('category')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getSpecialtyStatsProperty()
    {
        return Consultation::whereNotNull('department')
            ->where('department', '!=', '')
            ->selectRaw('department, count(*) as consultations')
            ->groupBy('department')
            ->orderByDesc('consultations')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $articleCount = Article::whereHas('category', fn ($q) => $q->where('name', 'like', "%{$row->department}%"))->count();
                return [
                    'department'   => $row->department,
                    'consultations' => $row->consultations,
                    'articles'     => $articleCount,
                ];
            });
    }

    protected function getViewData(): array
    {
        return [
            'stats'                => $this->getStatsProperty(),
            'consultationTrend'    => $this->getConsultationTrendProperty(),
            'pendingConsultations' => $this->getPendingConsultationsProperty(),
            'lowSeoArticles'       => $this->getLowSeoArticlesProperty(),
            'specialtyStats'       => $this->getSpecialtyStatsProperty(),
            'range'                => $this->range,
        ];
    }
}
