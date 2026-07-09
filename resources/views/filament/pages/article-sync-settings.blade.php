<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Thông tin API Đồng bộ Bài viết</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Cung cấp các thông tin dưới đây cho Quản trị viên của website nhận (Sống Khỏe Mỗi Ngày) để cấu hình đồng bộ bài viết tự động.
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Đường dẫn API (API URL)</label>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" readonly value="{{ $this->apiUrl }}" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 dark:bg-gray-700 cursor-text p-2" id="sync-api-url">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token xác thực (Sync Token)</label>
                    <div class="flex rounded-md shadow-sm">
                        <input type="text" readonly value="{{ $this->syncToken }}" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-gray-50 dark:bg-gray-700 cursor-text p-2 font-mono" id="sync-api-token">
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
