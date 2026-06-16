@extends('layouts.app')

@section('title', 'Bản xem trước hết hạn | Phòng Khám Đa Khoa Gia Phước')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center px-4 py-16 bg-slate-50">
    <div class="max-w-md w-full bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center space-y-6">
        <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto text-amber-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Bản xem trước hết hạn</h1>
            <p class="text-slate-500 text-sm leading-relaxed">
                Đường dẫn xem trước này đã hết hạn (sau 10 phút) hoặc không hợp lệ. Vui lòng quay lại trang quản trị và bấm nút "Xem trước" để tạo bản xem trước mới.
            </p>
        </div>
        
        <div>
            <a href="{{ url('/admin/articles') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition-colors shadow-sm text-sm">
                Quay lại trang quản lý
            </a>
        </div>
    </div>
</div>
@endsection
