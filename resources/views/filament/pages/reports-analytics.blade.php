<x-filament-panels::page>

{{-- Range Filter --}}
<div class="flex flex-wrap items-center gap-2 mb-6">
    <span class="text-sm font-semibold text-gray-500">Khoảng thời gian:</span>
    @foreach(['today' => 'Hôm nay', '7' => '7 ngày', '30' => '30 ngày', 'month' => 'Tháng này'] as $key => $label)
        <button
            wire:click="setRange('{{ $key }}')"
            class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition-all
                {{ $range === $key
                    ? 'bg-blue-700 text-white border-blue-700 shadow'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-700' }}">
            {{ $label }}
        </button>
    @endforeach
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @php
        $cards = [
            ['label' => 'Tổng tư vấn',           'value' => number_format($stats['totalConsultations']),    'icon' => '💬', 'color' => 'blue'],
            ['label' => 'Tư vấn chờ xử lý',      'value' => number_format($stats['pendingConsultations']),  'icon' => '⏳', 'color' => $stats['pendingConsultations'] > 0 ? 'red' : 'green'],
            ['label' => 'Tư vấn đã xử lý',        'value' => number_format($stats['processedConsultations']),'icon' => '✅', 'color' => 'green'],
            ['label' => 'Bệnh nhân mới (kỳ này)', 'value' => number_format($stats['newPatients']),          'icon' => '👤', 'color' => 'indigo'],
            ['label' => 'Tổng bệnh nhân',         'value' => number_format($stats['totalPatients']),        'icon' => '👥', 'color' => 'purple'],
            ['label' => 'Tỷ lệ chuyển đổi',       'value' => $stats['convRate'] . '%',                      'icon' => '📈', 'color' => 'teal'],
            ['label' => 'Bài viết đã xuất bản',   'value' => number_format($stats['publishedArticles']),   'icon' => '📝', 'color' => 'blue'],
            ['label' => 'Điểm SEO trung bình',    'value' => $stats['avgSeo'] ? number_format($stats['avgSeo'], 1) . '/100' : '—', 'icon' => '🔍', 'color' => $stats['avgSeo'] >= 80 ? 'green' : ($stats['avgSeo'] >= 50 ? 'yellow' : 'red')],
        ];
        $colorMap = [
            'blue'   => 'bg-blue-50 border-blue-100 text-blue-700',
            'green'  => 'bg-emerald-50 border-emerald-100 text-emerald-700',
            'red'    => 'bg-red-50 border-red-100 text-red-700',
            'indigo' => 'bg-indigo-50 border-indigo-100 text-indigo-700',
            'purple' => 'bg-purple-50 border-purple-100 text-purple-700',
            'teal'   => 'bg-teal-50 border-teal-100 text-teal-700',
            'yellow' => 'bg-yellow-50 border-yellow-100 text-yellow-700',
        ];
    @endphp
    @foreach($cards as $card)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-col gap-1">
            <div class="text-2xl">{{ $card['icon'] }}</div>
            <div class="text-2xl font-black text-gray-900 leading-tight">{{ $card['value'] }}</div>
            <div class="text-xs text-gray-500 font-semibold">{{ $card['label'] }}</div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Consultation Trend Chart --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">📊 Lượt tư vấn theo ngày</h3>
        @if(array_sum($consultationTrend['data']) > 0)
            <div style="position:relative; height:220px;">
                <canvas id="consultationTrendChart"></canvas>
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                <span class="text-4xl mb-2">📭</span>
                <p class="text-sm font-semibold">Chưa có tư vấn trong kỳ này</p>
            </div>
        @endif
    </div>

    {{-- Specialty Breakdown Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">🏥 Tư vấn theo chuyên khoa</h3>
        @if($specialtyStats->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-xs text-gray-400 uppercase">
                            <th class="text-left py-2 font-semibold">Chuyên khoa</th>
                            <th class="text-center py-2 font-semibold">Tư vấn</th>
                            <th class="text-center py-2 font-semibold">Bài viết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($specialtyStats as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 font-semibold text-gray-800">{{ $row['department'] }}</td>
                                <td class="py-2 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700">{{ $row['consultations'] }}</span>
                                </td>
                                <td class="py-2 text-center text-gray-500">{{ $row['articles'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-gray-400 py-8 text-sm">Chưa có dữ liệu</div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Pending Consultations Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">⏳ Tư vấn cần xử lý</h3>
            <a href="{{ route('filament.admin.resources.consultations.index') }}"
               class="text-xs text-blue-600 hover:underline font-semibold">Xem tất cả →</a>
        </div>
        @if($pendingConsultations->isNotEmpty())
            <div class="divide-y divide-gray-50 text-sm">
                @foreach($pendingConsultations as $c)
                    <div class="py-2.5 flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $c->name }}</p>
                            <p class="text-xs text-gray-400">{{ $c->phone }} · {{ $c->department ?? 'Chưa chọn chuyên khoa' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Chờ xử lý</span>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-8">
                <span class="text-3xl block mb-2">✅</span>
                <p class="text-sm font-semibold">Không có tư vấn chờ xử lý!</p>
            </div>
        @endif
    </div>

    {{-- Low SEO Articles --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">🔴 Bài viết SEO thấp</h3>
            <a href="{{ route('filament.admin.resources.articles.index') }}?tableFilters[seo_low][isActive]=true"
               class="text-xs text-blue-600 hover:underline font-semibold">Xem tất cả →</a>
        </div>
        @if($lowSeoArticles->isNotEmpty())
            <div class="divide-y divide-gray-50 text-sm">
                @foreach($lowSeoArticles as $article)
                    <div class="py-2.5 flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $article->title }}</p>
                            <p class="text-xs text-gray-400">{{ $article->category?->name ?? 'Chưa phân loại' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            @if($article->seo_score !== null)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                    {{ $article->seo_score >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $article->seo_score }}/100
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Chưa phân tích</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-8">
                <span class="text-3xl block mb-2">🎉</span>
                <p class="text-sm font-semibold">Tất cả bài viết SEO đều tốt!</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('consultationTrendChart');
    if (!canvas) return;

    const labels = @json($consultationTrend['labels']);
    const data   = @json($consultationTrend['data']);

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Lượt tư vấn',
                data,
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30,64,175,0.08)',
                borderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});

// Re-init chart after Livewire updates
document.addEventListener('livewire:updated', function () {
    const canvas = document.getElementById('consultationTrendChart');
    if (!canvas) return;
    // Destroy old chart instance if exists
    const old = Chart.getChart(canvas);
    if (old) old.destroy();

    const labels = @json($consultationTrend['labels']);
    const data   = @json($consultationTrend['data']);

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Lượt tư vấn',
                data,
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30,64,175,0.08)',
                borderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush

</x-filament-panels::page>
