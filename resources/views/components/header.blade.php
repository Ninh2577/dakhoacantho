@php
    $categories = \App\Models\Category::whereNull('parent_id')->where('name', '!=', 'Chưa được phân loại')->with('children')->get();
@endphp

<!-- Emergency Hotline Banner (Always visible at top) -->
<div class="bg-clinic-blue text-white text-xs md:text-sm font-semibold py-2 px-4 text-center tracking-wide shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
        <svg class="w-4 h-4 animate-pulse text-red-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
        </svg>
        <span>HOTLINE CẤP CỨU 24/7: <a href="tel:0933496986" class="underline hover:text-teal-300 font-extrabold">0933 49 69 86</a> — LUÔN SẴN SÀNG PHỤC VỤ</span>
    </div>
</div>

<!-- Header / Navbar -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="p-2 bg-gradient-to-br from-clinic-sky to-clinic-blue text-white rounded-xl shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </span>
                    <div class="leading-tight">
                        <span class="block text-lg font-black text-clinic-blue tracking-tight">Đa Khoa Gia Phước</span>
                        <span class="block text-[9px] uppercase font-bold text-clinic-teal tracking-widest">Medical Clinic</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8 text-sm font-bold text-slate-600">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-clinic-blue' : 'hover:text-clinic-blue' }} transition-colors">Trang chủ</a>
                
                <!-- Dynamic Category Dropdown -->
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button class="flex items-center gap-1 hover:text-clinic-blue transition-colors focus:outline-none py-4">
                        <span>Chuyên khoa</span>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="absolute left-0 mt-0 w-64 rounded-xl bg-white border border-slate-100 shadow-lg py-2 z-50">
                        @foreach($categories as $category)
                            <div class="relative group/sub" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
                                <a href="{{ url('category/' . $category->full_path) }}" class="flex items-center justify-between px-4 py-2.5 text-slate-700 hover:bg-slate-50 hover:text-clinic-blue transition-colors rounded-lg mx-2">
                                    <span>{{ $category->name }}</span>
                                    @if($category->children->isNotEmpty())
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover/sub:text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                                @if($category->children->isNotEmpty())
                                    <div x-show="subOpen" x-cloak x-transition class="absolute left-full top-0 ml-1 w-64 rounded-xl bg-white border border-slate-100 shadow-lg py-2 z-50">
                                        @foreach($category->children as $child)
                                            <a href="{{ url('category/' . $child->full_path) }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-clinic-blue transition-colors rounded-lg mx-2">
                                                {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'text-clinic-blue' : 'hover:text-clinic-blue' }} transition-colors">Liên hệ</a>
            </nav>

            <!-- Header Actions (Desktop) -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-extrabold text-white bg-clinic-blue hover:bg-opacity-90 rounded-lg shadow-md transition-all">
                    ĐẶT LỊCH KHÁM
                </a>
            </div>

            <!-- Hamburger Menu Button (Mobile) -->
            <div class="flex items-center md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-clinic-blue focus:outline-none p-2" aria-label="Toggle Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Navigation Menu Dropdown -->
    <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden border-t border-slate-100 bg-white shadow-inner">
        <div class="px-2 pt-2 pb-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Trang chủ</a>
            
            <!-- Mobile Accordion -->
            <div x-data="{ catOpen: false }">
                <button @click="catOpen = !catOpen" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue focus:outline-none">
                    <span>Chuyên khoa</span>
                    <svg class="w-5 h-5 transition-transform" :class="catOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="catOpen" x-cloak class="pl-4 space-y-1 bg-slate-50 rounded-xl p-2 mt-1">
                    @foreach($categories as $category)
                        <div x-data="{ childOpen: false }">
                            <div class="flex items-center justify-between pr-2">
                                <a href="{{ url('category/' . $category->full_path) }}" class="block py-2 text-sm font-bold text-slate-600 hover:text-clinic-blue">
                                    {{ $category->name }}
                                </a>
                                @if($category->children->isNotEmpty())
                                    <button @click="childOpen = !childOpen" class="p-2 text-slate-400 focus:outline-none">
                                        <svg class="w-4 h-4 transition-transform" :class="childOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            @if($category->children->isNotEmpty())
                                <div x-show="childOpen" x-cloak class="pl-4 border-l border-slate-200 ml-2 space-y-1">
                                    @foreach($category->children as $child)
                                        <a href="{{ url('category/' . $child->full_path) }}" class="block py-2 text-xs font-semibold text-slate-500 hover:text-clinic-blue">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            
            <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Liên hệ</a>
            
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between px-3">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-extrabold text-white bg-clinic-blue rounded-lg">
                    Đặt Lịch Khám
                </a>
            </div>
        </div>
    </div>
</header>
