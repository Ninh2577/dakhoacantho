<div class="flex justify-center items-center p-4">
    @if(str_contains($record->file_type, 'image'))
        <img src="{{ $record->url }}" alt="{{ $record->name }}" class="max-w-full max-h-[70vh] rounded-lg shadow-lg object-contain" />
    @else
        <div class="text-center p-6 w-full">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium font-sans">Đây là tệp tin tài liệu, không thể xem trước dưới dạng hình ảnh.</p>
            <a href="{{ $record->url }}" target="_blank" class="mt-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 font-sans">
                Tải xuống tài liệu
            </a>
        </div>
    @endif
</div>
