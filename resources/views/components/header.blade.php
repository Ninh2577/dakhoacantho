@php
    $zaloUrl = \App\Models\Setting::site('zalo_url') ?: 'https://zalo.me/0966332352';
@endphp
<!-- Header / Navbar -->
<header class="sticky top-0 z-50 shadow-lg" x-data="{ mobileMenuOpen: false, searchOpen: false }">

    <!-- Logo Bar (white background) -->
    <div class="bg-white/95 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-[1440px] mx-auto px-3 lg:px-6 h-20 flex items-center justify-between">

                <!-- Logo -->
                <div class="flex-shrink-0 mr-1 lg:mr-2 xl:mr-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <img src="{{ asset('images/logo-icon.png') }}?v={{ filemtime(public_path('images/logo-icon.png')) }}" class="w-12 h-12 object-contain" alt="{{ \App\Models\Setting::site('clinic_short_name') }}">
                        <div class="leading-tight">
                            <span class="block text-base lg:text-lg font-black text-clinic-blue tracking-tight">{{ \App\Models\Setting::site('clinic_short_name') }}</span>
                        </div>
                    </a>
                </div>

                <!-- Center Navigation Area (Redesigned Mega Menu) -->
                <div class="hidden lg:flex flex-1 justify-center overflow-visible">
                    <nav class="flex items-center text-[12.5px] lg:text-[13px] xl:text-[14.5px] font-bold text-gray-800">
                    <a href="{{ url('/') }}" class="px-1.5 lg:px-2 xl:px-3 whitespace-nowrap transition duration-200 py-6 border-b-2 {{ request()->is('/') ? 'text-clinic-blue font-extrabold border-clinic-blue' : 'text-gray-800 hover:text-blue-600 border-transparent' }}">Trang chủ</a>

                    @if(isset($mainCategories))
                        @foreach($mainCategories as $category)
                            @if($category->children->isNotEmpty())
                                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">

                                    {{-- Nav trigger --}}
                                    <a href="{{ $category->public_url }}"
                                       class="px-1.5 lg:px-2 xl:px-3 flex items-center whitespace-nowrap gap-1 transition duration-200 py-6 font-semibold relative border-b-2 {{ request()->is(ltrim(str_replace(url('/'), '', $category->public_url), '/') . '*') ? 'text-clinic-blue font-extrabold border-clinic-blue' : 'text-gray-800 hover:text-blue-600 border-transparent' }}"
                                       :class="open ? 'text-blue-600' : ''">
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
                                                         <a href="{{ $child->public_url }}"
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
                                                                         <a href="{{ $subChild->public_url }}"
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
                                                 <p class="text-slate-500" style="font-size:12px;line-height:1.6;margin-bottom:1.25rem;">Đội ngũ tư vấn tận tâm luôn sẵn sàng hỗ trợ và giải đáp thắc mắc của bạn.</p>
                                                 <a href="{{ $category->public_url }}"
                                                    class="inline-block bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition shadow-sm"
                                                    style="font-size:13.5px;padding:8px 24px;">Xem chi tiết</a>
                                             </div>

                                         </div>
                                     </div>
                                 </div>
                             @else
                                 <a href="{{ $category->public_url }}" class="px-1.5 lg:px-2 xl:px-3 whitespace-nowrap transition duration-200 py-6 border-b-2 {{ request()->is(ltrim(str_replace(url('/'), '', $category->public_url), '/') . '*') ? 'text-clinic-blue font-extrabold border-clinic-blue' : 'text-gray-800 hover:text-blue-600 border-transparent' }}">{{ $category->name }}</a>
                             @endif
                         @endforeach
                     @endif

                     <a href="{{ url('/lien-he') }}" class="px-1.5 lg:px-2 xl:px-3 whitespace-nowrap transition duration-200 py-6 border-b-2 {{ request()->is('lien-he*') ? 'text-clinic-blue font-extrabold border-clinic-blue' : 'text-gray-800 hover:text-blue-600 border-transparent' }}">Liên hệ</a>
                     </nav>
                 </div>

                 <!-- Header Actions (Desktop) -->
                 <div class="flex-shrink-0 hidden lg:flex items-center ml-1 lg:ml-2 xl:ml-8 gap-2 xl:gap-4">
                     <button @click="searchOpen = true" class="text-slate-600 hover:text-clinic-blue transition p-2 cursor-pointer focus:outline-none" aria-label="Tìm kiếm">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                         </svg>
                     </button>
                      <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center whitespace-nowrap w-max px-3 lg:px-4 xl:px-6 py-2 text-xs lg:text-sm font-extrabold text-white bg-clinic-blue hover:bg-opacity-90 rounded-lg shadow-md transition-all">
                          ĐẶT LỊCH KHÁM
                      </a>
                 </div>

                 <!-- Mobile Actions -->
                 <div class="flex-shrink-0 flex items-center gap-2 lg:hidden">
                     <button @click="searchOpen = true" class="text-clinic-blue focus:outline-none p-2" aria-label="Tìm kiếm">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                         </svg>
                     </button>
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
                             <a href="{{ $category->public_url }}" class="flex-1 text-left" @click.stop>
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
                                             <a href="{{ $child->public_url }}" class="flex-1 text-left" @click.stop>
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
                                                     <a href="{{ $grandchild->public_url }}"
                                                        class="block pl-10 pr-4 py-2 text-gray-300 text-sm border-b border-gray-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                         {{ $grandchild->name }}
                                                     </a>
                                                 </li>
                                             @endforeach
                                         </ul>
                                     </li>
                                 @else
                                     <li>
                                          <a href="{{ $child->public_url }}"
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
                          <a href="{{ $category->public_url }}"
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
                 <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-extrabold text-white bg-clinic-blue rounded-lg hover:bg-opacity-90 transition-all">
                     Đặt Lịch Khám
                 </a>
             </li>

         </ul>
     </nav>

     <!-- Search Modal Overlay -->
     <div x-show="searchOpen" 
          class="fixed inset-0 z-50 overflow-y-auto" 
          style="display: none;" 
          @keydown.window.escape="searchOpen = false" 
          role="dialog" 
          aria-modal="true">
         <!-- Backdrop -->
         <div x-show="searchOpen" 
              x-transition:enter="ease-out duration-300"
              x-transition:enter-start="opacity-0"
              x-transition:enter-end="opacity-100"
              x-transition:leave="ease-in duration-200"
              x-transition:leave-start="opacity-100"
              x-transition:leave-end="opacity-0"
              @click="searchOpen = false" 
              class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

         <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

         <!-- Modal Box -->
         <div x-show="searchOpen" 
              x-transition:enter="ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
              x-transition:leave="ease-in duration-200"
              x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
              x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              class="relative inline-block w-full max-w-2xl px-6 py-8 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:p-8">
             
             <button @click="searchOpen = false" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 outline-none">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
             </button>

             <div class="space-y-6">
                 <h3 class="text-xl font-extrabold text-slate-900">Tìm kiếm thông tin y khoa</h3>
                 <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-4">
                     <div>
                         <label for="q" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Từ khóa (Tên bệnh, triệu chứng...)</label>
                         <input type="text" 
                                name="q" 
                                id="q" 
                                value="{{ request('q') }}" 
                                placeholder="Nhập từ khóa cần tìm..." 
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold">
                     </div>
                     
                     <div class="grid grid-cols-2 gap-4">
                         <div>
                             <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Từ ngày</label>
                             <input type="date" 
                                    name="start_date" 
                                    id="start_date" 
                                    value="{{ request('start_date') }}" 
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold text-slate-600">
                         </div>
                         <div>
                             <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Đến ngày</label>
                             <input type="date" 
                                    name="end_date" 
                                    id="end_date" 
                                    value="{{ request('end_date') }}" 
                                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:border-clinic-blue outline-none transition text-sm font-semibold text-slate-600">
                         </div>
                     </div>

                     <button type="submit" class="w-full px-6 py-3.5 mt-2 text-white bg-clinic-blue hover:bg-opacity-95 font-extrabold rounded-xl text-sm transition shadow-md shadow-clinic-blue/10">
                         Tìm kiếm
                     </button>
                 </form>
             </div>
         </div>
     </div>

 </header>
