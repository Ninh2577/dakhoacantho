@php
    $clinicName = 'Đa Khoa Gia Phước';
    $clinicFullName = 'Phòng Khám Đa Khoa Gia Phước';
    $phoneDisplay = '0966.332.352';
    $phoneHref = 'tel:0966332352';
    $address = '57 Hùng Vương, P. Ninh Kiều, TP. Cần Thơ';
    $email = 'lienhe@dakhoagiaphuoc.vn';
    $emailHref = 'mailto:lienhe@dakhoagiaphuoc.vn';
    $directionUrl = 'https://www.google.com/maps/search/?api=1&query=57%20Hùng%20Vương%2C%20P.%20Ninh%20Kiều%2C%20TP.%20Cần%20Thơ';
    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode(url('/'));
    $zaloUrl = 'https://zalo.me/share?url=' . urlencode(url('/'));

    $serviceLabels = [
        'nam-khoa' => 'Nam khoa',
        'phu-khoa' => 'Phụ khoa',
        'ngoai-khoa' => 'Ngoại khoa',
        'benh-xa-hoi' => 'Bệnh xã hội',
        'xet-nghiem' => 'Xét nghiệm',
    ];

    $serviceCategories = \App\Models\Category::query()
        ->whereIn('slug', array_keys($serviceLabels))
        ->get()
        ->keyBy('slug');

    $serviceLinks = collect($serviceLabels)
        ->map(function ($label, $slug) use ($serviceCategories) {
            $category = $serviceCategories->get($slug);

            if (! $category) {
                return null;
            }

            return [
                'label' => $label,
                'url' => url('/category/' . $category->full_path),
            ];
        })
        ->filter()
        ->values();

    $supportLinks = array_filter([
        [
            'label' => 'Giới thiệu',
            'url' => route('home') . '#gioi-thieu',
        ],
        \Illuminate\Support\Facades\Route::has('terms.policy') ? [
            'label' => 'Điều khoản sử dụng',
            'url' => route('terms.policy'),
        ] : null,
        \Illuminate\Support\Facades\Route::has('privacy.policy') ? [
            'label' => 'Chính sách bảo mật',
            'url' => route('privacy.policy'),
        ] : null,
        [
            'label' => 'Sơ đồ website',
            'url' => url('/sitemap.xml'),
        ],
    ]);
@endphp

