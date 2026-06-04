@props(['title', 'description', 'slug', 'bgImage', 'icon'])

<div class="relative overflow-hidden rounded-2xl aspect-[4/3] group shadow-md hover:shadow-xl transition-all duration-500">
    <!-- Background Image -->
    <div class="absolute inset-0 bg-slate-900">
        <img src="{{ $bgImage }}" alt="{{ $title }}" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-700">
    </div>

    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

    <!-- Card Details -->
    <div class="absolute inset-0 p-6 flex flex-col justify-end text-white">
        <div class="space-y-2">
            <!-- Icon -->
            <div class="inline-flex items-center justify-center p-2.5 bg-white/20 backdrop-blur-md rounded-xl mb-2">
                {!! $icon !!}
            </div>

            <h3 class="text-lg md:text-xl font-extrabold tracking-tight group-hover:text-teal-300 transition-colors">
                {{ $title }}
            </h3>
            
            <p class="text-xs md:text-sm text-slate-200 line-clamp-2 leading-relaxed font-medium">
                {{ $description }}
            </p>
        </div>

        <!-- Read more indicator -->
        <div class="pt-4 mt-2 flex items-center justify-start border-t border-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <a href="{{ route('category.show', ['category_path' => $slug]) }}" class="inline-flex items-center text-xs font-bold text-teal-300 hover:underline">
                <span>Tìm hiểu chuyên khoa</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
