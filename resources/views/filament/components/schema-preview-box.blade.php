@php
    $siteUrl = url('/');
    
    $phoneRaw = $data['hotline'] ?? '';
    $phoneCleaned = preg_replace('/\D/', '', $phoneRaw);
    $phoneE164 = str_starts_with($phoneCleaned, '0') ? '+84' . substr($phoneCleaned, 1) : '+' . $phoneCleaned;

    $fullAddress = $data['address'] ?? '';
    $parts = array_map('trim', explode(',', $fullAddress));
    $streetAddress = $parts[0] ?? $fullAddress;
    $addressLocality = $parts[1] ?? 'Ninh Kiều';
    $addressRegion = $parts[2] ?? 'Cần Thơ';

    $workingHours = $data['working_hours'] ?? '';
    $opens = '07:30';
    $closes = '20:00';
    if (preg_match('/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', $workingHours, $matches)) {
        $opens = $matches[1];
        $closes = $matches[2];
    }

    $sameAs = array_values(array_filter([
        $data['facebook_url'] ?? null,
        $data['zalo_url'] ?? null,
        $data['youtube_url'] ?? null,
        $data['tiktok_url'] ?? null,
    ]));

    $json = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'MedicalBusiness',
                '@id' => $siteUrl.'/#organization',
                'name' => $data['clinic_name'] ?? '',
                'alternateName' => $data['clinic_short_name'] ?? '',
                'url' => $siteUrl,
                'logo' => $data['logo_url'] ?? asset('images/doctor.webp'),
                'image' => $data['logo_url'] ?? asset('images/doctor.webp'),
                'description' => $data['site_description'] ?? 'Chia sẻ các tin tức sức khỏe - tư vấn và đưa ra những kiến thức bổ ích...',
                'telephone' => $phoneE164,
                'email' => $data['email'] ?? '',
                'priceRange' => $data['price_range'] ?? '$$',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $streetAddress,
                    'addressLocality' => $addressLocality,
                    'addressRegion' => $addressRegion,
                    'postalCode' => '900000',
                    'addressCountry' => 'VietNam',
                ],
                'openingHoursSpecification' => [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => [
                        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
                    ],
                    'opens' => $opens,
                    'closes' => $closes,
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $data['latitude'] ?? '',
                    'longitude' => $data['longitude'] ?? '',
                ],
                'sameAs' => $sameAs,
                'areaServed' => $addressRegion,
            ]
        ]
    ];
@endphp

<div class="space-y-2">
    <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400">
        <span>JSON-LD @graph (Schema.org)</span>
        <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-500 font-mono text-[10px]">LIVESTREAM</span>
    </div>
    <pre class="bg-gray-950 text-emerald-400 p-4 rounded-xl overflow-x-auto text-[11px] font-mono border border-gray-800 shadow-inner max-h-[460px] min-h-[400px] select-all leading-relaxed whitespace-pre-wrap break-all">
{!! json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </pre>
    <div class="text-[10px] text-gray-400">
        💡 Mẹo: Nhấp đúp hoặc kéo chuột để sao chép nhanh đoạn mã Schema.
    </div>
</div>
