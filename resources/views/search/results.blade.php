@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm cho: "' . ($query ?? '') . '" | Đa Khoa Gia Phước')

@section('meta')
    <x-seo
        page-type="search"
        title="Kết quả tìm kiếm cho: '{{ $query }}' | Đa Khoa Gia Phước"
        description="Kết quả tìm kiếm bài viết y khoa và thông tin tư vấn sức khỏe theo từ khóa '{{ $query }}' tại Phòng Khám Gia Phước."
        :canonical="route('search', ['q' => $query])"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Tìm kiếm', 'url' => route('search', ['q' => $query])]
        ]"
    />
@endsection

@section('content')
<div class="py-8 md:py-12 bg-slate-50/50 min-h-[60vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-6 text-xs md:text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-clinic-blue inline-flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 012 0v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Trang chủ
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 md:ml-2 text-slate-400 font-medium">Tìm kiếm</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Search Header Info -->
        <div class="mb-8 space-y-3">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
                @if(empty(trim($query)))
                    Tìm kiếm bài viết
                @else
                    Kết quả tìm kiếm cho: "{{ $query }}"
                @endif
            </h1>
            <p class="text-slate-500 text-sm md:text-base">
                Có {{ $articles->total() }} bài viết được tìm thấy.
            </p>
        </div>

        <!-- Refine Search Input (Retains Old Values) -->
        <div class="max-w-xl mb-12 bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
            <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-4">
                <div>
                    <label for="q_refine" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Từ khóa (Tên bệnh, triệu chứng...)</label>
                    <input type="text" 
                           name="q" 
                           id="q_refine" 
                           value="{{ $query }}" 
                           placeholder="Nhập từ khóa cần tìm..." 
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold text-slate-800">
                    @error('q')
                        <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date_refine" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Từ ngày</label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date_refine" 
                               value="{{ $startDate }}" 
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold text-slate-600">
                        @error('start_date')
                            <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date_refine" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Đến ngày</label>
                        <input type="date" 
                               name="end_date" 
                               id="end_date_refine" 
                               value="{{ $endDate }}" 
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold text-slate-600">
                        @error('end_date')
                            <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="w-full px-6 py-3.5 mt-2 text-white bg-clinic-blue hover:bg-opacity-95 font-extrabold rounded-xl text-sm transition shadow-md shadow-clinic-blue/10">
                    Tìm kiếm lại
                </button>
            </form>
        </div>

        <!-- Article Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            @forelse($articles as $article)
                <x-article-card :article="$article" />
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100 shadow-sm space-y-4">
                    <span class="inline-block p-4 bg-slate-50 text-slate-300 rounded-2xl">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <h3 class="text-lg font-bold text-slate-800">Không tìm thấy bài viết nào</h3>
                    <p class="text-sm text-slate-400 max-w-sm mx-auto">
                        Vui lòng thử lại với các từ khóa khác hoặc kiểm tra lại định dạng ngày tìm kiếm.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination Component -->
        @if($articles->hasPages())
            <nav class="flex items-center justify-center pt-12 border-t border-slate-100 mt-12" aria-label="Pagination">
                <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                    <!-- Previous Page -->
                    @if($articles->onFirstPage())
                        <span class="p-2.5 bg-white border border-slate-200 text-slate-300 rounded-xl cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}" class="p-2.5 bg-white border border-slate-200 text-slate-600 hover:text-clinic-blue rounded-xl shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif

                    <!-- Page Numbers (Windowed) -->
                    @php
                        $currentPage = $articles->currentPage();
                        $lastPage = $articles->lastPage();
                        $start = max($currentPage - 2, 1);
                        $end = min($currentPage + 2, $lastPage);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $articles->url(1) }}" class="w-10 h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-600 font-extrabold rounded-xl shadow-sm hover:text-clinic-blue transition-all">1</a>
                        @if($start > 2)
                            <span class="w-10 h-10 inline-flex items-center justify-center text-slate-400 font-bold">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span class="w-10 h-10 inline-flex items-center justify-center bg-clinic-blue text-white font-extrabold rounded-xl shadow-md">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $articles->url($page) }}" class="w-10 h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-600 font-extrabold rounded-xl shadow-sm hover:text-clinic-blue transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="w-10 h-10 inline-flex items-center justify-center text-slate-400 font-bold">...</span>
                        @endif
                        <a href="{{ $articles->url($lastPage) }}" class="w-10 h-10 inline-flex items-center justify-center bg-white border border-slate-200 text-slate-600 font-extrabold rounded-xl shadow-sm hover:text-clinic-blue transition-all">{{ $lastPage }}</a>
                    @endif

                    <!-- Next Page -->
                    @if($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}" class="p-2.5 bg-white border border-slate-200 text-slate-600 hover:text-clinic-blue rounded-xl shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="p-2.5 bg-white border border-slate-200 text-slate-300 rounded-xl cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                </div>
            </nav>
        @endif

    </div>
</div>
@endsection
