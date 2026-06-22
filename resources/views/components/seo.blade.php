@props([
    'title',
    'description',
    'canonical',
    'ogType' => 'website',
    'breadcrumbs' => [],
    'faqs' => [],
    'pageType' => 'website',
    'article' => null,
    'category' => null,
    'image' => null,
    'publishedAt' => null,
    'modifiedAt' => null,
    // Per-page OG / Twitter overrides (filled from article's own og_* fields)
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => null,
    'twitterTitle' => null,
    'twitterDescription' => null,
    'twitterImage' => null,
    // Article schema_type: Article | BlogPosting | MedicalWebPage | None
    'schemaType' => 'Article',
])

@php
    // Resolve absolute canonical URL
    $absoluteCanonical = $canonical;
    if (!preg_match('/^https?:\/\//', $absoluteCanonical)) {
        $absoluteCanonical = url($absoluteCanonical);
    }

    // Fallback image chain
    $metaImage = $ogImage ?? $image ?? asset('images/doctor.webp');
    if (!preg_match('/^https?:\/\//', $metaImage)) {
        $metaImage = asset($metaImage);
    }

    $twitterImageFinal = $twitterImage ?? $metaImage;
    if (!preg_match('/^https?:\/\//', $twitterImageFinal)) {
        $twitterImageFinal = asset($twitterImageFinal);
    }

    // OG / Twitter title & description fallbacks
    $resolvedOgTitle  = $ogTitle       ?: $title;
    $resolvedOgDesc   = $ogDescription ?: $description;
    $resolvedTwTitle  = $twitterTitle  ?: $resolvedOgTitle;
    $resolvedTwDesc   = $twitterDescription ?: $resolvedOgDesc;

    $ogTypeFinal = ($pageType === 'article') ? 'article' : 'website';
@endphp

<!-- Description & Canonical -->
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $absoluteCanonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $ogTypeFinal }}">
<meta property="og:url" content="{{ $absoluteCanonical }}">
<meta property="og:title" content="{{ $resolvedOgTitle }}">
<meta property="og:description" content="{{ $resolvedOgDesc }}">
<meta property="og:site_name" content="{{ \App\Models\Setting::site('clinic_name') }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="vi_VN">
@if($publishedAt)
<meta property="article:published_time" content="{{ $publishedAt instanceof \DateTimeInterface ? $publishedAt->format('c') : $publishedAt }}">
@endif
@if($modifiedAt)
<meta property="article:modified_time" content="{{ $modifiedAt instanceof \DateTimeInterface ? $modifiedAt->format('c') : $modifiedAt }}">
@endif

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $absoluteCanonical }}">
<meta name="twitter:title" content="{{ $resolvedTwTitle }}">
<meta name="twitter:description" content="{{ $resolvedTwDesc }}">
<meta name="twitter:image" content="{{ $twitterImageFinal }}">

<!-- Unified Schema JSON-LD Graph -->
<x-seo.schema-jsonld
    :page-type="$pageType"
    :title="$title"
    :description="$description"
    :url="$absoluteCanonical"
    :image="$metaImage"
    :breadcrumbs="$breadcrumbs"
    :article="$article"
    :faq-items="$faqs"
    :category="$category"
    :published-at="$publishedAt"
    :modified-at="$modifiedAt"
    :schema-type="$schemaType"
/>
