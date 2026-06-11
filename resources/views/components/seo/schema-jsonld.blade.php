@props([
    'pageType' => 'website',
    'title' => null,
    'description' => null,
    'url' => null,
    'image' => null,
    'breadcrumbs' => [],
    'article' => null,
    'faqItems' => [],
    'category' => null,
    'publishedAt' => null,
    'modifiedAt' => null,
    'schemaType' => 'Article',
])

@php
    $schema = App\Support\SchemaBuilder::build([
        'pageType'    => $pageType,
        'title'       => $title,
        'description' => $description,
        'url'         => $url,
        'image'       => $image,
        'breadcrumbs' => $breadcrumbs,
        'faqItems'    => $faqItems,
        'article'     => $article,
        'category'    => $category,
        'publishedAt' => $publishedAt,
        'modifiedAt'  => $modifiedAt,
        'schemaType'  => $schemaType,
    ]);
@endphp

@if(!empty($schema) && !empty($schema['@graph']))
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif
