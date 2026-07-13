<x-filament-panels::page>
    <div class="fi-security-scan-page space-y-6">
        @if($summary['has_scan'])
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- System Status Card -->
                @if($summary['status'] === 'healthy')
                    <div class="fi-scan-card status-healthy">
                        <div class="flex justify-between items-start w-full">
                            <div>
                                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                                <h3 class="text-xl font-bold mt-1 text-white">Hệ thống an toàn</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono opacity-80">SECURE</span>
                                <span class="pulse-dot dot-healthy"></span>
                            </div>
                        </div>
                        <div class="text-xs opacity-90 mt-4 leading-relaxed">
                            Không phát hiện nguy hiểm, tệp tin độc hại hoặc thay đổi trái phép trong mã nguồn.
                        </div>
                    </div>
                @elseif($summary['status'] === 'warning')
                    <div class="fi-scan-card status-warning">
                        <div class="flex justify-between items-start w-full">
                            <div>
                                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                                <h3 class="text-xl font-bold mt-1 text-white">Cần chú ý</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono opacity-80">WARNING</span>
                                <span class="pulse-dot dot-warning"></span>
                            </div>
                        </div>
                        <div class="text-xs opacity-90 mt-4 leading-relaxed">
                            Hệ thống ghi nhận một số tệp tin mới hoặc thay đổi ở mức độ nguy hại thấp/trung bình.
                        </div>
                    </div>
                @else
                    <div class="fi-scan-card status-danger">
                        <div class="flex justify-between items-start w-full">
                            <div>
                                <span class="text-xs font-semibold tracking-wider uppercase opacity-80">Trạng thái hệ thống</span>
                                <h3 class="text-xl font-bold mt-1 text-white">Nguy cơ bảo mật</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono opacity-90">BREACHED</span>
                                <span class="pulse-dot dot-danger"></span>
                            </div>
                        </div>
                        <div class="text-xs opacity-90 mt-4 flex items-center gap-1.5 leading-relaxed">
                            <svg class="h-4 w-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Phát hiện lỗ hổng nguy hại cao hoặc thay đổi tệp cấu trúc hệ thống!
                        </div>
                    </div>
                @endif

                <!-- Incident Counter Card -->
                <div class="fi-scan-card info-card">
                    <div class="w-full">
                        <span class="text-xs font-semibold tracking-wider uppercase text-gray-400 dark:text-slate-400">Sự cố & Đe dọa</span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $summary['total_threats'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">cảnh báo cần xử lý</span>
                        </div>
                    </div>
                    <div class="flex gap-4 text-[11px] text-gray-500 dark:text-slate-400 mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 w-full">
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            An toàn: {{ $summary['total_ok'] }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                            Đã bỏ qua: {{ $summary['total_ignored'] }}
                        </span>
                    </div>
                </div>

                <!-- Last Scan Information Card -->
                <div class="fi-scan-card info-card">
                    <div class="w-full">
                        <span class="text-xs font-semibold tracking-wider uppercase text-gray-400 dark:text-slate-400">Lần quét gần nhất</span>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-2 leading-tight">{{ $summary['scanned_at'] }}</h4>
                    </div>
                    <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-4 truncate w-full pt-3 border-t border-gray-100 dark:border-gray-800">
                        Mã phiên: <code class="bg-gray-100 dark:bg-gray-950 px-1.5 py-0.5 rounded text-[10px] font-mono select-all text-gray-700 dark:text-gray-300">{{ $summary['scan_id'] }}</code>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Scan State -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800/30 dark:to-gray-900/30 border border-blue-100 dark:border-gray-800/50 rounded-2xl p-6 mb-6 flex items-center gap-4">
                <div class="p-3 bg-blue-500 rounded-full text-white shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-blue-900 dark:text-blue-200">Bắt đầu Quét Bảo Mật Hệ Thống</h3>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">Chưa có kết quả lịch sử. Vui lòng nhấn nút "Chạy quét nhanh" hoặc "Chạy quét đầy đủ" ở trên để thực hiện kiểm tra an ninh.</p>
                </div>
            </div>
        @endif

        <!-- Scan Results Table Section -->
        <div class="space-y-3">
            <h3 class="text-base font-bold text-gray-900 dark:text-white px-1 uppercase tracking-wider">Kết quả quét chi tiết</h3>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden p-1">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
