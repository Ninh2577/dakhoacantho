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
])

@php
    // Resolve absolute URLs
    $absoluteCanonical = $canonical;
    if (!preg_match('/^https?:\/\//', $absoluteCanonical)) {
        $absoluteCanonical = url($absoluteCanonical);
    }
    $metaImage = $image ?? asset('images/doctor.webp');
    if (!preg_match('/^https?:\/\//', $metaImage)) {
        $metaImage = asset($metaImage);
    }
@endphp

<!-- Description & Canonical -->
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $absoluteCanonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ $absoluteCanonical }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:site_name" content="Phòng Khám Đa Khoa Gia Phước">
<meta property="og:image" content="{{ $metaImage }}">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $absoluteCanonical }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $metaImage }}">

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
/>

