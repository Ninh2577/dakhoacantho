@extends('layouts.app')

@section('title', $article->meta_title ?? $article->title . ' | Phòng Khám Đa Khoa Cần Thơ')

@section('meta')
    <meta name="description" content="{{ $article->meta_description ?? Str::limit(strip_tags($article->content), 160) }}">
    <meta property="og:title" content="{{ $article->meta_title ?? $article->title }}">
    <meta property="og:description" content="{{ $article->meta_description ?? Str::limit(strip_tags($article->content), 160) }}">
    @if($article->thumbnail_image)
        <meta property="og:image" content="{{ asset('storage/' . $article->thumbnail_image) }}">
    @endif
    <meta property="og:type" content="article">
@endsection

@section('content')
<!-- Custom style overrides for rich text content formatting -->
<style>
    .rich-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-top: 2rem;
        margin-bottom: 1rem;
        border-left: 4px solid #0d9488;
        padding-left: 0.75rem;
        line-height: 1.35;
    }
    .rich-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .rich-content p {
        margin-bottom: 1.25rem;
        line-height: 1.75;
        color: #334155;
    }
    .rich-content ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1.25rem;
        color: #334155;
    }
    .rich-content ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1.25rem;
        color: #334155;
    }
    .rich-content li {
        margin-bottom: 0.5rem;
    }
    .rich-content img {
        border-radius: 0.75rem;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        max-width: 100%;
        height: auto;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }
    .rich-content strong {
        color: #0f172a;
        font-weight: 600;
    }
    .rich-content blockquote {
        border-left: 4px solid #cbd5e1;
        padding-left: 1rem;
        font-style: italic;
        margin: 1.5rem 0;
        color: #475569;
    }
</style>

<div class="py-8 md:py-12 bg-slate-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-xs md:text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="hover:text-teal-600 inline-flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 012 0v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Trang Chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 hover:text-teal-600 transition-colors font-medium">{{ $article->category->name }}</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-slate-400 truncate max-w-[200px] md:max-w-sm">{{ $article->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Article Container -->
        <article class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            
            <!-- Article Header -->
            <div class="p-6 md:p-10 border-b border-slate-100 bg-gradient-to-b from-slate-50/50 to-white">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 uppercase tracking-wider mb-4">
                    {{ $article->category->name }}
                </span>
                
                <h1 class="text-2xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4 tracking-tight">
                    {{ $article->title }}
                </h1>
                
                <div class="flex items-center gap-4 text-xs md:text-sm text-slate-500">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ $article->created_at->format('d/m/Y') }}</span>
                    </div>
                    <span class="text-slate-300">|</span>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span>Cẩm nang y tế</span>
                    </div>
                </div>
            </div>

            <!-- Featured Thumbnail -->
            @if($article->thumbnail_image)
                <div class="px-6 md:px-10 pt-8">
                    <img src="{{ asset('storage/' . $article->thumbnail_image) }}" alt="{{ $article->title }}" class="w-full h-auto max-h-[450px] object-cover rounded-2xl shadow-sm">
                </div>
            @endif

            <!-- Article Content Body -->
            <div class="p-6 md:p-10 text-slate-700 text-base md:text-lg leading-relaxed rich-content">
                {!! $article->content !!}
            </div>

            <!-- Booking / Call to Action Box inside Article -->
            <div class="m-6 md:m-10 p-6 md:p-8 bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl border border-teal-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center md:text-left">
                    <h3 class="text-lg md:text-xl font-bold text-slate-900">Đặt lịch hẹn trực tuyến</h3>
                    <p class="text-sm text-slate-600 leading-normal max-w-lg">
                        Nhận tư vấn sức khỏe miễn phí từ bác sĩ chuyên khoa và đặt số khám nhanh chóng không cần chờ đợi.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <a href="tel:0292123456" class="inline-flex items-center justify-center px-6 py-3 border border-teal-600 text-teal-700 hover:bg-teal-50 font-bold rounded-xl text-sm transition-all duration-200 text-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        0292 123 456
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white font-bold rounded-xl text-sm shadow-md shadow-teal-500/10 transition-all duration-200 text-center">
                        Đặt Lịch Ngay
                    </a>
                </div>
            </div>
            
        </article>
    </div>
</div>
@endsection
