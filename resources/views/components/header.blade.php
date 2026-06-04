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
<header class="sticky top-0 z-50 shadow-lg" x-data="{ mobileMenuOpen: false }">

    <!-- Logo Bar (white background) -->
    <div class="bg-white/95 backdrop-blur-md border-b border-slate-100">
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

                <!-- Center Navigation Area (Redesigned Mega Menu) -->
                <nav class="hidden lg:flex items-center space-x-8 font-semibold text-gray-800">
                    <a href="{{ url('/') }}" class="hover:text-blue-600 transition duration-200">Trang chủ</a>

                    @if(isset($mainCategories))
                        @foreach($mainCategories as $category)
                            @if($category->children->isNotEmpty())
                                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">

                                    {{-- Nav trigger --}}
                                    <a href="{{ url('/category/' . $category->full_path) }}"
                                       class="flex items-center gap-1 transition duration-200 py-6 font-semibold relative"
                                       :class="open ? 'text-blue-600' : 'text-gray-800 hover:text-blue-600'">
                                        {{ $category->name }}
                                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-blue-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                        <span x-show="open" class="absolute bottom-0 left-0 w-full bg-blue-600 rounded-t" style="height:3px;display:none;"></span>
                                    </a>

                                    {{-- Dropdown panel — widths fully inline to bypass any Tailwind compile issues --}}
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 translate-y-0"
                                         x-transition:leave-end="opacity-0 translate-y-2"
                                         class="absolute top-full z-50 bg-white rounded-2xl shadow-2xl border border-slate-200"
                                         style="display:none; width:900px; min-width:900px; left:50%; transform:translateX(-50%);">

                                        {{-- Caret --}}
                                        <div class="absolute bg-white border-t border-l border-slate-200 rotate-45 rounded-sm z-10"
                                             style="width:14px;height:14px;top:-7px;left:50%;transform:translateX(-50%);"></div>

                                        {{-- Main flex row — inline widths ensure no shrinking --}}
                                        <div class="relative z-20 rounded-2xl bg-white"
                                             style="display:flex; flex-direction:row; overflow:hidden;">

                                            {{-- LEFT: 2-col subcategory grid --}}
                                            <div style="width:640px; flex-shrink:0; padding:2rem; display:grid; grid-template-columns:1fr 1fr; gap:1.75rem 2.5rem; align-content:start;">
                                                @foreach($category->children as $child)
                                                    <div style="display:flex; flex-direction:column;">
                                                        <a href="{{ url('/category/' . $child->full_path) }}"
                                                           class="group/cat hover:text-blue-600 transition"
                                                           style="display:flex; align-items:center; gap:10px; margin-bottom:10px; text-decoration:none;">
                                                            <span class="bg-blue-50 border-2 border-blue-200 rounded-full flex items-center justify-center group-hover/cat:bg-blue-100 group-hover/cat:border-blue-500 transition"
                                                                  style="width:36px;height:36px;flex-shrink:0;">
                                                                <svg class="text-blue-500" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                            </span>
                                                            <span class="font-bold text-slate-800 group-hover/cat:text-blue-600 transition" style="font-size:14.5px; line-height:1.3;">{{ $child->name }}</span>
                                                        </a>
                                                        @if($child->children->isNotEmpty())
                                                            <ul style="margin-left:46px; display:flex; flex-direction:column; gap:7px;">
                                                                @foreach($child->children as $subChild)
                                                                    <li>
                                                                        <a href="{{ url('/category/' . $subChild->full_path) }}"
                                                                           class="text-slate-500 hover:text-blue-600 transition-colors"
                                                                           style="font-size:13px; line-height:1.4; display:block;"
                                                                           title="{{ $subChild->name }}">{{ $subChild->name }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- RIGHT: featured card --}}
                                            <div class="bg-[#ebf5ff] border-l border-blue-100"
                                                 style="width:260px; flex-shrink:0; display:flex; flex-direction:column; align-items:center; text-align:center; padding:1.5rem 1.25rem 1.75rem;">
                                                @if($category->featured_image)
                                                    <img src="{{ asset('storage/' . $category->featured_image) }}"
                                                         alt="{{ $category->name }}"
                                                         style="width:100%;height:176px;object-fit:cover;object-position:top;border-radius:12px;margin-bottom:1rem;box-shadow:0 1px 4px rgba(0,0,0,.1);">
                                                @else
                                                    <div class="bg-blue-100 rounded-xl flex items-center justify-center mb-4"
                                                         style="width:100%;height:176px;">
                                                        <svg class="text-blue-300" style="width:56px;height:56px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </div>
                                                @endif
                                                <h4 class="font-bold text-slate-800" style="font-size:15px;margin-bottom:6px;">Highlight</h4>
                                                <p class="text-slate-500" style="font-size:12px;line-height:1.6;margin-bottom:1.25rem;">Đội ngũ y bác sĩ chuyên khoa hàng đầu sẵn sàng hỗ trợ và tư vấn cho bạn.</p>
                                                <a href="{{ url('/category/' . $category->full_path) }}"
                                                   class="inline-block bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition shadow-sm"
                                                   style="font-size:13.5px;padding:8px 24px;">Xem chi tiết</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ url('/category/' . $category->full_path) }}" class="hover:text-blue-600 transition duration-200 py-6">{{ $category->name }}</a>
                            @endif
                        @endforeach
                    @endif

                    <a href="{{ url('/lien-he') }}" class="hover:text-blue-600 transition duration-200 py-6">Liên hệ</a>
                </nav>

                <!-- Header Actions (Desktop) -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-extrabold text-white bg-clinic-blue hover:bg-opacity-90 rounded-lg shadow-md transition-all">
                        ĐẶT LỊCH KHÁM
                    </a>
                </div>

                <!-- Hamburger Menu Button (Mobile) -->
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-clinic-blue focus:outline-none p-2" aria-label="Toggle Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ===== MOBILE NAVIGATION (3-level accordion) ===== --}}
    <nav class="lg:hidden bg-gray-900" x-show="mobileMenuOpen" x-cloak x-transition style="display: none;">
        <ul class="flex flex-col">

            <li>
                <a href="{{ url('/') }}"
                   class="flex items-center gap-2 px-4 py-3 text-white font-bold uppercase border-b border-gray-700 hover:bg-blue-600 transition-colors {{ request()->routeIs('home') ? 'bg-blue-700' : '' }}">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v6a1 1 0 001 1h4v-4h2v4h4a1 1 0 001-1v-6h1a1 1 0 00.707-1.707l-7-7z"/>
                    </svg>
                    Trang Chủ
                </a>
            </li>

            <li>
                <a href="{{ route('categories.index') }}"
                   class="block px-4 py-3 text-white font-bold uppercase border-b border-gray-700 hover:bg-blue-600 transition-colors {{ request()->routeIs('categories.index') ? 'bg-blue-700' : '' }}">
                    Chuyên Khoa
                </a>
            </li>

            @foreach($mainCategories as $category)
                @if($category->children->isNotEmpty())
                    {{-- Mobile L1 accordion --}}
                    <li x-data="{ openL1: false }">
                        <button @click="openL1 = !openL1"
                                class="w-full flex items-center justify-between px-4 py-3 text-white font-bold uppercase border-b border-gray-700 hover:bg-blue-600 transition-colors focus:outline-none">
                            <a href="{{ url('/category/' . $category->full_path) }}" class="flex-1 text-left" @click.stop>
                                {{ $category->name }}
                            </a>
                            <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="openL1 ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <ul x-show="openL1" x-transition class="bg-gray-800" style="display: none;">
                            @foreach($category->children as $child)
                                @if($child->children->isNotEmpty())
                                    {{-- Mobile L2 accordion --}}
                                    <li x-data="{ openL2: false }">
                                        <button @click="openL2 = !openL2"
                                                class="w-full flex items-center justify-between pl-6 pr-4 py-3 text-gray-200 text-sm border-b border-gray-700 hover:bg-blue-600 transition-colors focus:outline-none">
                                            <a href="{{ url('/category/' . $child->full_path) }}" class="flex-1 text-left" @click.stop>
                                                {{ $child->name }}
                                            </a>
                                            <svg class="w-3 h-3 flex-shrink-0 transition-transform" :class="openL2 ? 'rotate-180' : ''"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <ul x-show="openL2" x-transition class="bg-gray-700" style="display: none;">
                                            @foreach($child->children as $grandchild)
                                                <li>
                                                    <a href="{{ url('/category/' . $grandchild->full_path) }}"
                                                       class="block pl-10 pr-4 py-2 text-gray-300 text-sm border-b border-gray-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                        {{ $grandchild->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ url('/category/' . $child->full_path) }}"
                                           class="block pl-6 pr-4 py-3 text-gray-200 text-sm border-b border-gray-700 hover:bg-blue-600 transition-colors">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </li>
                @else
                    <li>
                        <a href="{{ url('/category/' . $category->full_path) }}"
                           class="block px-4 py-3 text-white font-bold uppercase border-b border-gray-700 hover:bg-blue-600 transition-colors">
                            {{ $category->name }}
                        </a>
                    </li>
                @endif
            @endforeach

            {{-- Liên hệ --}}
            <li>
                <a href="{{ route('contact') }}"
                   class="block px-4 py-3 text-white font-bold uppercase border-b border-gray-700 hover:bg-blue-600 transition-colors {{ request()->routeIs('contact') ? 'bg-blue-700' : '' }}">
                    Liên hệ
                </a>
            </li>

            {{-- Mobile CTA --}}
            <li class="px-4 py-4">
                <a href="{{ route('contact') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-extrabold text-white bg-clinic-blue rounded-lg hover:bg-opacity-90 transition-all">
                    Đặt Lịch Khám
                </a>
            </li>

        </ul>
    </nav>

</header>
