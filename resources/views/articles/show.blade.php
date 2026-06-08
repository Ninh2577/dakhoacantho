@extends('layouts.app')

@section('title', e($article->meta_title ?? $article->title . ' | Phòng Khám Đa Khoa Gia Phước'))

@section('meta')
    @php
        $rawDesc = $article->meta_description 
            ?? $article->excerpt 
            ?? (trim(strip_tags($article->content ?? '')) !== '' ? Str::limit(strip_tags($article->content), 160) : $article->title);
        $seoDesc = trim($rawDesc);
    @endphp
    
    <meta name="description" content="{{ $seoDesc }}">
    
    {{-- Canonical link --}}
    <link rel="canonical" href="{{ $article->canonical_url ?? url()->current() }}">

    {{-- Robots metadata --}}
    <meta name="robots" content="{{ ($article->robots_index ?? true) ? 'index' : 'noindex' }},{{ ($article->robots_follow ?? true) ? 'follow' : 'nofollow' }}">

    {{-- Open Graph (Facebook) --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $article->og_title ?? $article->meta_title ?? $article->title }}">
    <meta property="og:description" content="{{ $article->og_description ?? $seoDesc }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($article->og_image)
        <meta property="og:image" content="{{ asset('storage/' . $article->og_image) }}">
    @elseif($article->thumbnail_image)
        <meta property="og:image" content="{{ asset('storage/' . $article->thumbnail_image) }}">
    @endif

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $article->twitter_title ?? $article->meta_title ?? $article->title }}">
    <meta name="twitter:description" content="{{ $article->twitter_description ?? $seoDesc }}">
    @if($article->twitter_image)
        <meta name="twitter:image" content="{{ asset('storage/' . $article->twitter_image) }}">
    @elseif($article->thumbnail_image)
        <meta name="twitter:image" content="{{ asset('storage/' . $article->thumbnail_image) }}">
    @endif

    {{-- BreadcrumbList JSON-LD Schema --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": "{{ url('/') }}"
        },
        {
          "@@type": "ListItem",
          "position": 2,
          "name": "{{ $article->category->name }}",
          "item": "{{ route('category.show', ['category_path' => $article->category_path]) }}"
        },
        {
          "@@type": "ListItem",
          "position": 3,
          "name": "{{ $article->title }}",
          "item": "{{ url()->current() }}"
        }
      ]
    }
    </script>

    {{-- BlogPosting JSON-LD Schema (ISO 8601 Dates, Neutral Author) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "BlogPosting",
      "headline": "{{ $article->title }}",
      "description": "{{ $seoDesc }}",
      "image": "{{ $article->thumbnail_image ? asset('storage/' . $article->thumbnail_image) : asset('images/doctor.png') }}",
      "datePublished": "{{ $article->created_at->toIso8601String() }}",
      "dateModified": "{{ $article->updated_at->toIso8601String() }}",
      "author": {
        "@@type": "Organization",
        "name": "Ban Biên Tập - Phòng Khám Đa Khoa Gia Phước",
        "url": "{{ url('/') }}"
      },
      "publisher": {
        "@@type": "Organization",
        "name": "Phòng Khám Đa Khoa Gia Phước",
        "logo": {
          "@@type": "ImageObject",
          "url": "{{ asset('images/doctor.png') }}"
        }
      },
      "mainEntityOfPage": {
        "@@type": "WebPage",
        "@@id": "{{ url()->current() }}"
      }
    }
    </script>
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
    .rich-content a {
        color: #0d9488;
        font-weight: 600;
        text-decoration: underline;
    }
    .rich-content a:hover {
        color: #0f766e;
    }
</style>

<!-- Reading Progress Bar (Alpine.js) -->
<div x-data="{ scrollPercent: 0 }" 
     x-init="window.addEventListener('scroll', () => { 
         let scrollTop = window.scrollY || document.documentElement.scrollTop;
         let scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
         scrollPercent = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
     })" 
     class="fixed top-0 left-0 right-0 h-1 bg-clinic-teal z-50 transition-all duration-75" 
     :style="'width: ' + scrollPercent + '%'">
