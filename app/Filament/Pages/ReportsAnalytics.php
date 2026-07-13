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

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Báo cáo & Phân tích';

    protected static ?string $navigationGroup = 'Báo cáo & Phân tích';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.reports-analytics';

    protected static ?string $title = 'Báo cáo & Phân tích';

    protected static ?string $slug = 'reports-analytics';

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
            'today' => Carbon::today(),
            '30' => Carbon::today()->subDays(29),
            'month' => Carbon::today()->startOfMonth(),
            default => Carbon::today()->subDays(6), // 7 days
        };
    }

    public function getStatsProperty(): array
    {
        $from = $this->getDateFrom();

        $totalArticles = Article::count();
        $newArticles = Article::where('created_at', '>=', $from)->count();
        $publishedArticles = Article::where('is_published', true)->count();
        $draftArticles = Article::where('is_published', false)->count();
        $avgSeo = Article::where('is_published', true)->whereNotNull('seo_score')->avg('seo_score');

        $totalComments = ArticleComment::count();
        $pendingComments = ArticleComment::where('status', 'pending')->count();
        $approvedComments = ArticleComment::where('status', 'approved')->count();
        $newComments = ArticleComment::where('created_at', '>=', $from)->count();

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
        $from = $this->getDateFrom();
        $days = [];
        $current = $from->copy();
        while ($current->lte(Carbon::today())) {
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
