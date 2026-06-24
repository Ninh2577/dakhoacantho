@php
    $clinicName = \App\Models\Setting::site('clinic_short_name');
    $clinicFullName = \App\Models\Setting::site('clinic_name');
    $phoneDisplay = \App\Models\Setting::site('hotline');
    $phoneHref = 'tel:' . preg_replace('/\D/', '', $phoneDisplay);
    $address = \App\Models\Setting::site('address');
    $email = \App\Models\Setting::site('email');
    $emailHref = 'mailto:' . $email;
    $directionUrl  = \App\Models\Setting::site('google_maps_url');
    $mapEmbedUrl   = \App\Models\Setting::site('google_maps_embed_url');
    $facebookUrl   = \App\Models\Setting::site('facebook_url');
    $zaloUrl       = \App\Models\Setting::site('zalo_url');

    $serviceLabels = [
        'nam-khoa' => 'Nam khoa',
        'phu-khoa' => 'Phụ khoa',
        'ngoai-khoa' => 'Ngoại khoa',
        'benh-xa-hoi' => 'Bệnh xã hội',
        'xet-nghiem' => 'Xét nghiệm',
    ];

    $serviceCategories = \Illuminate\Support\Facades\Cache::remember('dakhoacantho:footer:categories', now()->addHours(24), function () use ($serviceLabels) {
        return \App\Models\Category::query()
            ->whereIn('slug', array_keys($serviceLabels))
            ->get()
            ->keyBy('slug');
    });

    $serviceLinks = collect($serviceLabels)
        ->map(function ($label, $slug) use ($serviceCategories) {
            $category = $serviceCategories->get($slug);

            if (! $category) {
                return null;
            }

            return [
                'label' => $label,
                'url' => $category->public_url,
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
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
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

                        {{-- Google Maps Iframe nhúng trực tiếp --}}
                        <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 shadow-lg">
                            <iframe
                                src="{{ $mapEmbedUrl }}"
                                width="100%"
                                height="260"
                                style="border:0; display:block;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Bản đồ {{ $clinicName }}"
                                aria-label="Bản đồ vị trí {{ $clinicFullName }}"
                            ></iframe>
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


    {{-- Keyframe animation for gentle float effect (shared with app.blade.php) --}}
    <style>
        @keyframes gpFloat {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-5px); }
        }
    </style>

</footer>
