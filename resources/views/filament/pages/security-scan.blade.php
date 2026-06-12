<x-filament-panels::page>
    <style>
        .scan-card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .scan-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.15);
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.33); }
            80%, 100% { opacity: 0; }
        }
        @keyframes pulse-dot {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.2); }
            100% { transform: scale(0.8); }
        }
        .pulse-active {
            position: relative;
        }
        .pulse-active::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: inherit;
            opacity: 0.75;
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
        }
        .pulse-active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: inherit;
            animation: pulse-dot 1.5s cubic-bezier(0.455, 0.030, 0.515, 0.955) infinite;
        }
    </style>

    @if($summary['has_scan'])
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Trạng thái tổng quát -->
            @if($summary['status'] === 'healthy')
                <div class="scan-card-hover bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between min-h-[140px]">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                            <h3 class="text-2xl font-bold mt-1">Hệ thống an toàn</h3>
                        </div>
                        <div class="h-4 w-4 bg-emerald-300 rounded-full pulse-active"></div>
                    </div>
                    <div class="text-xs opacity-90 mt-4">
                        Không phát hiện nguy hiểm nguy kịch hoặc mã độc trong tệp nguồn.
                    </div>
                </div>
            @elseif($summary['status'] === 'warning')
                <div class="scan-card-hover bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between min-h-[140px]">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                            <h3 class="text-2xl font-bold mt-1">Cần chú ý</h3>
                        </div>
                        <div class="h-4 w-4 bg-amber-300 rounded-full pulse-active"></div>
                    </div>
                    <div class="text-xs opacity-90 mt-4">
                        Hệ thống ghi nhận một số khuyến nghị bảo mật mức thấp/trung bình.
                    </div>
                </div>
            @else
                <div class="scan-card-hover bg-gradient-to-br from-rose-500 to-red-700 text-white rounded-xl p-6 shadow-md relative overflow-hidden flex flex-col justify-between min-h-[140px]">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                            <h3 class="text-2xl font-bold mt-1">Nguy cơ bảo mật</h3>
                        </div>
                        <div class="h-4 w-4 bg-rose-300 rounded-full pulse-active"></div>
                    </div>
                    <div class="text-xs opacity-90 mt-4 flex items-center gap-1">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Phát hiện lỗ hổng nguy cơ cao hoặc thay đổi tệp tin quan trọng!
                    </div>
                </div>
            @endif

            <!-- Thống kê chi tiết -->
            <div class="scan-card-hover bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between min-h-[140px]">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-gray-400">Sự cố & Đe dọa</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $summary['total_threats'] }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">đang chờ xử lý</span>
                    </div>
                </div>
                <div class="flex gap-4 text-xs text-gray-500 dark:text-gray-400 mt-4 pt-2 border-t border-gray-50 dark:border-gray-700">
                    <span class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        An toàn: {{ $summary['total_ok'] }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                        Đã bỏ qua: {{ $summary['total_ignored'] }}
                    </span>
                </div>
            </div>

            <!-- Thông tin lần cuối -->
            <div class="scan-card-hover bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between min-h-[140px]">
                <div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-gray-400">Lần quét gần nhất</span>
                    <h4 class="text-base font-semibold text-gray-900 dark:text-white mt-2">{{ $summary['scanned_at'] }}</h4>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-4 truncate">
                    Mã quét: <code class="bg-gray-50 dark:bg-gray-900 px-1 py-0.5 rounded text-[10px] font-mono select-all">{{ $summary['scan_id'] }}</code>
                </div>
            </div>
        </div>
    @else
        <!-- Chưa có kết quả quét -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 border border-blue-100 dark:border-gray-800 rounded-xl p-6 mb-6 flex items-center gap-4">
            <div class="p-3 bg-blue-500 rounded-full text-white">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200">Chào mừng bạn đến với Quét Bảo Mật</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Hệ thống chưa ghi nhận bất kỳ dữ liệu quét nào. Vui lòng bấm vào nút "Chạy quét nhanh" hoặc "Chạy quét đầy đủ" để tạo phiên quét đầu tiên.</p>
            </div>
        </div>
    @endif

    <!-- Bảng kết quả Filament Table -->
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white px-1">Kết quả quét bảo mật</h3>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
