<x-filament-panels::page>
    @if ($activeBatch)
        {{-- Polling active progress if batch is not in a final state --}}
        <div @if (in_array($activeBatch->status, ['pending', 'processing'])) wire:poll.3s @endif class="space-y-6">
            
            {{-- Header panel --}}
            <div class="flex items-center justify-between bg-white dark:bg-gray-950 p-6 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm transition-all duration-300">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                        <span>Lịch sử chi tiết: {{ $activeBatch->original_file_name }}</span>
                        @if ($activeBatch->dry_run)
                            <x-filament::badge color="warning" class="animate-pulse">Chạy thử nghiệm (Dry-run)</x-filament::badge>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Domain nguồn: <a href="{{ $activeBatch->old_domain }}" target="_blank" class="underline text-primary-600 dark:text-primary-400">{{ $activeBatch->old_domain }}</a>
                    </p>
                </div>
                <x-filament::button wire:click="backToList" color="gray" icon="heroicon-o-chevron-left" class="hover:scale-105 transition-transform">
                    Quay lại danh sách
                </x-filament::button>
            </div>

            {{-- Progress Status Panel --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                {{-- Status Card --}}
                <div class="md:col-span-1 bg-white dark:bg-gray-950 p-6 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Trạng thái</span>
                        <div class="mt-2 flex items-center gap-2">
                            @if ($activeBatch->status === 'pending')
                                <span class="flex h-3 w-3 rounded-full bg-gray-400"></span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Đang chờ xử lý</span>
                            @elseif ($activeBatch->status === 'processing')
                                <span class="flex h-3 w-3 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-primary-500"></span>
                                </span>
                                <span class="font-semibold text-primary-600 dark:text-primary-400">Đang import dữ liệu</span>
                            @elseif ($activeBatch->status === 'completed')
                                <span class="flex h-3 w-3 rounded-full bg-success-500"></span>
                                <span class="font-semibold text-success-600 dark:text-success-400">Đã hoàn thành</span>
                            @elseif ($activeBatch->status === 'failed')
                                <span class="flex h-3 w-3 rounded-full bg-danger-500"></span>
                                <span class="font-semibold text-danger-600 dark:text-danger-400">Thất bại</span>
                            @else
                                <span class="flex h-3 w-3 rounded-full bg-gray-500"></span>
                                <span class="font-semibold text-gray-600 dark:text-gray-400">Đã hủy</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-900 text-xs text-gray-500 space-y-1">
                        <div>Bắt đầu: {{ $activeBatch->started_at ? $activeBatch->started_at->format('H:i:s d/m/Y') : 'N/A' }}</div>
                        <div>Kết thúc: {{ $activeBatch->finished_at ? $activeBatch->finished_at->format('H:i:s d/m/Y') : 'N/A' }}</div>
                    </div>
                </div>

                {{-- Statistics Grid --}}
                <div class="md:col-span-3 bg-white dark:bg-gray-950 p-6 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-4">Thống kê tiến trình</span>
                    
                    {{-- Progress Bar --}}
                    @if ($activeBatch->status === 'processing' || $activeBatch->status === 'completed')
                        @php
                            $percentage = $activeBatch->total_items > 0 
                                ? round(($activeBatch->processed_items / $activeBatch->total_items) * 100) 
                                : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
                                <span>Tiến trình hoàn thành</span>
                                <span class="font-bold text-primary-600 dark:text-primary-400">{{ $percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-900 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $activeBatch->total_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Tổng số mục</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $activeBatch->processed_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Đã xử lý</div>
                        </div>
                        <div class="bg-success-50/50 dark:bg-success-950/20 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-success-600 dark:text-success-400">{{ $activeBatch->imported_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Thêm mới</div>
                        </div>
                        <div class="bg-primary-50/50 dark:bg-primary-950/20 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $activeBatch->updated_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Cập nhật</div>
                        </div>
                        <div class="bg-warning-50/50 dark:bg-warning-950/20 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-warning-600 dark:text-warning-400">{{ $activeBatch->skipped_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Bỏ qua</div>
                        </div>
                        <div class="bg-danger-50/50 dark:bg-danger-950/20 p-3 rounded-xl text-center">
                            <div class="text-lg font-bold text-danger-600 dark:text-danger-400">{{ $activeBatch->failed_items }}</div>
                            <div class="text-[10px] text-gray-400 uppercase">Lỗi</div>
                        </div>
                    </div>

                    @if ($activeBatch->missing_media_items > 0)
                        <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 rounded-xl text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4" />
                            <span>Chú ý: Có {{ $activeBatch->missing_media_items }} tệp tin hình ảnh đính kèm không tìm thấy cục bộ. Các tham chiếu này vẫn giữ nguyên liên kết cũ hoặc URL đích.</span>
                        </div>
                    @endif

                    @if ($activeBatch->error_message)
                        <div class="mt-4 p-3 bg-danger-50 dark:bg-danger-950/20 border border-danger-200 dark:border-danger-900/50 rounded-xl text-xs text-danger-700 dark:text-danger-300">
                            <span class="font-bold block">Lỗi tiến trình:</span>
                            <span class="block mt-1 font-mono text-[10px] whitespace-pre-wrap">{{ $activeBatch->error_message }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Audit Logs section --}}
            <div class="bg-white dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-900 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Nhật ký xử lý chi tiết</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nhật ký trực tiếp theo dõi các hành động ghi hoặc bỏ qua trên từng bản ghi danh mục và bài viết.</p>
                    </div>

                    {{-- Logs filter bar --}}
                    <div class="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                        <div class="w-full sm:w-48">
                            <input wire:model.live.debounce.300ms="logSearch" type="text" placeholder="Tìm kiếm slug, tiêu đề..." class="block w-full rounded-lg border-gray-200 dark:border-gray-900 bg-gray-50 dark:bg-gray-900/50 text-xs px-3 py-1.5 focus:border-primary-500 focus:ring-primary-500 dark:text-white" />
                        </div>
                        <select wire:model.live="logAction" class="rounded-lg border-gray-200 dark:border-gray-900 bg-gray-50 dark:bg-gray-900/50 text-xs px-2 py-1.5 dark:text-white">
                            <option value="">Tất cả hành động</option>
                            <option value="imported">Thêm mới (Imported)</option>
                            <option value="updated">Cập nhật (Updated)</option>
                            <option value="skipped">Bỏ qua (Skipped)</option>
                            <option value="failed">Thất bại (Failed)</option>
                            <option value="dry_run">Thử nghiệm (Dry-run)</option>
                            <option value="missing_media">Thiếu ảnh (Missing Media)</option>
                        </select>
                        <select wire:model.live="logStatus" class="rounded-lg border-gray-200 dark:border-gray-900 bg-gray-50 dark:bg-gray-900/50 text-xs px-2 py-1.5 dark:text-white">
                            <option value="">Tất cả trạng thái</option>
                            <option value="success">Thành công (Success)</option>
                            <option value="warning">Cảnh báo (Warning)</option>
                            <option value="error">Lỗi (Error)</option>
                        </select>
                    </div>
                </div>

                {{-- Logs table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/30 text-gray-400 uppercase tracking-wider text-[10px] font-bold border-b border-gray-50 dark:border-gray-900">
                                <th class="px-6 py-3">WP ID</th>
                                <th class="px-6 py-3">Loại</th>
                                <th class="px-6 py-3">Đối tượng cũ (WordPress)</th>
                                <th class="px-6 py-3">Hành động</th>
                                <th class="px-6 py-3">Trạng thái</th>
                                <th class="px-6 py-3">Chi tiết thông điệp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-900">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/10 transition-colors">
                                    <td class="px-6 py-3 font-mono font-bold text-gray-500">{{ $log->source_post_id ?: 'N/A' }}</td>
                                    <td class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-400 capitalize">
                                        {{ $log->source_post_type ?: 'system' }}
                                    </td>
                                    <td class="px-6 py-3 max-w-xs truncate">
                                        @if ($log->source_title)
                                            <div class="font-bold text-gray-900 dark:text-white truncate">{{ $log->source_title }}</div>
                                        @endif
                                        @if ($log->source_slug)
                                            <div class="text-[10px] font-mono text-gray-400 truncate">Slug: {{ $log->source_slug }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($log->action === 'imported')
                                            <x-filament::badge color="success">Imported</x-filament::badge>
                                        @elseif ($log->action === 'updated')
                                            <x-filament::badge color="primary">Updated</x-filament::badge>
                                        @elseif ($log->action === 'skipped')
                                            <x-filament::badge color="gray">Skipped</x-filament::badge>
                                        @elseif ($log->action === 'failed')
                                            <x-filament::badge color="danger">Failed</x-filament::badge>
                                        @elseif ($log->action === 'dry_run')
                                            <x-filament::badge color="warning">Dry-run</x-filament::badge>
                                        @elseif ($log->action === 'missing_media')
                                            <x-filament::badge color="warning">Missing Media</x-filament::badge>
                                        @else
                                            <x-filament::badge color="gray">{{ $log->action }}</x-filament::badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($log->status === 'success')
                                            <span class="inline-flex items-center gap-1.5 text-success-600 dark:text-success-400 font-semibold">
                                                <span class="h-1.5 w-1.5 rounded-full bg-success-500"></span> Thành công
                                            </span>
                                        @elseif ($log->status === 'warning')
                                            <span class="inline-flex items-center gap-1.5 text-warning-600 dark:text-warning-400 font-semibold">
                                                <span class="h-1.5 w-1.5 rounded-full bg-warning-500"></span> Cảnh báo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-danger-600 dark:text-danger-400 font-semibold">
                                                <span class="h-1.5 w-1.5 rounded-full bg-danger-500"></span> Lỗi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-gray-600 dark:text-gray-300 max-w-sm whitespace-normal">
                                        {{ $log->message }}
                                        @if ($log->context)
                                            <div class="mt-1 font-mono text-[9px] text-gray-400 whitespace-pre-wrap truncate bg-gray-50 dark:bg-gray-900 p-1 rounded">
                                                {{ json_encode($log->context, JSON_UNESCAPED_UNICODE) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">Chưa có nhật ký nào được ghi nhận cho bộ lọc này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-6 border-t border-gray-50 dark:border-gray-900">
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    @else
        {{-- Import Config Form and Past Batches list --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            {{-- Configurations Form --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-950 p-6 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm">
                <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white mb-6">Thiết lập tiến trình nhập dữ liệu mới</h2>
                <form wire:submit="startImport" class="space-y-6">
                    {{ $this->form }}
                    <div class="flex justify-end pt-4 border-t border-gray-50 dark:border-gray-900">
                        <x-filament::button type="submit" size="lg" color="primary" class="shadow-md shadow-primary-500/20 hover:scale-105 transition-all">
                            Bắt đầu nhập dữ liệu
                        </x-filament::button>
                    </div>
                </form>
            </div>

            {{-- Past Batches Table --}}
            <div class="lg:col-span-1 bg-white dark:bg-gray-950 p-6 rounded-2xl border border-gray-100 dark:border-gray-900 shadow-sm flex flex-col justify-between min-h-[500px]">
                <div>
                    <h2 class="text-base font-bold tracking-tight text-gray-900 dark:text-white mb-1">Lịch sử các đợt import</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Danh sách các tiến trình đã và đang chạy trên hệ thống.</p>
                    
                    <div class="space-y-4">
                        @forelse ($pastBatches as $batch)
                            <div class="p-4 rounded-xl bg-gray-50/50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-900/50 hover:border-gray-200 dark:hover:border-gray-800 transition-colors flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-bold text-xs text-gray-900 dark:text-white truncate" title="{{ $batch->original_file_name }}">
                                        {{ $batch->original_file_name }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $batch->created_at->format('H:i d/m/Y') }}
                                        @if ($batch->creator)
                                            · bởi {{ $batch->creator->name }}
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center gap-1.5 flex-wrap">
                                        {{-- Inline compact stats --}}
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-success-50 dark:bg-success-950/20 text-success-600 dark:text-success-400 font-medium">
                                            +{{ $batch->imported_items }}
                                        </span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-primary-50 dark:bg-primary-950/20 text-primary-600 dark:text-primary-400 font-medium">
                                            u{{ $batch->updated_items }}
                                        </span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-warning-50 dark:bg-warning-950/20 text-warning-600 dark:text-warning-400 font-medium">
                                            s{{ $batch->skipped_items }}
                                        </span>
                                        @if ($batch->failed_items > 0)
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-danger-50 dark:bg-danger-950/20 text-danger-600 dark:text-danger-400 font-medium">
                                                e{{ $batch->failed_items }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    @if ($batch->status === 'pending')
                                        <x-filament::badge color="gray">Chờ</x-filament::badge>
                                    @elseif ($batch->status === 'processing')
                                        <x-filament::badge color="primary" class="animate-pulse">Đang chạy</x-filament::badge>
                                    @elseif ($batch->status === 'completed')
                                        <x-filament::badge color="success">Hoàn thành</x-filament::badge>
                                    @elseif ($batch->status === 'failed')
                                        <x-filament::badge color="danger">Lỗi</x-filament::badge>
                                    @else
                                        <x-filament::badge color="gray">Đã hủy</x-filament::badge>
                                    @endif

                                    <x-filament::button wire:click="viewBatch({{ $batch->id }})" size="xs" color="gray" icon="heroicon-o-eye" class="hover:bg-primary-50 hover:text-primary-600">
                                        Xem
                                    </x-filament::button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-xs text-gray-500">Chưa có đợt nhập dữ liệu nào được thực hiện.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Pagination past batches --}}
                <div class="mt-6 border-t border-gray-50 dark:border-gray-900 pt-4">
                    {{ $pastBatches->links() }}
                </div>
            </div>

        </div>
    @endif
</x-filament-panels::page>
