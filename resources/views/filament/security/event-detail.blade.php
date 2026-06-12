<div class="space-y-4 text-sm">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="font-semibold text-gray-500">Loại</p>
            <p class="font-mono">{{ $record->type }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-500">Mức độ</p>
            <span @class([
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                'bg-gray-100 text-gray-800'   => $record->severity === 'info',
                'bg-green-100 text-green-800' => $record->severity === 'low',
                'bg-yellow-100 text-yellow-800' => $record->severity === 'medium',
                'bg-orange-100 text-orange-800' => $record->severity === 'high',
                'bg-red-100 text-red-800'     => $record->severity === 'critical',
            ])>
                {{ strtoupper($record->severity) }}
            </span>
        </div>
        <div>
            <p class="font-semibold text-gray-500">IP</p>
            <p class="font-mono">{{ $record->ip_address ?? '—' }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-500">Phương thức</p>
            <p class="font-mono">{{ $record->method ?? '—' }}</p>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-500">URL</p>
            <p class="font-mono break-all text-xs">{{ $record->url ?? '—' }}</p>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-500">Mô tả</p>
            <p>{{ $record->message }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-500">User Agent</p>
            <p class="text-xs text-gray-600 break-all">{{ $record->user_agent ?? '—' }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-500">Thời gian</p>
            <p>{{ $record->created_at?->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    @if(!empty($record->context))
        <div class="border-t pt-3">
            <p class="font-semibold text-gray-500 mb-2">Context (đã ẩn thông tin nhạy cảm)</p>
            <pre class="bg-gray-50 border rounded p-3 text-xs font-mono overflow-auto max-h-64 whitespace-pre-wrap">{{ json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>
