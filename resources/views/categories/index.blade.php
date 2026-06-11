@extends('layouts.app')

@section('title', 'Tất cả Chuyên khoa | Danh mục Chuyên khoa')

@section('meta')
    @php
        $indexBreadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Chuyên khoa', 'url' => route('categories.index')]
        ];
    @endphp
    <x-seo
        page-type="category"
        title="Tất cả Chuyên khoa | Danh mục Chuyên khoa"
        description="Tổng hợp tất cả các danh mục chuyên khoa tại Phòng Khám Đa Khoa Gia Phước Cần Thơ: Nam khoa, Phụ khoa, Bệnh xã hội..."
        :canonical="route('categories.index')"
        :breadcrumbs="$indexBreadcrumbs"
    />
@endsection

@php
    $featuredArticle = $articles->currentPage() == 1 ? $articles->first() : null;
@endphp

@section('content')
<div class="py-8 md:py-12 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs -->
        <nav class="flex mb-6 text-xs md:text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-clinic-blue inline-flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 012 0v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Trang chủ
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-slate-400 font-medium">Tất cả chuyên khoa</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Category Title Header -->
        <div class="mb-10 space-y-3">
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Danh mục Chuyên khoa</h1>
            <p class="text-slate-600 text-sm md:text-base max-w-4xl leading-relaxed">
                Hệ thống chuyên khoa đa dạng với đội ngũ bác sĩ giàu kinh nghiệm, trang thiết bị hiện đại, cam kết mang lại dịch vụ chăm sóc sức khỏe tốt nhất cho cộng đồng.
            </p>
        </div>

        <!-- Main Layout Split -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Sidebar (lg:col-span-3) -->
            <aside class="lg:col-span-3 space-y-6">
                <!-- Group filter list -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="bg-sky-50 px-5 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-clinic-blue uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Lọc theo nhóm
                        </h3>
                    </div>
                    
                    <div class="p-5 space-y-1">
                        <!-- All categories link (Active by default here) -->
                        <a href="{{ route('categories.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition-all bg-clinic-blue text-white shadow-sm">
                            <span>Tất cả chuyên khoa</span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </a>

                        @foreach($parentCategories as $category)
                            <a href="{{ $category->public_url }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold transition-all text-slate-600 hover:bg-slate-50">
                                <span>{{ $category->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Consultation promo banner -->
                <div class="bg-gradient-to-br from-[#0a3875] to-[#082a58] text-white rounded-2xl p-6 shadow-md border border-white/10 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <div class="space-y-4 relative">
                        <h4 class="text-base font-extrabold tracking-tight">Tư vấn trực tuyến</h4>
                        <p class="text-xs text-slate-200 leading-relaxed font-medium">
                            Bác sĩ chuyên khoa đang chờ giải đáp thắc mắc của bạn.
                        </p>
                        <a href="#" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white hover:bg-slate-50 text-[#0a3875] font-extrabold text-xs rounded-xl shadow-sm transition-all" onclick="alert('Đang kết nối đến bác sĩ tư vấn...');">
                            Chat với bác sĩ
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Right Content Area (lg:col-span-9) -->
            <section class="lg:col-span-9 space-y-8">
                
                <!-- Featured Article (Horizontal Card) -->
                @if($featuredArticle)
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden grid grid-cols-1 md:grid-cols-12 group hover:shadow-md transition-all duration-300">
                        <!-- Image Column -->
                        <div class="md:col-span-5 relative aspect-video md:aspect-auto overflow-hidden bg-slate-100">
                            @if($featuredArticle->thumbnail_image)
                                <img src="{{ asset('storage/' . $featuredArticle->thumbnail_image) }}" alt="{{ $featuredArticle->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" decoding="async">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                    <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Info Column -->
                        <div class="md:col-span-7 p-6 md:p-8 flex flex-col justify-between space-y-4">
                            <div class="space-y-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-clinic-teal uppercase">
                                    {{ $featuredArticle->category->name }}
                                </span>
                                
                                <h2 class="text-xl md:text-2xl font-black text-slate-900 group-hover:text-clinic-blue transition-colors leading-tight">
                                    <a href="{{ $featuredArticle->public_url }}">
                                        {{ $featuredArticle->title }}
                                    </a>
                                </h2>
                                
                                <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                                    {{ $featuredArticle->meta_description ?? Str::limit(strip_tags($featuredArticle->content), 150) }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
                                <a href="{{ $featuredArticle->public_url }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl text-xs shadow-md transition-all">
                                    Xem chi tiết
                                </a>
                                <button class="p-2.5 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-500 transition-all" aria-label="Share">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 10.742l4.607-2.304m0 4.124l-4.607 2.304m-1.78-.328a3.3 3.3 0 100-6.6 3.3 3.3 0 000 6.6zm10.136-6.08a3.3 3.3 0 100-6.6 3.3 3.3 0 000 6.6zm0 12.16a3.3 3.3 0 100-6.6 3.3 3.3 0 000 6.6z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Article Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                    @forelse($articles as $index => $article)
                        @if($articles->currentPage() == 1 && $index == 0)
                            @continue
                        @endif
                        <x-article-card :article="$article" />
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-3xl border border-slate-100 shadow-sm">
                            Không tìm thấy bài viết nào.
                        </div>
                    @endforelse
                </div>

                <!-- Highlight alert / info banner -->
                @if($featuredArticle)
                    <div class="p-6 bg-sky-50 rounded-2xl border border-sky-100 flex items-start gap-4 shadow-sm">
                        <span class="p-3 bg-clinic-sky text-white rounded-xl shadow-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </span>
                        <div class="space-y-2 flex-grow">
                            <h4 class="text-sm font-extrabold text-slate-900">Kiến thức Y khoa cần thiết</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Tìm hiểu chi tiết và phòng ngừa các biến chứng nguy hiểm của bệnh học.
                            </p>
                            <span class="block text-[10px] text-slate-400 font-bold">Cậu Lông Team &bull; {{ date('d/m/Y') }}</span>
                        </div>
                        <a href="{{ $featuredArticle->public_url }}" class="inline-flex items-center justify-center px-4 py-2 bg-clinic-sky hover:bg-opacity-95 text-white font-extrabold rounded-xl text-xs shadow-md transition-all self-center">
                            Đọc bài viết
                        </a>
                    </div>
                @endif

                <!-- Pagination Component -->
                @if($articles->hasPages())
                    <nav class="flex items-center justify-center pt-8 border-t border-slate-100" aria-label="Pagination">
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

                            <!-- Page Numbers (Windowed to prevent horizontal overflow) -->
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

            </section>

        </div>

    </div>
</div>
@endsection
