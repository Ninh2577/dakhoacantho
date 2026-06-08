@props(['title', 'subtitle', 'articles', 'viewAllUrl' => null])

@php
    // Prepare the articles array for JavaScript/Alpine serialization
    $articlesList = $articles->map(function($article) {
        $excerpt = trim($article->excerpt ?? '');
        if (empty($excerpt)) {
            $excerpt = trim(strip_tags($article->content ?? ''));
            $excerpt = Str::limit($excerpt, 130);
        }
        return [
            'id' => $article->id,
            'title' => $article->title,
            'excerpt' => $excerpt,
            'thumbnail_image' => $article->thumbnail_image ? asset('storage/' . $article->thumbnail_image) : null,
            'category_name' => $article->category->name ?? 'Tin tức',
            'created_at' => $article->created_at->format('d/m/Y'),
            'link' => route('article.show', ['category_path' => $article->category_path, 'slug' => $article->slug])
        ];
    })->toArray();
@endphp

<section class="py-12 md:py-16 bg-slate-50/60 border-y border-slate-100" 
         x-data="{
             articles: [],
             startIndex: 0,
             perPage: 3,
             init() {
                 this.articles = JSON.parse(this.$refs.articlesData.textContent);
                 this.updatePerPage();
                 window.addEventListener('resize', () => this.updatePerPage());
             },
             updatePerPage() {
                 if (window.innerWidth < 640) {
                     this.perPage = 1;
                 } else if (window.innerWidth < 1024) {
                     this.perPage = 2;
                 } else {
                     this.perPage = 3;
                 }
                 if (this.startIndex >= this.articles.length) {
                     this.startIndex = Math.max(0, this.articles.length - this.perPage);
                 }
             },
             get visibleArticles() {
                 return this.articles.slice(this.startIndex, this.startIndex + this.perPage);
             },
             get pageText() {
                 let totalPages = Math.ceil(this.articles.length / this.perPage) || 1;
                 let currentPage = Math.floor(this.startIndex / this.perPage) + 1;
                 return currentPage + ' / ' + totalPages;
             },
             get hasPrev() {
                 return this.startIndex > 0;
             },
             get hasNext() {
                 return this.startIndex + this.perPage < this.articles.length;
             },
             next() {
                 if (this.hasNext) {
                     this.startIndex += this.perPage;
                 }
             },
             prev() {
                 if (this.hasPrev) {
                     this.startIndex -= this.perPage;
                 }
             }
         }">
    <script x-ref="articlesData" type="application/json">@json($articlesList)</script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header area with Controls -->
        <div class="flex items-end justify-between border-b border-slate-250 pb-5">
            <div class="space-y-1.5 max-w-2xl">
                <span class="text-[10px] font-black text-clinic-teal uppercase tracking-widest block">KIẾN THỨC LIÊN QUAN</span>
                <h2 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="text-xs md:text-sm text-slate-500 font-semibold leading-relaxed">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            <!-- Carousel Controls -->
            <div class="flex items-center gap-2 shrink-0 pb-1" x-show="articles.length > perPage" x-cloak>
                <span class="text-xs font-black text-slate-400 mr-2" x-text="pageText"></span>
                <!-- Prev Button -->
                <button @click="prev()" :disabled="!hasPrev" 
                        class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-clinic-teal hover:border-clinic-teal disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:text-slate-600 disabled:hover:border-slate-200 transition-all duration-150 active:scale-95 shadow-sm"
                        aria-label="Xem trang trước">
                    <svg class="w-5 h-5 stroke-current fill-none" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <!-- Next Button -->
                <button @click="next()" :disabled="!hasNext" 
                        class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-600 hover:text-clinic-teal hover:border-clinic-teal disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:text-slate-600 disabled:hover:border-slate-200 transition-all duration-150 active:scale-95 shadow-sm"
                        aria-label="Xem trang sau">
                    <svg class="w-5 h-5 stroke-current fill-none" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Carousel Content Area -->
        <div class="relative min-h-[380px] sm:min-h-[400px]">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <template x-for="article in visibleArticles" :key="article.id">
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col group h-full">
                        
                        <!-- Thumbnail Image -->
                        <a :href="article.link" class="block aspect-[16/10] overflow-hidden relative bg-slate-100 shrink-0">
                            <template x-if="article.thumbnail_image">
                                <img :src="article.thumbnail_image" 
                                     :alt="article.title" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-350" 
                                     loading="lazy">
                            </template>
                            <template x-if="!article.thumbnail_image">
                                <div class="w-full h-full flex flex-col items-center justify-center text-slate-350 space-y-2 bg-gradient-to-br from-slate-50 to-slate-100">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                    </svg>
                                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Đa Khoa Gia Phước</span>
                                </div>
                            </template>
                            <!-- Badge -->
                            <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-[9px] font-black text-clinic-teal uppercase shadow-sm border border-slate-100/50" 
                                  x-text="article.category_name"></span>
                        </a>

                        <!-- Details Body -->
                        <div class="p-5 md:p-6 flex-grow flex flex-col justify-between space-y-4">
                            <div class="space-y-2.5">
                                <a :href="article.link" class="block">
                                    <h3 class="text-sm md:text-base font-extrabold text-slate-800 line-clamp-2 leading-snug group-hover:text-clinic-teal transition-colors duration-200" 
                                        x-text="article.title"></h3>
                                </a>
                                <p class="text-xs md:text-sm text-slate-500 font-medium leading-relaxed line-clamp-3" 
                                   x-text="article.excerpt"></p>
                            </div>

                            <!-- Footer Link details -->
                            <div class="flex items-center justify-between pt-3 border-t border-slate-50 text-xs font-bold shrink-0">
                                <span class="text-slate-400 font-semibold" x-text="article.created_at"></span>
                                <a :href="article.link" class="inline-flex items-center text-clinic-teal hover:text-clinic-teal-dark hover:underline gap-1 transition-colors">
                                    Đọc tiếp
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- View All Link (Bottom CTA) -->
        @if($viewAllUrl && $articles->isNotEmpty())
            <div class="text-center pt-4">
                <a href="{{ $viewAllUrl }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 hover:border-clinic-teal text-slate-650 hover:text-clinic-teal font-extrabold rounded-2xl text-sm transition-all shadow-sm active:scale-95 duration-150">
                    Xem tất cả bài viết
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                    </svg>
                </a>
            </div>
        @endif

    </div>
</section>
