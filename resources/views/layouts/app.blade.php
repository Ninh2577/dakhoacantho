<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Phòng Khám Đa Khoa Gia Phước - Cần Thơ')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen selection:bg-clinic-teal selection:text-white" x-data="{ mobileMenuOpen: false }">

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
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm">
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
                    <a href="{{ route('category.show') }}" class="{{ request()->routeIs('category.show') ? 'text-clinic-blue' : 'hover:text-clinic-blue' }} transition-colors">Chuyên khoa</a>
                    <a href="#" class="hover:text-clinic-blue transition-colors">Tin tức</a>
                    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'text-clinic-blue' : 'hover:text-clinic-blue' }} transition-colors">Liên hệ</a>
                </nav>

                <!-- Header Actions (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-clinic-blue uppercase">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a3 3 0 003-3V6.7m0 0l-3-3m3 3H9"></path>
                            </svg>
                            <span>English</span>
                        </button>
                    </div>
                    
                    <!-- Call To Action -->
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
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-slate-100 bg-white shadow-inner">
            <div class="px-2 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Trang chủ</a>
                <a href="{{ route('category.show') }}" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Chuyên khoa</a>
                <a href="#" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Tin tức</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2.5 rounded-lg text-base font-bold text-slate-700 hover:bg-slate-50 hover:text-clinic-blue">Liên hệ</a>
                
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between px-3">
                    <span class="text-sm font-semibold text-slate-500">Ngôn ngữ: English</span>
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-extrabold text-white bg-clinic-blue rounded-lg">
                        Đặt Lịch Khám
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow pb-16 md:pb-0">
        @yield('content')
    </main>

    <!-- Fixed Bottom Navigation Bar for Mobile (Matches Mockups) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-100 shadow-lg py-2">
        <div class="grid grid-cols-4 text-center">
            <a href="{{ route('contact') }}" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-[10px] font-bold">Đặt hẹn</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span class="text-[10px] font-bold">Chat</span>
            </a>
            <a href="tel:0933496986" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <span class="text-[10px] font-bold">Gọi điện</span>
            </a>
            <a href="{{ route('contact') }}" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-[10px] font-bold">Vị trí</span>
            </a>
        </div>
    </div>

    <!-- Global Footer -->
    <footer class="bg-[#242b35] text-slate-400 border-t border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
                
                <!-- Column 1: Identity & General Contacts -->
                <div class="space-y-4">
                    <h3 class="text-white text-lg font-black tracking-tight">Da Khoa Gia Phước</h3>
                    <p class="text-sm leading-relaxed">
                        Địa chỉ y tế tin cậy tại miền Tây với dịch vụ tận tâm và chuyên nghiệp nhất.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="text-slate-400 hover:text-white transition-colors" aria-label="Social Link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-slate-400 hover:text-white transition-colors" aria-label="Social Link">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12.713l-11.985-9.713h23.97l-11.985 9.713zm0 2.574l12-9.725v15.438h-24v-15.438l12 9.725z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Quick Links (Chuyên Khoa) -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Về Chúng Tôi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Giới thiệu</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Đội ngũ bác sĩ</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Cơ sở vật chất</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hợp tác quốc tế</a></li>
                    </ul>
                </div>

                <!-- Column 3: Corporate Policies -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Chính Sách</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Sơ đồ website</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Quy trình khám bệnh</a></li>
                    </ul>
                </div>

                <!-- Column 4: Specific Location details -->
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Liên Hệ</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-clinic-teal flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>57 Hùng Vương, P. Thới Bình, Q. Ninh Kiều, TP. Cần Thơ</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="font-extrabold text-white">0933 49 69 86</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>07:30 - 20:00 (Hàng ngày)</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-700 mt-12 pt-8 text-center text-xs space-y-2">
                <p>Copyright &copy; {{ date('Y') }} Đa Khoa Cần Thơ. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
