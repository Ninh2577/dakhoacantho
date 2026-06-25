<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Phòng Khám Đa Khoa Cần Thơ')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen selection:bg-clinic-teal selection:text-white overflow-x-hidden">

    <x-header />

    <!-- Main Content Area -->
    <main class="flex-grow pb-16 md:pb-0">
        @yield('content')
    </main>

    <x-footer />

    @livewireScripts

    @php
        $_phoneRaw     = \App\Models\Setting::site('hotline');
        $_phoneDisplay = $_phoneRaw;
        $_phoneHref    = 'tel:' . preg_replace('/\D/', '', $_phoneRaw);
        $_zaloUrl      = \App\Models\Setting::site('zalo_url');
        $_facebookUrl  = \App\Models\Setting::site('facebook_url');
    @endphp

    {{-- ===== Floating Contact Group — Bottom LEFT (fixed to viewport) ===== --}}
    {{-- Custom sizing and styles for the bottom-left floating buttons --}}
    <style>
        .gp-floating-btn {
            width: 52px !important;
            height: 52px !important;
        }
        .gp-floating-btn .gp-icon-call {
            width: 22px !important;
            height: 22px !important;
        }
        .gp-floating-btn .gp-icon-social {
            width: 26px !important;
            height: 26px !important;
        }
        @media (min-width: 768px) {
            .gp-floating-btn {
                width: 60px !important;
                height: 60px !important;
            }
            .gp-floating-btn .gp-icon-call {
                width: 26px !important;
                height: 26px !important;
            }
            .gp-floating-btn .gp-icon-social {
                width: 30px !important;
                height: 30px !important;
            }
        }
    </style>

    <div class="pointer-events-none fixed bottom-6 left-4 z-[999] flex flex-col-reverse gap-3 md:bottom-6 md:left-6"
         role="group"
         aria-label="Liên hệ nhanh">

        {{-- Call Button --}}
        <a href="{{ $_phoneHref }}"
           aria-label="Gọi điện tư vấn {{ $_phoneDisplay }}"
           class="pointer-events-auto gp-floating-btn group relative inline-flex items-center justify-center rounded-full
                  ring-2 ring-white ring-offset-1
                  text-white
                  transition-all duration-300
                  hover:scale-110
                  focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
           style="background:#E53935; box-shadow:0 6px 20px rgba(229,57,53,0.5); animation:gpFloat 3s ease-in-out infinite;"
           onmouseenter="this.style.background='#C62828'" onmouseleave="this.style.background='#E53935'">
            <svg class="gp-icon-call" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span class="pointer-events-none absolute left-full ml-3 whitespace-nowrap rounded-lg bg-slate-900/90 px-3 py-1.5
                         text-xs font-semibold text-white opacity-0 shadow-lg backdrop-blur-sm
                         transition-opacity duration-200 group-hover:opacity-100 hidden md:block">
                {{ $_phoneDisplay }}
            </span>
        </a>

        {{-- Zalo Button --}}
        <a href="{{ $_zaloUrl }}"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Chat Zalo"
           class="pointer-events-auto gp-floating-btn group relative inline-flex items-center justify-center rounded-full
                  ring-2 ring-white ring-offset-1
                  text-white
                  transition-all duration-300
                  hover:scale-110
                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
           style="background:#0068FF; box-shadow:0 6px 20px rgba(0,104,255,0.5); animation:gpFloat 3s ease-in-out 0.4s infinite;"
           onmouseenter="this.style.background='#0052CC'" onmouseleave="this.style.background='#0068FF'">
            <svg class="gp-icon-social" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <g>
                    {{-- White message bubble base --}}
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="white"/>
                    {{-- Blue letters inside matching Zalo brand background (#0068FF) --}}
                    <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"/>
                    <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"/>
                    <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"/>
                    <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"/>
                    <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"/>
                </g>
            </svg>
            <span class="pointer-events-none absolute left-full ml-3 whitespace-nowrap rounded-lg bg-slate-900/90 px-3 py-1.5
                         text-xs font-semibold text-white opacity-0 shadow-lg backdrop-blur-sm
                         transition-opacity duration-200 group-hover:opacity-100 hidden md:block">
                Chat Zalo
            </span>
        </a>

    </div>

    {{-- Cần Thơ Live Chat Widget - loaded once globally, async to avoid render blocking --}}
    <script src="https://chat.dakhoagiaphuoc.vn/embed.js"
            data-site-id="app.dakhoacantho.com"
            data-site-name="App Đa Khoa Cần Thơ"
            async>
    </script>
</body>
</html>
