<div class="space-y-4 text-sm">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="font-semibold text-gray-500">Nhóm kiểm tra</p>
            <p class="font-mono text-gray-800 dark:text-gray-200">{{ $record->check_group }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-500">Mức độ</p>
            <span @class([
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                'bg-gray-100 text-gray-800'     => $record->severity === 'info',
                'bg-blue-100 text-blue-800'     => $record->severity === 'low',
                'bg-yellow-100 text-yellow-800' => $record->severity === 'medium',
                'bg-orange-100 text-orange-800' => $record->severity === 'high',
                'bg-red-100 text-red-800'       => $record->severity === 'critical',
            ])>
                {{ strtoupper($record->severity) }}
            </span>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-500">Mục tiêu / Đường dẫn</p>
            <p class="font-mono break-all text-xs text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900 p-2 rounded border border-gray-100 dark:border-gray-800">{{ $record->target ?: 'Toàn hệ thống' }}</p>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-500">Nội dung phát hiện</p>
            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $record->message }}</p>
        </div>
        
        @if($record->recommendation)
            <div class="col-span-2 bg-blue-50 dark:bg-gray-800 border-l-4 border-blue-400 p-3 rounded-r">
                <p class="font-semibold text-blue-800 dark:text-blue-200 mb-1">Khuyến nghị khắc phục</p>
                <p class="text-blue-900 dark:text-blue-300 text-xs">{{ $record->recommendation }}</p>
            </div>
        @endif

        @if($record->hash)
            <div>
                <p class="font-semibold text-gray-500">Mã băm MD5</p>
                <p class="font-mono text-xs text-gray-600 dark:text-gray-400 break-all bg-gray-50 dark:bg-gray-900 px-1 py-0.5 rounded">{{ $record->hash }}</p>
            </div>
        @endif

        <div>
            <p class="font-semibold text-gray-500">Thời gian quét</p>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->created_at?->format('d/m/Y H:i:s') }}</p>
        </div>

        @if($record->type === 'ignored')
            <div class="col-span-2 bg-yellow-50 dark:bg-gray-800 border-l-4 border-yellow-400 p-3 rounded-r">
                <p class="font-semibold text-yellow-800 dark:text-yellow-200 mb-1">Cảnh báo bị bỏ qua</p>
                <p class="text-yellow-900 dark:text-yellow-300 text-xs"><strong>Lý do:</strong> {{ $record->ignored_reason ?: 'Không có lý do.' }}</p>
                <p class="text-yellow-600 dark:text-yellow-400 text-[10px] mt-1">Bỏ qua lúc: {{ $record->ignored_at?->format('d/m/Y H:i:s') }}</p>
            </div>
        @endif
    </div>

    @if(!empty($record->meta))
        <div class="border-t dark:border-gray-700 pt-3">
            <p class="font-semibold text-gray-500 mb-2">Thông tin bổ sung (Meta Context)</p>
            <pre class="bg-gray-50 dark:bg-gray-900 border dark:border-gray-800 rounded p-3 text-xs font-mono overflow-auto max-h-64 whitespace-pre-wrap text-gray-800 dark:text-gray-200">{{ json_encode($record->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>
