<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Categories -->
    @foreach($categories as $category)
        <url>
            <loc>{{ url('category/' . $category->full_path) }}</loc>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    <!-- Articles -->
    @foreach($articles as $article)
        <url>
            <loc>{{ url($article->category_path . '/' . $article->slug . '.html') }}</loc>
            <lastmod>{{ $article->updated_at->toAtomString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
</urlset>
