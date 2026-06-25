@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang | Phòng Khám Đa Khoa Cần Thơ')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-100/50 py-16 px-4">
    <div class="max-w-xl w-full text-center space-y-8 bg-white rounded-3xl border border-slate-100 shadow-xl p-8 md:p-12 relative overflow-hidden group">
        
        <!-- Background Light Gradients -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-sky-200/30 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-teal-200/30 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>

        <!-- 404 Illustration with Pulse Cross -->
        <div class="relative inline-flex flex-col items-center">
            <h1 class="text-8xl md:text-9xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-clinic-blue to-teal-500 select-none leading-none">
                404
            </h1>
            <div class="absolute -top-4 right-4 animate-bounce">
                <span class="inline-flex items-center justify-center p-2.5 bg-sky-50 text-clinic-blue rounded-2xl border border-sky-100 shadow-md">
                    <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </span>
            </div>
        </div>

        <!-- Heading & Description -->
        <div class="space-y-3 relative z-10">
            <h2 class="text-xl md:text-2xl font-black text-slate-900 leading-tight uppercase tracking-tight">
                Không Tìm Thấy Trang Yêu Cầu!
            </h2>
            <p class="text-sm md:text-base text-slate-600 leading-relaxed max-w-md mx-auto">
                Đường dẫn bạn truy cập có thể đã hết hạn, bị thay đổi hoặc không tồn tại. Hãy thử tìm kiếm bài viết khác hoặc quay lại trang chủ.
            </p>
        </div>

        <!-- Inline Search Form -->
        <form action="{{ route('search') }}" method="GET" class="w-full max-w-md mx-auto relative z-10">
            <div class="relative flex items-center">
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." required 
                       class="w-full pl-5 pr-14 py-4 text-sm bg-slate-50 border border-slate-200 rounded-2xl shadow-inner focus:bg-white focus:outline-none focus:ring-2 focus:ring-clinic-blue focus:border-transparent transition-all duration-300">
                <button type="submit" class="absolute right-2 p-2 bg-gradient-to-r from-clinic-blue to-teal-500 text-white rounded-xl shadow-md hover:opacity-95 transition-opacity duration-300" aria-label="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Quick Links (Categories) -->
        <div class="space-y-4 pt-4 border-t border-slate-100 relative z-10">
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest">Danh mục nổi bật</span>
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="{{ url('category/nam-khoa') }}" class="px-4 py-2 bg-slate-50 hover:bg-sky-50 hover:text-clinic-blue border border-slate-200 hover:border-sky-100 rounded-xl text-xs font-bold text-slate-600 transition-all duration-300 shadow-sm">
                    Nam Khoa
                </a>
                <a href="{{ url('category/phu-khoa') }}" class="px-4 py-2 bg-slate-50 hover:bg-sky-50 hover:text-clinic-blue border border-slate-200 hover:border-sky-100 rounded-xl text-xs font-bold text-slate-600 transition-all duration-300 shadow-sm">
                    Phụ Khoa
                </a>
                <a href="{{ url('category/benh-xa-hoi') }}" class="px-4 py-2 bg-slate-50 hover:bg-sky-50 hover:text-clinic-blue border border-slate-200 hover:border-sky-100 rounded-xl text-xs font-bold text-slate-600 transition-all duration-300 shadow-sm">
                    Bệnh Xã Hội
                </a>
                <a href="{{ url('category/xet-nghiem') }}" class="px-4 py-2 bg-slate-50 hover:bg-sky-50 hover:text-clinic-blue border border-slate-200 hover:border-sky-100 rounded-xl text-xs font-bold text-slate-600 transition-all duration-300 shadow-sm">
                    Xét Nghiệm
                </a>
            </div>
        </div>

        <!-- Back & Hotline Actions -->
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-center relative z-10 pt-2">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-clinic-blue to-teal-500 hover:shadow-lg hover:shadow-sky-500/20 text-white font-extrabold rounded-2xl text-sm transition-all duration-300 group-hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Quay lại trang chủ
            </a>
            <a href="tel:{{ preg_replace('/\D/', '', \App\Models\Setting::site('hotline')) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 text-slate-700 font-extrabold rounded-2xl text-sm transition-all duration-300 shadow-sm">
                <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z" clip-rule="evenodd" />
                </svg>
                Tổng đài tư vấn
            </a>
        </div>

    </div>
</div>
@endsection
