<?php

namespace App\Filament\Pages;

use App\Models\Article;
use App\Models\Category;
use App\Models\ArticleComment;
use App\Models\MediaFile;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsAnalytics extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user() && auth()->user()->hasPermission(static::class);
    }

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $activeNavigationIcon = 'heroicon-m-chart-bar-square';

    protected static ?string $navigationLabel = 'Báo cáo & Phân tích';

    protected static ?string $navigationGroup = 'Báo cáo & Phân tích';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.reports-analytics';

    protected static ?string $title = 'Báo cáo & Phân tích';

    protected static ?string $slug = 'reports-analytics';

    public string $range = '7'; // days or 'custom'
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->range = request()->get('range', '7');
        
        list($from, $to) = $this->getDateRange();
        $this->dateFrom = $from->format('Y-m-d');
        $this->dateTo = $to->format('Y-m-d');
    }

    public function setRange(string $range): void
    {
        $this->range = $range;
        
        list($from, $to) = $this->getDateRange();
        $this->dateFrom = $from->format('Y-m-d');
        $this->dateTo = $to->format('Y-m-d');
    }

    public function updatedDateFrom(): void
    {
        $this->range = 'custom';
    }

    public function updatedDateTo(): void
    {
        $this->range = 'custom';
    }

    protected function getDateRange(): array
    {
        if ($this->range === 'custom') {
            $from = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : Carbon::today()->subDays(6)->startOfDay();
            $to = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : Carbon::today()->endOfDay();
            return [$from, $to];
        }

        $to = Carbon::today()->endOfDay();
        $from = match ($this->range) {
            'today' => Carbon::today()->startOfDay(),
            '30' => Carbon::today()->subDays(29)->startOfDay(),
            'month' => Carbon::today()->startOfMonth()->startOfDay(),
            default => Carbon::today()->subDays(6)->startOfDay(), // 7 days
        };

        return [$from, $to];
    }

    public function getStatsProperty(): array
    {
        list($from, $to) = $this->getDateRange();

        $totalArticles = Article::count();
        $newArticles = Article::whereBetween('created_at', [$from, $to])->count();
        $publishedArticles = Article::where('is_published', true)->count();
        $draftArticles = Article::where('is_published', false)->count();
        $avgSeo = Article::where('is_published', true)->whereNotNull('seo_score')->avg('seo_score');

        $totalComments = ArticleComment::count();
        $pendingComments = ArticleComment::where('status', 'pending')->count();
        $approvedComments = ArticleComment::where('status', 'approved')->count();
        $newComments = ArticleComment::whereBetween('created_at', [$from, $to])->count();

        $totalMediaFiles = MediaFile::count();
        $totalMediaSizeBytes = MediaFile::sum('file_size');
        // Convert to MB
        $totalMediaSizeMB = round($totalMediaSizeBytes / (1024 * 1024), 1);

        return compact(
            'totalArticles', 'newArticles', 'publishedArticles', 'draftArticles', 'avgSeo',
            'totalComments', 'pendingComments', 'approvedComments', 'newComments',
            'totalMediaFiles', 'totalMediaSizeMB'
        );
    }

    public function getTrendProperty(): array
    {
        list($from, $to) = $this->getDateRange();
        
        // Safety check to prevent generating too many days if user selects a massive range
        $diffInDays = $from->diffInDays($to);
        if ($diffInDays > 90) {
            $from = $to->copy()->subDays(90);
        }

        $days = [];
        $current = $from->copy();
        while ($current->lte($to)) {
            $days[] = $current->copy();
            $current->addDay();
        }

        $labels = collect($days)->map(fn ($d) => $d->format('d/m'))->toArray();
        
        $articlesData = collect($days)->map(function ($d) {
            return Article::whereDate('created_at', $d)->count();
        })->toArray();

        $commentsData = collect($days)->map(function ($d) {
            return ArticleComment::whereDate('created_at', $d)->count();
        })->toArray();

        return [
            'labels' => $labels,
            'articles' => $articlesData,
            'comments' => $commentsData,
        ];
    }

    public function getLowSeoArticlesProperty()
    {
        return Article::where(fn ($q) => $q->where('seo_score', '<', 50)->orWhereNull('seo_score'))
            ->with('category')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getCategoryStatsProperty()
    {
        return Category::withCount('articles')
            ->orderByDesc('articles_count')
            ->limit(8)
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStatsProperty(),
            'trend' => $this->getTrendProperty(),
            'lowSeoArticles' => $this->getLowSeoArticlesProperty(),
            'categoryStats' => $this->getCategoryStatsProperty(),
            'range' => $this->range,
        ];
    }
}