</div>

<div class="py-8 md:py-12 bg-slate-50" x-data="tocComponent()" x-init="initTOC()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumbs Layout -->
        <nav class="flex mb-6 text-xs md:text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ url('/') }}" class="hover:text-teal-600 inline-flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 012 0v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Trang Chủ
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('category.show', ['category_path' => $article->category_path]) }}" class="ml-1 md:ml-2 hover:text-teal-600 transition-colors font-medium">
                            {{ $article->category->name }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 md:ml-2 text-slate-400 truncate max-w-[150px] md:max-w-sm">{{ $article->title }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Premium Two-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Content Column -->
            <div class="lg:col-span-8 space-y-8">
                
                <article class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    
                    <!-- Article Header Card -->
                    <div class="p-6 md:p-10 border-b border-slate-100 bg-gradient-to-b from-slate-50/50 to-white">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-teal-50 text-teal-700 uppercase tracking-wider mb-4">
                            {{ $article->category->name }}
                        </span>
                        
                        <h1 class="text-2xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-5 tracking-tight">
                            {{ $article->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-slate-500 font-semibold">
                            <!-- Published Date -->
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
                                </svg>
                                <span>{{ $article->created_at->format('d/m/Y') }}</span>
                            </div>

                            <!-- Updated Date (if different) -->
                            @if($article->updated_at->format('Y-m-d') !== $article->created_at->format('Y-m-d'))
                                <span class="text-slate-300">|</span>
                                <div class="flex items-center gap-1.5 text-slate-450">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                                    </svg>
                                    <span>Cập nhật: {{ $article->updated_at->format('d/m/Y') }}</span>
                                </div>
                            @endif

                            <span class="text-slate-300">|</span>
                            
                            <!-- Reading Time -->
                            @php
                                $vietnameseWordCount = count(explode(' ', trim(strip_tags($article->content))));
                                $readingTime = max(1, (int) ceil($vietnameseWordCount / 220));
                            @endphp
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $readingTime }} phút đọc</span>
                            </div>

                            <span class="text-slate-300">|</span>
                            
                            <!-- Editor Label -->
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"></path>
                                </svg>
                                <span>Biên tập nội dung</span>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Thumbnail -->
                    @if($article->thumbnail_image)
                        <div class="px-6 md:px-10 pt-8">
                            <img src="{{ asset('storage/' . $article->thumbnail_image) }}" alt="{{ $article->title }}" class="w-full h-auto max-h-[450px] object-cover rounded-2xl shadow-sm">
                        </div>
                    @endif

                    <!-- Article Body -->
                    <div class="p-6 md:p-10">
                        
                        <!-- Collapsible Mobile TOC (Only visible on Mobile/Tablet) -->
                        <div x-show="headings.length >= 2" class="lg:hidden bg-slate-50 border border-slate-200/60 rounded-2xl p-4 mb-6">
                            <button @click="isOpen = !isOpen" class="w-full flex items-center justify-between text-slate-800 font-extrabold text-sm uppercase tracking-wider">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4.5 h-4.5 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
                                    </svg>
                                    Mục lục bài viết
                                </span>
                                <svg class="w-4.5 h-4.5 transform transition-transform duration-200" :class="isOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"></path>
                                </svg>
                            </button>
                            <div x-show="isOpen" x-transition class="mt-4 border-t border-slate-200/60 pt-3">
                                <nav class="space-y-2.5">
                                    <template x-for="heading in headings" :key="heading.id">
                                        <a :href="'#' + heading.id" 
                                           @click.prevent="scrollToHeading(heading.id)"
                                           class="block text-slate-650 hover:text-clinic-teal text-sm leading-snug transition-colors"
                                           :class="heading.level === 'H3' ? 'pl-4 text-[13px] text-slate-500' : 'font-bold'"
                                           x-text="heading.text"></a>
                                    </template>
                                </nav>
                            </div>
                        </div>

                        <!-- Rich Text Content Wrapper -->
                        <div class="prose prose-slate max-w-none text-slate-700 text-base md:text-lg leading-relaxed rich-content">
                            {!! $article->content !!}
                        </div>

                        <!-- Medical Disclaimer Box -->
                        <div class="bg-slate-50 border-l-4 border-slate-400 rounded-r-2xl p-5 my-8">
                            <p class="text-xs md:text-sm text-slate-600 leading-relaxed font-semibold">
                                <strong>Tuyên bố miễn trừ trách nhiệm y tế:</strong> Bài viết chỉ mang tính chất tham khảo, không thay thế cho việc tư vấn hoặc thăm khám trực tiếp tại cơ sở y tế. Nếu bạn có bất kỳ triệu chứng bất thường nào, vui lòng liên hệ ngay với <strong>Phòng Khám Đa Khoa Gia Phước</strong> qua số hotline <a href="tel:0966332352" class="text-clinic-teal font-extrabold hover:underline">0966.332.352</a> để nhận hỗ trợ và tư vấn phù hợp nhất.
                            </p>
                        </div>

                        <!-- Engagement Bar (Likes, Bookmarks, Copy Link, FB & Zalo Share) -->
                        <div x-data="{ 
                            liked: false, 
                            bookmarked: false,
                            copied: false,
                            init() {
                                this.liked = localStorage.getItem('liked_' + {{ $article->id }}) === 'true';
                                this.bookmarked = localStorage.getItem('bookmarked_' + {{ $article->id }}) === 'true';
                            },
                            toggleLike() {
                                this.liked = !this.liked;
                                localStorage.setItem('liked_' + {{ $article->id }}, this.liked);
                            },
                            toggleBookmark() {
                                this.bookmarked = !this.bookmarked;
                                localStorage.setItem('bookmarked_' + {{ $article->id }}, this.bookmarked);
                            },
                            copyLink() {
                                navigator.clipboard.writeText(window.location.href).then(() => {
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000);
                                });
                            }
                        }" class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-slate-100 my-6">
                            <div class="flex items-center gap-3">
                                <!-- Like Button -->
                                <button @click="toggleLike()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all active:scale-95 duration-150"
                                        :class="liked ? 'bg-rose-50 border-rose-100 text-rose-600' : 'bg-slate-50 border-slate-100 text-slate-650 hover:bg-slate-100'">
                                    <svg class="w-4.5 h-4.5" :class="liked ? 'fill-rose-600 stroke-rose-600 scale-110' : 'fill-none stroke-current'" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <span x-text="liked ? 'Đã thích' : 'Thích'">Thích</span>
                                </button>

                                <!-- Favorite/Bookmark Button -->
                                <button @click="toggleBookmark()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all active:scale-95 duration-150"
                                        :class="bookmarked ? 'bg-amber-50 border-amber-100 text-amber-600' : 'bg-slate-50 border-slate-100 text-slate-650 hover:bg-slate-100'">
                                    <svg class="w-4.5 h-4.5" :class="bookmarked ? 'fill-amber-500 stroke-amber-500' : 'fill-none stroke-current'" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.154 1.907 1.1 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.085.808-2.031 1.907-2.185a48.507 48.507 0 0112.186 0z" />
                                    </svg>
                                    <span x-text="bookmarked ? 'Đã lưu' : 'Lưu lại'">Lưu lại</span>
                                </button>

                                <!-- Copy Link Button -->
                                <button @click="copyLink()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold border bg-slate-50 border-slate-100 text-slate-650 hover:bg-slate-100 transition-all active:scale-95 duration-150">
                                    <svg class="w-4.5 h-4.5" x-show="!copied" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                    </svg>
                                    <svg class="w-4.5 h-4.5 text-green-600 animate-pulse" x-show="copied" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span x-text="copied ? 'Đã sao chép!' : 'Sao chép link'">Sao chép link</span>
                                </button>
                            </div>

                            <!-- Social Shares -->
                            <div class="flex items-center gap-2.5 text-xs font-bold text-slate-500">
                                <span>Chia sẻ:</span>
                                <!-- Facebook Share -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-full bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-600 border border-slate-100 transition-colors" title="Chia sẻ Facebook">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                                    </svg>
                                </a>
                                <!-- Zalo Share -->
                                <a href="https://zalo.me/share?url={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener noreferrer" class="p-2 rounded-full bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-[#0068ff] border border-slate-100 flex items-center justify-center transition-colors text-[10px] w-8 h-8 font-black shrink-0" title="Chia sẻ Zalo">
                                    Zalo
                                </a>
                            </div>
                        </div>

                    </div>
                    
                </article>

                <!-- Bottom Related Articles Section -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-6">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-clinic-teal rounded-full"></span>
                        Bài viết liên quan hữu ích
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @forelse($relatedArticles as $related)
                            <div class="bg-slate-50 rounded-2xl border border-slate-100/60 overflow-hidden flex flex-col group transition-all hover:shadow-sm">
                                <a href="{{ route('article.show', ['category_path' => $related->category_path, 'slug' => $related->slug]) }}" class="block overflow-hidden aspect-video relative">
                                    @if($related->thumbnail_image)
                                        <img src="{{ asset('storage/' . $related->thumbnail_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350" loading="lazy">
                                    @else
                                        <div class="w-full h-full bg-slate-200 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full text-[9px] font-black text-clinic-teal uppercase shadow-sm">
                                        {{ $related->category->name }}
                                    </span>
                                </a>
                                <div class="p-4 space-y-2 flex-grow flex flex-col justify-between">
                                    <div class="space-y-1">
                                        <a href="{{ route('article.show', ['category_path' => $related->category_path, 'slug' => $related->slug]) }}" class="block">
                                            <h4 class="text-sm font-extrabold text-slate-800 line-clamp-2 group-hover:text-clinic-teal transition-colors leading-snug">{{ $related->title }}</h4>
                                        </a>
                                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                            {{ trim(strip_tags($related->content)) !== '' ? Str::limit(strip_tags($related->content), 80) : $related->title }}
                                        </p>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-semibold block pt-2">{{ $related->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-400 col-span-2">Không có bài viết liên quan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Approved Comments Section -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-6">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-clinic-teal rounded-full"></span>
                        Ý kiến bạn đọc ({{ $article->comments->count() }})
                    </h3>
                    
                    @if($article->comments->isEmpty())
                        <div class="bg-slate-50 border border-dashed border-slate-200 rounded-3xl p-8 text-center space-y-2">
                            <svg class="w-10 h-10 text-slate-350 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.625.625 0 11-1.25 0 .625.625 0 011.25 0zm0 0H8.63m2.525 0a.625.625 0 11-1.25 0 .625.625 0 011.25 0zm0 0h.01M12 7.5h.01M3 20.25V4.75A1.75 1.75 0 014.75 3h14.5A1.75 1.75 0 0121 4.75v10.5A1.75 1.75 0 0119.25 17H8.25l-5.25 3.25z"></path>
                            </svg>
                            <p class="text-slate-500 font-bold text-sm">Chưa có bình luận nào</p>
                            <p class="text-slate-400 text-xs font-semibold">Hãy là người đầu tiên chia sẻ câu hỏi của bạn.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($article->comments as $comment)
                                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-5 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-extrabold text-sm text-slate-800 flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-full bg-clinic-teal/10 text-clinic-teal flex items-center justify-center font-black text-xs uppercase">
                                                {{ substr($comment->name, 0, 1) }}
                                            </span>
                                            {{ $comment->name }}
                                        </h4>
                                        <span class="text-xs text-slate-400 font-bold">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-650 leading-relaxed pl-10 font-medium whitespace-pre-line">{{ $comment->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Comment Submission Form (CSRF, Honeypot website, Validation, alpine loading check) -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 md:p-8 shadow-sm space-y-6">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Gửi câu hỏi của bạn</h3>
                    
                    @if(session('comment_success'))
                        <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-bold animate-pulse">
                            {{ session('comment_success') }}
                        </div>
                    @endif

                    <form action="{{ route('articles.comments.store', $article) }}" method="POST" 
                          x-data="{ 
                              name: '{{ old('name') }}', 
                              phone: '{{ old('phone') }}', 
                              content: '{{ old('content') }}', 
                              website: '',
                              isSubmitting: false, 
                              errors: {} 
                          }" 
                          @submit="
                              errors = {};
                              if (!name.trim()) errors.name = 'Vui lòng nhập họ và tên của bạn.';
                              if (phone.trim() && !/^(03|05|07|08|09)\d{8}$/.test(phone.trim())) errors.phone = 'Số điện thoại không hợp lệ.';
                              if (!content.trim()) errors.content = 'Vui lòng nhập nội dung câu hỏi/bình luận.';
                              
                              if (Object.keys(errors).length > 0) {
                                  $event.preventDefault();
                                  return;
                              }
                              isSubmitting = true;
                          "
                          class="space-y-4">
                        @csrf

                        <!-- Honeypot field (hidden from users, bot trap) -->
                        <div style="display: none;" class="hidden" aria-hidden="true">
                            <input type="text" name="website" x-model="website" autocomplete="off" tabindex="-1">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Name input -->
                            <div class="space-y-1">
                                <label for="comment-name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Họ và tên <span class="text-rose-500">*</span></label>
                                <input type="text" id="comment-name" name="name" x-model="name" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-clinic-teal focus:ring-1 focus:ring-clinic-teal focus:outline-none" placeholder="Ví dụ: Nguyễn Văn A">
                                <span class="text-xs text-rose-500 font-bold block" x-show="errors.name" x-text="errors.name"></span>
                                @error('name')
                                    <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone input (for admin use only, fully private) -->
                            <div class="space-y-1">
                                <label for="comment-phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Số điện thoại (Nhận phản hồi tư vấn)</label>
                                <input type="tel" id="comment-phone" name="phone" x-model="phone" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-clinic-teal focus:ring-1 focus:ring-clinic-teal focus:outline-none" placeholder="09xxxxxxxx (Bảo mật tuyệt đối)">
                                <span class="text-xs text-rose-500 font-bold block" x-show="errors.phone" x-text="errors.phone"></span>
                                @error('phone')
                                    <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Content textarea -->
                        <div class="space-y-1">
                            <label for="comment-content" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nội dung câu hỏi cần giải đáp <span class="text-rose-500">*</span></label>
                            <textarea id="comment-content" name="content" x-model="content" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:border-clinic-teal focus:ring-1 focus:ring-clinic-teal focus:outline-none" placeholder="Nhập câu hỏi chi tiết về tình trạng sức khỏe của bạn để nhận tư vấn phù hợp nhất..."></textarea>
                            <span class="text-xs text-rose-500 font-bold block" x-show="errors.content" x-text="errors.content"></span>
                            @error('content')
                                <span class="text-xs text-rose-500 font-bold block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit button -->
                        <button type="submit" 
                                :disabled="isSubmitting" 
                                class="w-full sm:w-auto px-6 py-3 bg-clinic-blue hover:bg-clinic-blue/90 disabled:bg-slate-300 text-white font-extrabold rounded-xl text-sm transition-all shadow-md active:scale-95 duration-150 inline-flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4.5 w-4.5 text-white" x-show="isSubmitting" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Đang gửi...' : 'Gửi câu hỏi'">Gửi câu hỏi</span>
                        </button>
                    </form>
                </div>

            </div>

            <!-- Sticky Sidebar Column (Desktop only, hidden on mobile) -->
            <aside class="lg:col-span-4 sticky top-6 space-y-6 hidden lg:block">
                
                <!-- Table of Contents Widget -->
                <div x-show="headings.length >= 2" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
                        </svg>
                        Mục lục bài viết
                    </h3>
                    <nav class="space-y-2.5">
                        <template x-for="heading in headings" :key="heading.id">
                            <a :href="'#' + heading.id" 
                               @click.prevent="scrollToHeading(heading.id)"
                               class="block text-slate-600 hover:text-clinic-teal text-sm leading-snug transition-colors"
                               :class="heading.level === 'H3' ? 'pl-4 text-[13px] text-slate-450' : 'font-bold'"
                               x-text="heading.text"></a>
                        </template>
                    </nav>
                </div>

                <!-- Sticky Quick Booking Banner -->
                <div class="bg-gradient-to-br from-clinic-blue to-[#07244e] text-white rounded-3xl shadow-md p-6 text-center space-y-4">
                    <h4 class="text-base font-black uppercase tracking-tight">Tư vấn sức khỏe 24/7</h4>
                    <p class="text-xs text-blue-100 font-semibold leading-relaxed">
                        Nhận hướng dẫn sức khỏe trực tuyến nhanh chóng, bảo mật thông tin và hoàn toàn miễn phí.
                    </p>
                    <div class="space-y-2.5 pt-2">
                        <a href="tel:0966332352" class="block w-full bg-white text-clinic-blue font-extrabold py-2.5 rounded-xl text-sm transition-all hover:bg-blue-50 active:scale-95 duration-150 text-center shadow-sm">
                            Gọi hotline: 0966.332.352
                        </a>
                        <a href="{{ route('contact') }}" class="block w-full bg-clinic-teal text-white font-extrabold py-2.5 rounded-xl text-sm transition-all hover:bg-clinic-teal/90 active:scale-95 duration-150 text-center shadow-sm">
                            Đăng ký đặt lịch khám
                        </a>
                    </div>
                </div>

                <!-- Sidebar Related Articles -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Bài đọc hữu ích khác
                    </h3>
                    <div class="space-y-4">
                        @forelse($relatedArticles as $related)
                            <a href="{{ route('article.show', ['category_path' => $related->category_path, 'slug' => $related->slug]) }}" class="group flex gap-3.5 items-start">
                                @if($related->thumbnail_image)
                                    <img src="{{ asset('storage/' . $related->thumbnail_image) }}" alt="{{ $related->title }}" class="w-14 h-14 rounded-xl object-cover shrink-0" loading="lazy">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 border border-slate-100">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="space-y-1">
                                    <span class="text-[9px] font-black text-clinic-teal uppercase">{{ $related->category->name }}</span>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2 group-hover:text-clinic-teal transition-colors leading-snug">{{ $related->title }}</h4>
                                </div>
                            </a>
                        @empty
                            <p class="text-xs text-slate-400">Không có bài viết khác.</p>
                        @endforelse
                    </div>
                </div>

            </aside>

        </div>

    </div>
