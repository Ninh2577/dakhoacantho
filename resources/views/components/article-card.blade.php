@props(['article'])

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col group hover:shadow-md hover:border-slate-200 transition-all duration-300">
    <!-- Thumbnail Image -->
    <a href="{{ $article->public_url }}" class="block relative aspect-video overflow-hidden bg-slate-100">
        @if($article->thumbnail_image)
            <img src="{{ asset('storage/' . $article->thumbnail_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
        @else
            <!-- Placeholder design -->
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                <svg class="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
    </a>

    <!-- Card Content -->
    <div class="p-5 flex-grow flex flex-col justify-between">
        <div class="space-y-3">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-clinic-teal">
                    {{ $article->category->name }}
                </span>
                <span>{{ $article->created_at->format('d/m/Y') }}</span>
            </div>
            
            <h3 class="text-base font-extrabold text-slate-900 group-hover:text-clinic-blue leading-snug tracking-tight transition-colors line-clamp-2">
                <a href="{{ $article->public_url }}">
                    {{ $article->title }}
                </a>
            </h3>

            <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed">
                {{ html_entity_decode($article->meta_description ?? Str::limit(html_entity_decode(strip_tags($article->content), ENT_QUOTES, 'UTF-8'), 120), ENT_QUOTES, 'UTF-8') }}
            </p>
        </div>

        <div class="pt-4 border-t border-slate-50 mt-4 flex items-center justify-end">
            <a href="{{ $article->public_url }}" class="inline-flex items-center text-xs font-extrabold text-clinic-teal hover:text-clinic-blue transition-colors">
                <span>Chi tiết</span>
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