<footer class="w-full bg-[#343b3f] text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                    <section>
                        <h2 class="text-[2rem] font-black leading-tight tracking-tight text-white sm:text-[2.15rem] lg:text-[2.35rem]">{{ $clinicName }}</h2>
                        <p class="mt-5 max-w-xs text-[15px] font-semibold leading-8 text-slate-300">
                            Hệ thống y tế hiện đại, tận tâm phục vụ cộng đồng với quy trình rõ ràng và bảo mật thông tin.
                        </p>

                        <ul class="mt-8 space-y-4 text-[15px] font-semibold text-slate-100">
                            <li class="flex items-start gap-3">
                                <span class="mt-0.5 text-[#29b8ff]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <span class="max-w-[15rem] leading-7 text-slate-100">{{ $address }}</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#29b8ff]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </span>
                                <a href="{{ $phoneHref }}" class="transition hover:text-[#29b8ff]">{{ $phoneDisplay }}</a>
                            </li>
                            <li class="flex items-center gap-3">
                                <span class="text-[#29b8ff]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <a href="{{ $emailHref }}" class="break-all transition hover:text-[#29b8ff]">{{ $email }}</a>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-[1.7rem] font-black uppercase tracking-tight text-white">CHUYÊN KHOA</h2>
                        <ul class="mt-6 space-y-4 text-[15px] font-semibold text-slate-200">
                            @foreach ($serviceLinks as $link)
                                <li>
                                    <a href="{{ $link['url'] }}" class="group inline-flex items-center gap-2.5 transition hover:text-white">
                                        <span class="h-2 w-2 rounded-full bg-[#2db8ff]"></span>
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-[1.7rem] font-black uppercase tracking-tight text-white">HỖ TRỢ &amp; CHÍNH SÁCH</h2>
                        <ul class="mt-6 space-y-4 text-[15px] font-semibold text-slate-200">
                            @foreach ($supportLinks as $link)
                                <li>
                                    <a href="{{ $link['url'] }}" class="transition hover:text-white">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-[1.7rem] font-black uppercase tracking-tight text-white">LIÊN HỆ &amp; VỊ TRÍ</h2>

                        <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-[#566169]">
                            <div class="relative h-[108px] bg-gradient-to-br from-[#5a9fd4] via-[#4a8cc0] to-[#3a7aac]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 200%22><defs><pattern id=%22grid%22 width=%2240%22 height=%2240%22 patternUnits=%22userSpaceOnUse%22><path d=%22M 40 0 L 0 0 0 40%22 fill=%22none%22 stroke=%22rgba(255,255,255,0.1)%22 stroke-width=%220.5%22/></pattern></defs><rect width=%22400%22 height=%22200%22 fill=%22url(%23grid)%22/><circle cx=%22200%22 cy=%22100%22 r=%2230%22 fill=%22%23ff6b5b%22 opacity=%220.3%22/><path d=%22M 100 50 Q 150 80 200 100 T 300 120%22 stroke=%22rgba(255,255,255,0.15)%22 stroke-width=%221%22 fill=%22none%22/></svg>');">
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-transparent to-[rgba(137,168,195,0.3)]"></div>
                                <div class="absolute inset-y-0 right-0 w-24 bg-gradient-to-l from-[#89a8c3]/40 to-transparent"></div>
                                <div class="absolute left-4 top-1/2 -translate-y-1/2">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-100/80">YOUR HEALTH</p>
                                    <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-100/80">OUR PRIORITY</p>
                                </div>
                                <a href="{{ $directionUrl }}" target="_blank" rel="noopener noreferrer" class="absolute left-1/2 top-1/2 inline-flex -translate-x-1/2 -translate-y-1/2 items-center gap-2 rounded-full bg-[#0d58c8] px-5 py-2.5 text-sm font-extrabold text-white shadow-lg shadow-blue-950/25 transition hover:bg-[#0b4cb0] focus:outline-none focus:ring-2 focus:ring-[#2db8ff] focus:ring-offset-2 focus:ring-offset-[#343b3f]">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                    Chỉ đường
                                </a>
                            </div>
                        </div>

                        <div class="mt-5 flex items-start gap-3 text-[15px] font-semibold text-slate-200">
                            <span class="mt-0.5 text-[#2db8ff]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-black text-white">Giờ làm việc:</p>
                                <p class="mt-1 text-slate-300">07:30 - 20:00 (Tất cả các ngày)</p>
                            </div>
                        </div>
                    </section>
                </div>
        
        <div class="border-t border-white/10 pt-6 mt-6">
            <div class="flex flex-col gap-4 text-sm text-slate-300 lg:flex-row lg:items-center lg:justify-between">
                <p>Copyright © 2026 {{ $clinicName }}. All rights reserved.</p>

                <div class="flex flex-wrap items-center gap-6 lg:justify-end">
                    <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-semibold transition hover:text-white">
                        <svg class="h-5 w-5 text-[#9fdcff]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.6-1.6h1.7V4.8c-.3 0-1.3-.1-2.5-.1-2.4 0-4 1.4-4 4.1V11H8v3h2.3v8h3.2Z" />
                        </svg>
                        Facebook
                    </a>
                    <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 font-semibold transition hover:text-white">
                        <svg class="h-5 w-5 text-[#9fdcff]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M4 5.5h16v10.5H8l-4 3v-13.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.1" d="M8.5 9.5h7M8.5 12.5h4.5" />
                        </svg>
                        Zalo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="pointer-events-none fixed bottom-20 right-4 z-40 flex flex-col gap-3 md:bottom-6 md:right-6">
        <a href="{{ route('contact') }}" aria-label="Chat tư vấn" class="pointer-events-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#36b8f6] text-white shadow-[0_12px_24px_rgba(54,184,246,0.32)] transition hover:bg-[#1eaaf0] focus:outline-none focus:ring-2 focus:ring-[#7ad7ff] focus:ring-offset-2 focus:ring-offset-[#343b3f] md:h-16 md:w-16">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m8-2c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </a>
        <a href="{{ $phoneHref }}" aria-label="Gọi tư vấn" class="pointer-events-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#0d4ab0] text-white shadow-[0_12px_24px_rgba(13,74,176,0.32)] transition hover:bg-[#083f99] focus:outline-none focus:ring-2 focus:ring-[#7ad7ff] focus:ring-offset-2 focus:ring-offset-[#343b3f] md:h-16 md:w-16">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
        </a>
    </div>
</footer>
