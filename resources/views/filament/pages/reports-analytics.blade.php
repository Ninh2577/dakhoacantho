<x-filament-panels::page>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<style>
    .cms-reports-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }
    @media (min-width: 640px) {
        .cms-reports-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (min-width: 1024px) {
        .cms-reports-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
    .cms-stat-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 155px;
        transition: all 0.3s ease;
    }
    .cms-stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
</style>

{{-- Header & Range Filter --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-2">
    <div>
        <p class="text-sm text-gray-500 font-medium">Theo dõi và phân tích hiệu suất nội dung của website Phòng Khám Đa Khoa Cần Thơ</p>
    </div>
    <div class="flex items-center gap-2 self-start md:self-auto bg-gray-100 p-1 rounded-xl" style="background-color: #f1f5f9; padding: 4px;">
        @foreach(['today' => 'Hôm nay', '7' => '7 ngày', '30' => '30 ngày', 'month' => 'Tháng này'] as $key => $label)
            <button
                wire:click="setRange('{{ $key }}')"
                class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all duration-200"
                style="{{ $range === $key ? 'background-color: #ffffff; color: #0f172a; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); border-radius: 8px;' : 'color: #64748b; background: transparent; border: none;' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

{{-- Stats Grid --}}
<div class="cms-reports-grid">
    <!-- Articles Card -->
    <div class="cms-stat-card">
        <x-heroicon-o-document-text style="position: absolute; right: -12px; bottom: -12px; width: 96px; height: 96px; color: #f1f5f9; z-index: 0; pointer-events: none;" />
        <div style="position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; width: 100%;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 6px; background-color: #eff6ff; color: #1d4ed8;">Bài viết</span>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">+{{ $stats['newArticles'] }} mới</span>
                </div>
                <div style="font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1.1; margin-top: 12px; margin-bottom: 4px;">{{ number_format($stats['totalArticles']) }}</div>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 12px; padding-top: 8px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <span>Đã đăng: <strong style="color: #334155;">{{ number_format($stats['publishedArticles']) }}</strong></span>
                <span>Bản nháp: <strong style="color: #334155;">{{ number_format($stats['draftArticles']) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Comments Card -->
    <div class="cms-stat-card">
        <x-heroicon-o-chat-bubble-left-right style="position: absolute; right: -12px; bottom: -12px; width: 96px; height: 96px; color: #f1f5f9; z-index: 0; pointer-events: none;" />
        <div style="position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; width: 100%;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 6px; background-color: #ecfdf5; color: #047857;">Bình luận</span>
                    @if($stats['pendingComments'] > 0)
                        <span style="font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; background-color: #fee2e2; color: #b91c1c; animation: pulse 2s infinite;">{{ $stats['pendingComments'] }} chờ duyệt</span>
                    @else
                        <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">+{{ $stats['newComments'] }} mới</span>
                    @endif
                </div>
                <div style="font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1.1; margin-top: 12px; margin-bottom: 4px;">{{ number_format($stats['totalComments']) }}</div>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 12px; padding-top: 8px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <span>Đã duyệt: <strong style="color: #334155;">{{ number_format($stats['approvedComments']) }}</strong></span>
                <span>Chờ duyệt: <strong style="color: #334155;">{{ number_format($stats['pendingComments']) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Media Library Card -->
    <div class="cms-stat-card">
        <x-heroicon-o-photo style="position: absolute; right: -12px; bottom: -12px; width: 96px; height: 96px; color: #f1f5f9; z-index: 0; pointer-events: none;" />
        <div style="position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; width: 100%;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 6px; background-color: #e0e7ff; color: #4338ca;">Media</span>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">Đã đồng bộ</span>
                </div>
                <div style="font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1.1; margin-top: 12px; margin-bottom: 4px;">{{ number_format($stats['totalMediaFiles']) }} <span style="font-size: 14px; font-weight: 700; color: #64748b;">tệp</span></div>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 12px; padding-top: 8px; border-top: 1px solid #f1f5f9;">
                Dung lượng: <strong style="color: #334155;">{{ $stats['totalMediaSizeMB'] }} MB</strong>
            </div>
        </div>
    </div>

    <!-- SEO Score Card -->
    <div class="cms-stat-card">
        <x-heroicon-o-magnifying-glass style="position: absolute; right: -12px; bottom: -12px; width: 96px; height: 96px; color: #f1f5f9; z-index: 0; pointer-events: none;" />
        <div style="position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; width: 100%;">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 8px; border-radius: 6px; background-color: #fef3c7; color: #b45309;">SEO Score</span>
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">Trung bình</span>
                </div>
                <div style="font-size: 32px; font-weight: 900; color: #0f172a; line-height: 1.1; margin-top: 12px; margin-bottom: 4px;">
                    {{ $stats['avgSeo'] ? number_format($stats['avgSeo'], 1) : '—' }} <span style="font-size: 14px; font-weight: 700; color: #64748b;">/ 100</span>
                </div>
            </div>
            <div style="font-size: 12px; color: #64748b; margin-top: 12px; padding-top: 8px; border-top: 1px solid #f1f5f9; display: flex; align-items: center;">
                <span style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; background-color: {{ $stats['avgSeo'] >= 80 ? '#10b981' : ($stats['avgSeo'] >= 50 ? '#f59e0b' : '#ef4444') }}; margin-right: 6px;"></span>
                <span>Trạng thái: <strong style="color: #334155;">{{ $stats['avgSeo'] >= 80 ? 'Tốt' : ($stats['avgSeo'] >= 50 ? 'Khá' : 'Cần tối ưu') }}</strong></span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Trend Chart with Alpine.js --}}
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-500" />
                <div>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Biểu đồ hoạt động hệ thống</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Số lượng bài viết mới đăng và bình luận của bạn đọc</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold text-gray-500">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-1.5 rounded bg-blue-600 inline-block"></span>
                    <span>Bài viết</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-1.5 rounded bg-emerald-500 inline-block"></span>
                    <span>Bình luận</span>
                </div>
            </div>
        </div>
        <div 
            id="cmsTrendChartContainer"
            x-data="{}"
            x-init="
                window.renderCmsChart = function() {
                    if (typeof Chart === 'undefined') {
                        setTimeout(window.renderCmsChart, 100);
                        return;
                    }

                    const canvas = document.getElementById('cmsTrendChart');
                    const container = document.getElementById('cmsTrendChartContainer');
                    if (!canvas || !container) return;
                    
                    const labels = JSON.parse(container.getAttribute('data-labels') || '[]');
                    const articles = JSON.parse(container.getAttribute('data-articles') || '[]');
                    const comments = JSON.parse(container.getAttribute('data-comments') || '[]');

                    const oldChart = Chart.getChart(canvas);
                    if (oldChart) {
                        oldChart.destroy();
                    }
                    
                    const ctx = canvas.getContext('2d');
                    const blueGradient = ctx.createLinearGradient(0, 0, 0, 240);
                    blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
                    blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                    const greenGradient = ctx.createLinearGradient(0, 0, 0, 240);
                    greenGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                    greenGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Bài viết mới',
                                    data: articles,
                                    borderColor: '#3b82f6',
                                    backgroundColor: blueGradient,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    fill: true,
                                    tension: 0.35,
                                },
                                {
                                    label: 'Bình luận mới',
                                    data: comments,
                                    borderColor: '#10b981',
                                    backgroundColor: greenGradient,
                                    borderWidth: 3,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    fill: true,
                                    tension: 0.35,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { display: false } 
                            },
                            scales: {
                                x: {
                                    grid: { display: false }
                                },
                                y: { 
                                    beginAtZero: true, 
                                    ticks: { stepSize: 1 } 
                                }
                            }
                        }
                    });
                };
                $nextTick(() => { window.renderCmsChart(); });
            "
            x-effect="
                const trigger = '{{ microtime() }}';
                if (window.renderCmsChart) {
                    window.renderCmsChart();
                }
            "
            data-labels="{{ json_encode($trend['labels']) }}"
            data-articles="{{ json_encode($trend['articles']) }}"
            data-comments="{{ json_encode($trend['comments']) }}"
            style="position:relative; height:240px;"
        >
            <canvas id="cmsTrendChart" wire:ignore></canvas>
        </div>
    </div>

    {{-- Category Density --}}
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-2">
            <x-heroicon-o-tag class="w-5 h-5 text-gray-500" />
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Bài viết theo chuyên khoa</h3>
        </div>
        <p class="text-xs text-gray-400 mb-6">Phân bố mật độ nội dung theo từng danh mục chuyên khoa</p>
        
        @if($categoryStats->isNotEmpty())
            @php
                $maxArticles = max($categoryStats->pluck('articles_count')->toArray() ?: [1]);
            @endphp
            <div class="space-y-4 max-height-[240px] overflow-y-auto pr-1">
                @foreach($categoryStats as $row)
                    @php
                        $percentage = $maxArticles > 0 ? round(($row->articles_count / $maxArticles) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-bold text-gray-700">{{ $row->name }}</span>
                            <span class="font-extrabold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $row->articles_count }} bài</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-12 text-sm">Chưa có bài viết nào</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    {{-- Low SEO Articles --}}
    <div class="bg-white rounded-xl border border-gray-200/80 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-500" />
                <div>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Bài viết cần tối ưu SEO</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Danh sách các bài viết có điểm SEO thấp hoặc chưa cấu hình</p>
                </div>
            </div>
            <a href="{{ route('filament.admin.resources.articles.index') }}?tableFilters[seo_filter][value]=not_configured"
               class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-bold transition-all">Xem tất cả →</a>
        </div>
        @if($lowSeoArticles->isNotEmpty())
            <div class="divide-y divide-gray-100 text-sm max-h-[350px] overflow-y-auto pr-1">
                @foreach($lowSeoArticles as $article)
                    <div class="py-3 hover:bg-gray-50/50 rounded-lg px-2 transition-all duration-200 flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('filament.admin.resources.articles.edit', $article->id) }}" class="font-bold text-gray-800 hover:text-blue-600 transition-colors truncate block">
                                {{ $article->title }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $article->category?->name ?? 'Chưa phân loại' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            @if($article->seo_score !== null && $article->seo_score > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black
                                    {{ $article->seo_score >= 50 ? 'bg-amber-50 text-amber-700 border border-amber-200/50' : 'bg-red-50 text-red-700 border border-red-200/50' }}">
                                    {{ $article->seo_score }}/100
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 border border-gray-200/50">Chưa cấu hình SEO</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-16">
                <span class="text-4xl block mb-3">🎉</span>
                <p class="text-sm font-bold text-gray-700">Tuyệt vời! Điểm SEO của mọi bài viết đều tốt</p>
                <p class="text-xs text-gray-400 mt-1">Tất cả các bài viết hiện tại đã được tối ưu hóa tối đa.</p>
            </div>
        @endif
    </div>
</div>

</x-filament-panels::page>
