<div class="space-y-6 text-sm text-gray-800 dark:text-gray-200">
    <!-- 1. Tổng quan -->
    <div class="bg-gray-50 dark:bg-gray-900 border dark:border-gray-800 p-4 rounded-xl">
        <h4 class="font-bold text-gray-900 dark:text-white mb-3 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">1. Tổng quan kết quả</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Nhóm kiểm tra</p>
                <p class="font-medium text-gray-800 dark:text-gray-200">{{ $record->check_group }}</p>
            </div>
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Mức độ nguy hiểm</p>
                <span @class([
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-0.5',
                    'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'     => $record->severity === 'info',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'     => $record->severity === 'low',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' => $record->severity === 'medium',
                    'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' => $record->severity === 'high',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'       => $record->severity === 'critical',
                ])>
                    @if($record->severity === 'info') Thông tin @elseif($record->severity === 'low') Thấp @elseif($record->severity === 'medium') Trung bình @elseif($record->severity === 'high') Cao @elseif($record->severity === 'critical') Nguy hiểm @else {{ strtoupper($record->severity) }} @endif
                </span>
            </div>
            <div class="md:col-span-2">
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs mb-1">Mục tiêu / Đường dẫn</p>
                <div class="flex items-center gap-2">
                    <code class="font-mono text-xs text-gray-800 dark:text-gray-200 bg-gray-150 dark:bg-gray-950 px-2 py-1 rounded border dark:border-gray-800 select-all block w-full truncate">{{ $record->target ?: 'Toàn hệ thống' }}</code>
                </div>
            </div>
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Trạng thái xử lý</p>
                <span @class([
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-0.5',
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'   => $record->type === 'ok',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'       => $record->type === 'suspicious',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' => $record->type === 'modified',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'     => $record->type === 'new',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'       => $record->type === 'deleted',
                    'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'     => $record->type === 'reviewed',
                    'bg-gray-250 text-gray-700 dark:bg-gray-800 dark:text-gray-300'     => $record->type === 'ignored',
                ])>
                    @if($record->type === 'ok') An toàn @elseif($record->type === 'suspicious') Đáng ngờ @elseif($record->type === 'modified') Đã sửa đổi @elseif($record->type === 'new') Tệp mới @elseif($record->type === 'deleted') Đã bị xóa @elseif($record->type === 'reviewed') Đã duyệt @elseif($record->type === 'ignored') Đã bỏ qua @else {{ $record->type }} @endif
                </span>
            </div>
            <div>
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Thời gian quét</p>
                <p class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">{{ $record->created_at?->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Nội dung phát hiện</p>
                <p class="font-medium text-gray-800 dark:text-gray-200 mt-0.5">{{ $record->message }}</p>
            </div>
            @if($record->recommendation)
                <div class="md:col-span-2">
                    <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Khuyến nghị gốc</p>
                    <p class="text-gray-700 dark:text-gray-300 mt-0.5 text-xs leading-relaxed bg-white dark:bg-gray-950 p-2.5 rounded border dark:border-gray-800">{{ $record->recommendation }}</p>
                </div>
            @endif
            @if($record->hash)
                <div class="md:col-span-2">
                    <p class="font-semibold text-gray-500 dark:text-gray-400 text-xs">Mã băm MD5 tệp</p>
                    <code class="font-mono text-[10px] text-gray-600 dark:text-gray-400 select-all">{{ $record->hash }}</code>
                </div>
            @endif
        </div>
    </div>

    <!-- 2. Vì sao bị cảnh báo? -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-1.5 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">2. Vì sao bị cảnh báo?</h4>
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $guidance['why_flagged'] }}</p>
    </div>

    <!-- 3. Bằng chứng phát hiện -->
    @if(!empty($guidance['evidence']))
        <div>
            <h4 class="font-bold text-gray-900 dark:text-white mb-1.5 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">3. Bằng chứng phát hiện</h4>
            <div class="bg-gray-100 dark:bg-gray-950 border dark:border-gray-900 rounded-xl p-4 space-y-2.5 font-mono text-xs">
                @if(isset($guidance['evidence']['matched_pattern']))
                    <p><span class="text-gray-500 dark:text-gray-400">Từ khóa/Mẫu phát hiện:</span> <span class="text-red-600 dark:text-red-400 font-bold bg-red-50 dark:bg-red-950/20 px-1 py-0.5 rounded">{{ $guidance['evidence']['matched_pattern'] }}</span></p>
                @endif
                @if(isset($guidance['evidence']['line']))
                    <p><span class="text-gray-500 dark:text-gray-400">Dòng trong tệp:</span> {{ $guidance['evidence']['line'] }}</p>
                @endif
                @if(isset($guidance['evidence']['article_id']))
                    <p><span class="text-gray-500 dark:text-gray-400">Bài viết ID:</span> {{ $guidance['evidence']['article_id'] }}</p>
                @endif
                @if(isset($guidance['evidence']['title']))
                    <p><span class="text-gray-500 dark:text-gray-400">Tiêu đề bài viết:</span> <span class="text-gray-800 dark:text-gray-200">{{ $guidance['evidence']['title'] }}</span></p>
                @endif
                @if(isset($guidance['evidence']['field']))
                    <p><span class="text-gray-500 dark:text-gray-400">Trường dữ liệu:</span> {{ $guidance['evidence']['field'] }}</p>
                @endif
                @if(isset($guidance['evidence']['admin_edit_url']))
                    <p>
                        <span class="text-gray-500 dark:text-gray-400">Liên kết chỉnh sửa nhanh:</span> 
                        <a href="{{ $guidance['evidence']['admin_edit_url'] }}" target="_blank" class="text-primary-600 dark:text-primary-400 underline hover:text-primary-800 font-medium">
                            Mở trang chỉnh sửa CMS
                        </a>
                    </p>
                @endif

                @if(!empty($guidance['evidence']['snippet']))
                    <div class="mt-3 pt-2.5 border-t border-gray-200 dark:border-gray-800">
                        <p class="text-gray-500 dark:text-gray-400 mb-1.5">Đoạn nội dung nghi vấn:</p>
                        <pre class="bg-white dark:bg-gray-900 border dark:border-gray-800 p-2.5 rounded-lg overflow-x-auto text-[11px] whitespace-pre-wrap text-gray-800 dark:text-gray-200 leading-normal">{{ $guidance['evidence']['snippet'] }}</pre>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- 4. Có thể là cảnh báo giả? -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-1.5 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">4. Đánh giá cảnh báo giả (False Positive)</h4>
        @if($guidance['confidence'] === 'low')
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 text-amber-800 dark:text-amber-300">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Đánh giá: Rất có thể đây là cảnh báo giả
                </div>
                <p class="text-xs leading-relaxed">{{ $guidance['false_positive_hint'] }}</p>
            </div>
        @else
            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900 text-blue-800 dark:text-blue-300">
                <div class="flex items-center gap-2 font-bold mb-1 text-xs">
                    Độ tin cậy của cảnh báo: {{ $guidance['confidence_label'] }}
                </div>
                <p class="text-xs leading-relaxed">{{ $guidance['false_positive_hint'] }}</p>
            </div>
        @endif
    </div>

    <!-- 5. Mức độ ảnh hưởng -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-1.5 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">5. Mức độ ảnh hưởng</h4>
        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $guidance['impact'] }}</p>
    </div>

    <!-- 6. Cần kiểm tra gì? -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">6. Các bước kiểm tra thủ công cần thực hiện</h4>
        <ul class="space-y-2">
            @foreach($guidance['manual_checks'] as $check)
                <li class="flex items-start gap-2.5">
                    <span class="inline-flex h-4 w-4 shrink-0 items-center justify-center rounded bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 font-bold text-[10px]">
                        ✓
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 text-xs leading-normal">{{ $check }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- 7. Cách khắc phục -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">7. Hướng dẫn khắc phục an toàn</h4>
        <ul class="space-y-2">
            @foreach($guidance['remediation_steps'] as $idx => $step)
                <li class="flex items-start gap-2.5">
                    <span class="inline-flex h-4.5 w-4.5 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[10px] font-bold">
                        {{ $idx + 1 }}
                    </span>
                    <span class="text-gray-700 dark:text-gray-300 text-xs leading-normal">{{ $step }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- 8. Hành động tiếp theo -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-xs uppercase tracking-wider text-primary-600 dark:text-primary-400">8. Hành động tiếp theo trong CMS</h4>
        <ul class="space-y-2">
            @foreach($guidance['next_actions'] as $action)
                <li class="flex items-start gap-2.5">
                    <span class="text-primary-500 dark:text-primary-400 font-bold text-xs leading-none mt-0.5">•</span>
                    <span class="text-gray-700 dark:text-gray-300 text-xs leading-normal">{{ $action }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Thông tin bỏ qua (chỉ hiển thị nếu đã bị bỏ qua) -->
    @if($record->type === 'ignored')
        <div class="border-t dark:border-gray-800 pt-4 mt-2">
            <div class="bg-gray-100 dark:bg-gray-800/50 p-4 rounded-xl border dark:border-gray-700">
                <h5 class="font-bold text-gray-900 dark:text-white text-xs mb-2">Thông tin bỏ qua cảnh báo</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-gray-500">Người thực hiện:</span>
                        <span class="font-medium">{{ $record->meta['ignored_by'] ?? 'Không rõ' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Thời điểm:</span>
                        <span class="font-medium">{{ $record->ignored_at?->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-gray-500">Lý do bỏ qua:</span>
                        <p class="bg-white dark:bg-gray-900 p-2.5 rounded-lg border dark:border-gray-800 mt-1 italic text-gray-700 dark:text-gray-300">{{ $record->ignored_reason ?: 'Không có.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
