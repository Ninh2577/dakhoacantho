<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- Suggestions Section -->
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                💡 Gợi ý cấu hình URL mẫu
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button type="button" 
                        wire:click="selectSuggestion('{categories}/{slug}.html', 'category/{categories}')"
                        class="p-4 rounded-xl border border-slate-200 hover:border-clinic-blue hover:bg-slate-50 transition-all text-left space-y-2">
                    <p class="text-xs font-extrabold text-clinic-blue">Cấu trúc chuyên sâu SEO (.html)</p>
                    <p class="text-xs text-slate-500">Bài viết: <code class="bg-slate-100 px-1 py-0.5 rounded">{categories}/{slug}.html</code></p>
                    <p class="text-xs text-slate-500">Danh mục: <code class="bg-slate-100 px-1 py-0.5 rounded">category/{categories}</code></p>
                </button>
                
                <button type="button" 
                        wire:click="selectSuggestion('{slug}', '{slug}')"
                        class="p-4 rounded-xl border border-slate-200 hover:border-clinic-blue hover:bg-slate-50 transition-all text-left space-y-2">
                    <p class="text-xs font-extrabold text-clinic-blue">Cấu trúc siêu ngắn (Tối giản)</p>
                    <p class="text-xs text-slate-500">Bài viết: <code class="bg-slate-100 px-1 py-0.5 rounded">{slug}</code></p>
                    <p class="text-xs text-slate-500">Danh mục: <code class="bg-slate-100 px-1 py-0.5 rounded">{slug}</code></p>
                </button>

                <button type="button" 
                        wire:click="selectSuggestion('{slug}.html', 'category/{categories}')"
                        class="p-4 rounded-xl border border-slate-200 hover:border-clinic-blue hover:bg-slate-50 transition-all text-left space-y-2">
                    <p class="text-xs font-extrabold text-clinic-blue">Bài viết đuôi HTML phẳng</p>
                    <p class="text-xs text-slate-500">Bài viết: <code class="bg-slate-100 px-1 py-0.5 rounded">{slug}.html</code></p>
                    <p class="text-xs text-slate-500">Danh mục: <code class="bg-slate-100 px-1 py-0.5 rounded">category/{categories}</code></p>
                </button>
            </div>
        </div>

        <!-- Configuration Form -->
        <form wire:submit.prevent="previewChanges" class="space-y-6">
            {{ $this->form }}

            <div class="flex flex-wrap gap-4">
                <x-filament::button type="submit" size="lg" color="info" icon="heroicon-o-eye">
                    Xem trước thay đổi
                </x-filament::button>

                @if($hasPreviewed && $conflictCount === 0)
                    <x-filament::button type="button" size="lg" color="success" icon="heroicon-o-check-circle"
                                        wire:click="applyChanges"
                                        wire:loading.attr="disabled"
                                        :disabled="optional($this->activeHistory)->status === 'processing'">
                        Áp dụng cấu trúc URL
                    </x-filament::button>
                @else
                    <x-filament::button type="button" size="lg" color="gray" disabled icon="heroicon-o-lock-closed">
                        Áp dụng cấu trúc URL (Chỉ khả dụng khi conflict = 0)
                    </x-filament::button>
                @endif

                <x-filament::button type="button" size="lg" color="danger" icon="heroicon-o-arrow-uturn-left"
                                    wire:click="triggerRollback"
                                    wire:confirm="Bạn có chắc chắn muốn khôi phục về phiên bản URL trước đó?">
                    Khôi phục bản trước (Rollback)
                </x-filament::button>
            </div>
        </form>

        <!-- Active Processing / Polling Card -->
        @if($this->activeHistory)
            <div wire:poll.2s class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
                        @if($this->activeHistory->status === 'processing' || $this->activeHistory->status === 'pending')
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-clinic-blue opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-clinic-blue"></span>
                            </span>
                            Đang xử lý biên dịch URL...
                        @elseif($this->activeHistory->status === 'completed')
                            <span class="inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            Biên dịch URL hoàn tất thành công!
                        @else
                            <span class="inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            Biên dịch URL thất bại!
                        @endif
                    </h3>
                    <span class="text-xs px-2 py-0.5 rounded-lg font-bold
                        {{ $this->activeHistory->status === 'completed' ? 'bg-emerald-50 text-emerald-600' : '' }}
                        {{ $this->activeHistory->status === 'failed' ? 'bg-rose-50 text-rose-600' : '' }}
                        {{ in_array($this->activeHistory->status, ['pending', 'processing']) ? 'bg-blue-50 text-blue-600' : '' }}
                    ">
                        {{ strtoupper($this->activeHistory->status) }}
                    </span>
                </div>

                @if(in_array($this->activeHistory->status, ['pending', 'processing']))
                    @php
                        $percentage = $this->activeHistory->total_items > 0 
                            ? round(($this->activeHistory->processed_items / $this->activeHistory->total_items) * 100) 
                            : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-bold text-slate-500">
                            <span>Tiến độ: {{ $this->activeHistory->processed_items }} / {{ $this->activeHistory->total_items }} mục</span>
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-clinic-blue h-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center pt-2">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-500">Đã cập nhật</p>
                        <p class="text-lg font-bold text-slate-800">{{ $this->activeHistory->updated_items }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-500">Thất bại</p>
                        <p class="text-lg font-bold text-rose-600">{{ $this->activeHistory->failed_items }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-500">Số Redirects tạo</p>
                        <p class="text-lg font-bold text-slate-800">{{ $this->activeHistory->redirect_count }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-500">Thời gian bắt đầu</p>
                        <p class="text-xs font-bold text-slate-700 mt-1">
                            {{ $this->activeHistory->started_at ? $this->activeHistory->started_at->format('H:i:s d/m') : 'N/A' }}
                        </p>
                    </div>
                </div>

                @if($this->activeHistory->error_message)
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-xs text-rose-700 space-y-1">
                        <p class="font-bold">Chi tiết lỗi:</p>
                        <p>{{ $this->activeHistory->error_message }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Conflicts Warning Section -->
        @if($hasPreviewed && $conflictCount > 0)
            <div class="bg-rose-50 border border-rose-150 p-6 rounded-2xl space-y-4">
                <h3 class="text-sm font-extrabold text-rose-800 flex items-center gap-2">
                    ⚠️ Phát hiện {{ $conflictCount }} xung đột đường dẫn!
                </h3>
                <p class="text-xs text-rose-700">
                    Bạn bắt buộc phải điều chỉnh cấu hình định dạng hoặc chỉnh sửa các bài viết/danh mục bị trùng lặp slug trước khi áp dụng cấu trúc này để tránh làm hỏng các trang tĩnh của hệ thống.
                </p>
                <div class="bg-white rounded-xl border border-rose-100 overflow-hidden text-xs max-h-60 overflow-y-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-55 border-b border-slate-100 font-bold text-slate-700">
                                <th class="p-3">Loại xung đột</th>
                                <th class="p-3">Chi tiết xung đột</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($conflicts as $conflict)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-bold text-rose-600 uppercase">{{ $conflict['type'] }}</td>
                                    <td class="p-3 text-slate-700">{{ $conflict['message'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Preview Examples Table -->
        @if($hasPreviewed)
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-extrabold text-slate-800">
                        🔍 Xem trước cấu trúc URL mới (Ví dụ ngẫu nhiên)
                    </h3>
                    <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-bold">
                        Sẽ tạo: ~{{ $redirectCount }} 301 Redirects
                    </span>
                </div>
                <div class="border border-slate-100 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 font-bold text-slate-600">
                                <th class="p-3">Loại</th>
                                <th class="p-3">Tên mục</th>
                                <th class="p-3">URL hiện tại / Mặc định</th>
                                <th class="p-3">URL mới dự kiến</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700">
                            @foreach($previewExamples as $ex)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-bold">
                                        <span class="px-2 py-0.5 rounded text-[10px] 
                                            {{ $ex['type'] === 'Danh mục' ? 'bg-blue-50 text-blue-600' : 'bg-teal-50 text-teal-600' }}
                                        ">
                                            {{ $ex['type'] }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-bold">{{ $ex['name'] }}</td>
                                    <td class="p-3 text-slate-400 font-mono">{{ $ex['old'] }}</td>
                                    <td class="p-3 text-clinic-blue font-bold font-mono">{{ $ex['new'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