</div>

<!-- Mobile Bottom Sticky CTA (Only visible on Mobile/Tablet) -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-3 shadow-2xl flex gap-3">
    <!-- Call Button -->
    <a href="tel:0966332352" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-white border border-clinic-teal text-clinic-teal font-extrabold rounded-xl text-sm transition-all shadow-sm active:scale-95 duration-150">
        <svg class="w-4.5 h-4.5 animate-bounce" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
        Gọi tư vấn
    </a>
    <!-- Booking Button -->
    <a href="{{ route('contact') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-clinic-blue text-white font-extrabold rounded-xl text-sm transition-all shadow-md active:scale-95 duration-150">
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Đặt lịch khám
    </a>
</div>

<!-- Script to handle client-side automated TOC and lazy load images in rich-content -->
<script>
    function tocComponent() {
        return {
            headings: [],
            isOpen: false,
            initTOC() {
                // Parse H2 and H3 tags inside rich-content on client-side
                this.$nextTick(() => {
                    const elements = document.querySelectorAll('.rich-content h2, .rich-content h3');
                    const found = [];
                    elements.forEach((el, index) => {
                        let id = el.getAttribute('id');
                        if (!id) {
                            id = 'heading-id-' + (index + 1);
                            el.setAttribute('id', id);
                        }
                        found.push({
                            id: id,
                            text: el.innerText || el.textContent,
                            level: el.tagName
                        });
                    });
                    this.headings = found;

                    // Proactively lazy load rich-content images
                    document.querySelectorAll('.rich-content img').forEach(img => {
                        if (!img.hasAttribute('loading')) {
                            img.setAttribute('loading', 'lazy');
                        }
                    });
                });
            },
            scrollToHeading(id) {
                const target = document.getElementById(id);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    }
</script>
@endsection
