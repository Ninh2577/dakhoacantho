@props(['title', 'description', 'canonical', 'ogType' => 'website', 'breadcrumbs' => [], 'faqs' => []])

<!-- Description & Canonical -->
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:site_name" content="Phòng Khám Đa Khoa Gia Phước">
<meta property="og:image" content="{{ asset('images/doctor.webp') }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonical }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ asset('images/doctor.webp') }}">

<!-- MedicalClinic / LocalBusiness JSON-LD Schema (Global) -->
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "MedicalClinic",
  "name": "Phòng Khám Đa Khoa Gia Phước",
  "telephone": "0966.332.352",
  "url": "{{ url('/') }}",
  "logo": "{{ asset('images/doctor.webp') }}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Số 57 Hùng Vương",
    "addressLocality": "Ninh Kiều",
    "addressRegion": "Cần Thơ",
    "addressCountry": "VN"
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "07:30",
    "closes": "20:00"
  }
}
</script>

<!-- BreadcrumbList JSON-LD Schema -->
@if(!empty($breadcrumbs))
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    @foreach($breadcrumbs as $index => $crumb)
    {
      "@type": "ListItem",
      "position": {{ $index + 1 }},
      "name": "{{ $crumb['name'] }}",
      "item": "{{ $crumb['url'] }}"
    }{{ $index < count($breadcrumbs) - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endif

<!-- FAQPage JSON-LD Schema -->
@if(!empty($faqs))
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $index => $faq)
    {
      "@type": "Question",
      "name": "{{ $faq['q'] }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ $faq['a'] }}"
      }
    }{{ $index < count($faqs) - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endif
