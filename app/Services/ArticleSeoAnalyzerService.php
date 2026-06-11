<?php

namespace App\Services;

use App\Models\Article;

class ArticleSeoAnalyzerService
{
    /**
     * Analyze an article and calculate its SEO score and check list.
     */
    public function analyze(Article $article): array
    {
        $score = 0;
        
        $keyword = trim($article->focus_keyword ?? '');
        $title = trim($article->title ?? '');
        $metaTitle = trim($article->meta_title ?? '');
        $metaDesc = trim($article->meta_description ?? '');
        $slug = trim($article->slug ?? '');
        $seoSlug = trim($article->seo_slug ?? '');
        $content = trim($article->content ?? '');
        $excerpt = trim($article->excerpt ?? '');
        $thumbnail = trim($article->thumbnail_image ?? '');
        $canonical = trim($article->canonical_url ?? '');
        $schemaType = trim($article->schema_type ?? 'Article');
        
        $ogTitle = trim($article->og_title ?? '');
        $ogDesc = trim($article->og_description ?? '');
        $ogImage = trim($article->og_image ?? '');
        $twitterTitle = trim($article->twitter_title ?? '');
        $twitterDesc = trim($article->twitter_description ?? '');
        $twitterImage = trim($article->twitter_image ?? '');

        // Pre-parse content details
        $cleanContent = strip_tags($content);
        // Clean Vietnamese words
        $words = array_filter(explode(' ', preg_replace('/\s+/', ' ', $cleanContent)));
        $wordCount = count($words);

        // Group definitions
        $basicGroup = ['name' => 'SEO cơ bản', 'checks' => []];
        $titleMetaGroup = ['name' => 'Tiêu đề & Thẻ Meta', 'checks' => []];
        $contentGroup = ['name' => 'Nội dung', 'checks' => []];
        $linkImageGroup = ['name' => 'Liên kết & Hình ảnh', 'checks' => []];
        $technicalGroup = ['name' => 'Kỹ thuật', 'checks' => []];

        if ($keyword !== '') {
            $kwLower = mb_strtolower($keyword);

            // ==========================================
            // A. SEO CƠ BẢN — 30 điểm
            // ==========================================
            
            // 1. Có từ khóa chính (5 điểm)
            $score += 5;
            $basicGroup['checks'][] = [
                'key' => 'focus_keyword',
                'label' => 'Từ khóa chính',
                'status' => 'pass',
                'message' => 'Đã cấu hình từ khóa chính: "' . $keyword . '"',
                'points' => 5
            ];

            // 2. Từ khóa chính có trong tiêu đề bài viết (H1) (5 điểm)
            $titleKwPass = mb_strpos(mb_strtolower($title), $kwLower) !== false;
            if ($titleKwPass) {
                $score += 5;
            }
            $basicGroup['checks'][] = [
                'key' => 'keyword_in_title',
                'label' => 'Từ khóa chính trong H1',
                'status' => $titleKwPass ? 'pass' : 'fail',
                'message' => $titleKwPass ? 'Từ khóa chính xuất hiện trong Tiêu đề bài viết.' : 'Tiêu đề bài viết không chứa từ khóa chính.',
                'points' => 5
            ];

            // 3. Từ khóa chính có trong SEO Title (meta_title) (5 điểm)
            $seoTitleKwPass = mb_strpos(mb_strtolower($metaTitle), $kwLower) !== false;
            if ($seoTitleKwPass) {
                $score += 5;
            }
            $basicGroup['checks'][] = [
                'key' => 'keyword_in_seo_title',
                'label' => 'Từ khóa chính trong SEO Title',
                'status' => $seoTitleKwPass ? 'pass' : 'fail',
                'message' => $seoTitleKwPass ? 'Từ khóa chính xuất hiện trong SEO Title.' : 'SEO Title không chứa từ khóa chính.',
                'points' => 5
            ];

            // 4. Từ khóa chính có trong Meta Description (5 điểm)
            $descKwPass = mb_strpos(mb_strtolower($metaDesc), $kwLower) !== false;
            if ($descKwPass) {
                $score += 5;
            }
            $basicGroup['checks'][] = [
                'key' => 'keyword_in_meta_description',
                'label' => 'Từ khóa chính trong Meta Description',
                'status' => $descKwPass ? 'pass' : 'fail',
                'message' => $descKwPass ? 'Từ khóa chính xuất hiện trong Meta Description.' : 'Meta Description không chứa từ khóa chính.',
                'points' => 5
            ];

            // 5. Từ khóa chính có trong Slug (5 điểm)
            $targetSlug = $seoSlug !== '' ? $seoSlug : $slug;
            $slugKw = str_replace(' ', '-', $kwLower);
            $cleanSlugKw = $this->removeVietnameseSign($slugKw);
            $cleanTargetSlug = $this->removeVietnameseSign($targetSlug);
            $slugKwPass = mb_strpos(mb_strtolower($cleanTargetSlug), mb_strtolower($cleanSlugKw)) !== false;
            if ($slugKwPass) {
                $score += 5;
            }
            $basicGroup['checks'][] = [
                'key' => 'keyword_in_slug',
                'label' => 'Từ khóa chính trong Slug URL',
                'status' => $slugKwPass ? 'pass' : 'fail',
                'message' => $slugKwPass ? 'Từ khóa chính xuất hiện trong URL.' : 'URL không chứa từ khóa chính.',
                'points' => 5
            ];

            // 6. Có canonical URL hợp lệ hoặc tự canonical về URL bài viết (5 điểm)
            // Vì mặc định nếu để trống nó sẽ tự canonical về URL bài viết (luôn hợp lệ), ta chấm pass
            $score += 5;
            $basicGroup['checks'][] = [
                'key' => 'canonical_check',
                'label' => 'Thẻ Canonical',
                'status' => 'pass',
                'message' => $canonical !== '' ? 'Đã thiết lập Canonical URL: ' . $canonical : 'Tự động Canonical về URL bài viết.',
                'points' => 5
            ];


            // ==========================================
            // B. TIÊU ĐỀ & META — 20 điểm
            // ==========================================

            // 1. Tiêu đề bài viết dài 40-70 ký tự (5 điểm)
            $titleLength = mb_strlen($title);
            $titleLenPass = ($titleLength >= 40 && $titleLength <= 70);
            if ($titleLenPass) {
                $score += 5;
            }
            $titleMetaGroup['checks'][] = [
                'key' => 'title_length',
                'label' => 'Độ dài Tiêu đề bài viết',
                'status' => $titleLenPass ? 'pass' : 'fail',
                'message' => "Độ dài Tiêu đề bài viết nên từ 40-70 ký tự. Hiện tại: $titleLength ký tự.",
                'points' => 5
            ];

            // 2. SEO Title (meta_title) dài 50-60 ký tự (5 điểm)
            $seoTitleLength = mb_strlen($metaTitle);
            $seoTitleLenPass = ($seoTitleLength >= 50 && $seoTitleLength <= 60);
            if ($seoTitleLenPass) {
                $score += 5;
            }
            $titleMetaGroup['checks'][] = [
                'key' => 'seo_title_length',
                'label' => 'Độ dài SEO Title',
                'status' => $seoTitleLenPass ? 'pass' : 'fail',
                'message' => "Độ dài SEO Title tốt nhất là 50-60 ký tự. Hiện tại: $seoTitleLength ký tự.",
                'points' => 5
            ];

            // 3. Meta Description dài 140-160 ký tự (5 điểm)
            $descLength = mb_strlen($metaDesc);
            $descLenPass = ($descLength >= 140 && $descLength <= 160);
            if ($descLenPass) {
                $score += 5;
            }
            $titleMetaGroup['checks'][] = [
                'key' => 'meta_description_length',
                'label' => 'Độ dài Meta Description',
                'status' => $descLenPass ? 'pass' : 'fail',
                'message' => "Độ dài Meta Description tốt nhất là 140-160 ký tự. Hiện tại: $descLength ký tự.",
                'points' => 5
            ];

            // 4. Meta Description có CTA/lợi ích rõ ràng (5 điểm)
            $ctaWords = ['ngay', 'nhanh chóng', 'an toàn', 'uy tín', 'hiệu quả', 'chi tiết', 'cam kết', 'tư vấn', 'miễn phí', 'liên hệ', 'click', 'xem'];
            $hasCta = false;
            foreach ($ctaWords as $word) {
                if (mb_strpos(mb_strtolower($metaDesc), $word) !== false) {
                    $hasCta = true;
                    break;
                }
            }
            if ($hasCta) {
                $score += 5;
            }
            $titleMetaGroup['checks'][] = [
                'key' => 'meta_description_cta',
                'label' => 'Từ kêu gọi hành động (CTA) / Lợi ích',
                'status' => $hasCta ? 'pass' : 'fail',
                'message' => $hasCta ? 'Thẻ Meta Description chứa từ kêu gọi hành động hoặc lợi ích.' : 'Nên thêm các từ kích thích click (ví dụ: ngay, nhanh chóng, an toàn, uy tín, liên hệ, miễn phí...).',
                'points' => 5
            ];


            // ==========================================
            // C. NỘI DUNG — 25 điểm
            // ==========================================

            // 1. Nội dung tối thiểu 800 từ (5 điểm)
            $wordCountPass = ($wordCount >= 800);
            if ($wordCountPass) {
                $score += 5;
            }
            $contentGroup['checks'][] = [
                'key' => 'content_length',
                'label' => 'Độ dài bài viết',
                'status' => $wordCountPass ? 'pass' : 'fail',
                'message' => "Độ dài khuyên dùng từ 800 từ trở lên. Hiện tại: $wordCount từ.",
                'points' => 5
            ];

            // 2. Có ít nhất một H2 (5 điểm)
            $hasH2 = preg_match('/<h2[^>]*>/i', $content);
            if ($hasH2) {
                $score += 5;
            }
            $contentGroup['checks'][] = [
                'key' => 'has_h2',
                'label' => 'Sử dụng thẻ Heading H2',
                'status' => $hasH2 ? 'pass' : 'fail',
                'message' => $hasH2 ? 'Bài viết có chứa tiêu đề phụ H2.' : 'Vui lòng bổ sung ít nhất một tiêu đề phụ H2.',
                'points' => 5
            ];

            // 3. Có 2-6 H2/H3 hợp lý (5 điểm)
            preg_match_all('/<h2[^>]*>/i', $content, $h2Tags);
            preg_match_all('/<h3[^>]*>/i', $content, $h3Tags);
            $totalHeadings = count($h2Tags[0] ?? []) + count($h3Tags[0] ?? []);
            $headingsPass = ($totalHeadings >= 2 && $totalHeadings <= 6);
            if ($headingsPass) {
                $score += 5;
            }
            $contentGroup['checks'][] = [
                'key' => 'headings_structure',
                'label' => 'Số lượng Heading H2 & H3',
                'status' => $headingsPass ? 'pass' : 'fail',
                'message' => "Nên có từ 2-6 thẻ H2/H3 trong bài viết. Hiện tại: $totalHeadings thẻ H2/H3.",
                'points' => 5
            ];

            // 4. Mật độ từ khóa chính khoảng 0.5% - 2.5% (5 điểm)
            $keywordCount = mb_substr_count(mb_strtolower($cleanContent), $kwLower);
            $density = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;
            $densityPass = ($density >= 0.5 && $density <= 2.5);
            if ($densityPass) {
                $score += 5;
            }
            $contentGroup['checks'][] = [
                'key' => 'keyword_density',
                'label' => 'Mật độ từ khóa chính',
                'status' => $densityPass ? 'pass' : 'warning',
                'message' => "Mật độ khuyên dùng là 0.5% - 2.5%. Hiện tại: " . number_format($density, 2) . "% ($keywordCount lần).",
                'points' => 5
            ];

            // 5. Từ khóa xuất hiện trong 150 từ đầu (5 điểm)
            $first150Content = implode(' ', array_slice($words, 0, 150));
            $first150Pass = mb_strpos(mb_strtolower($first150Content), $kwLower) !== false;
            if ($first150Pass) {
                $score += 5;
            }
            $contentGroup['checks'][] = [
                'key' => 'keyword_in_first_150_words',
                'label' => 'Từ khóa ở đoạn mở đầu',
                'status' => $first150Pass ? 'pass' : 'fail',
                'message' => $first150Pass ? 'Từ khóa chính xuất hiện trong 150 từ đầu tiên.' : 'Từ khóa chính không được tìm thấy trong 150 từ đầu tiên.',
                'points' => 5
            ];


            // ==========================================
            // D. LIÊN KẾT & HÌNH ẢNH — 15 điểm
            // ==========================================

            // 1. Có ảnh đại diện (3 điểm)
            $hasThumbnail = ($thumbnail !== '');
            if ($hasThumbnail) {
                $score += 3;
            }
            $linkImageGroup['checks'][] = [
                'key' => 'has_thumbnail',
                'label' => 'Ảnh đại diện (Thumbnail)',
                'status' => $hasThumbnail ? 'pass' : 'fail',
                'message' => $hasThumbnail ? 'Đã cài đặt ảnh đại diện.' : 'Chưa cài đặt ảnh đại diện cho bài viết.',
                'points' => 3
            ];

            // 2. Có ít nhất 1 ảnh trong nội dung (3 điểm)
            preg_match_all('/<img[^>]+>/i', $content, $contentImgs);
            $totalContentImgs = count($contentImgs[0] ?? []);
            $hasContentImg = ($totalContentImgs > 0);
            if ($hasContentImg) {
                $score += 3;
            }
            $linkImageGroup['checks'][] = [
                'key' => 'has_content_image',
                'label' => 'Ảnh trong nội dung',
                'status' => $hasContentImg ? 'pass' : 'fail',
                'message' => $hasContentImg ? "Bài viết có $totalContentImgs ảnh trong nội dung." : 'Nên có ít nhất 1 ảnh minh họa trong phần nội dung.',
                'points' => 3
            ];

            // 3. Ảnh có alt (3 điểm)
            $hasAltOnImages = false;
            $missingAltCount = 0;
            if ($totalContentImgs > 0) {
                foreach ($contentImgs[0] as $imgTag) {
                    if (!preg_match('/alt=["\'][^"\']+["\']/i', $imgTag)) {
                        $missingAltCount++;
                    }
                }
                $hasAltOnImages = ($missingAltCount === 0);
            } else {
                // If there are no images at all, they fail this check as well
                $hasAltOnImages = false;
            }
            
            if ($totalContentImgs > 0 && $hasAltOnImages) {
                $score += 3;
            }
            $linkImageGroup['checks'][] = [
                'key' => 'images_alt',
                'label' => 'Thuộc tính Alt của hình ảnh',
                'status' => ($totalContentImgs > 0 && $hasAltOnImages) ? 'pass' : 'warning',
                'message' => ($totalContentImgs > 0 && $hasAltOnImages) ? 'Tất cả hình ảnh nội dung đều có Alt tag.' : ($totalContentImgs > 0 ? "Có $missingAltCount hình ảnh bị thiếu thuộc tính Alt." : 'Không có hình ảnh nội dung nào để kiểm tra Alt tag.'),
                'points' => 3
            ];

            // 4. Có ít nhất 1 internal link (3 điểm)
            $hasInternalLink = false;
            $hasExternalLink = false;
            preg_match_all('/href=["\']([^"\']+)["\']/i', $content, $linksMatches);
            if (!empty($linksMatches[1])) {
                foreach ($linksMatches[1] as $href) {
                    if (str_starts_with($href, '/') || str_contains($href, request()->getHost())) {
                        $hasInternalLink = true;
                    } else if (str_starts_with($href, 'http') || str_starts_with($href, 'https')) {
                        $hasExternalLink = true;
                    }
                }
            }
            if ($hasInternalLink) {
                $score += 3;
            }
            $linkImageGroup['checks'][] = [
                'key' => 'internal_link',
                'label' => 'Liên kết nội bộ (Internal Link)',
                'status' => $hasInternalLink ? 'pass' : 'fail',
                'message' => $hasInternalLink ? 'Bài viết chứa liên kết nội bộ.' : 'Cần thêm ít nhất 1 liên kết nội bộ hướng tới bài viết/trang khác của phòng khám.',
                'points' => 3
            ];

            // 5. Có ít nhất 1 external link (3 điểm)
            if ($hasExternalLink) {
                $score += 3;
            }
            $linkImageGroup['checks'][] = [
                'key' => 'external_link',
                'label' => 'Liên kết ngoài (External Link)',
                'status' => $hasExternalLink ? 'pass' : 'fail',
                'message' => $hasExternalLink ? 'Bài viết chứa liên kết ngoài.' : 'Cần thêm ít nhất 1 liên kết ngoài hướng tới trang web uy tín để kiểm chứng dữ liệu.',
                'points' => 3
            ];


            // ==========================================
            // E. KỸ THUẬT — 10 điểm
            // ==========================================

            // 1. Slug ngắn, dưới 80 ký tự (3 điểm)
            $slugLen = mb_strlen($targetSlug);
            $slugLenPass = ($slugLen < 80);
            if ($slugLenPass) {
                $score += 3;
            }
            $technicalGroup['checks'][] = [
                'key' => 'slug_length',
                'label' => 'Độ dài Slug URL',
                'status' => $slugLenPass ? 'pass' : 'fail',
                'message' => "Slug nên ngắn gọn dưới 80 ký tự. Hiện tại: $slugLen ký tự.",
                'points' => 3
            ];

            // 2. Không trùng SEO title với bài khác (2 điểm)
            $titleQuery = Article::where('meta_title', $metaTitle);
            if ($article && $article->exists) {
                $titleQuery->where('id', '!=', $article->id);
            }
            $isTitleDuplicate = ($metaTitle !== '') && $titleQuery->exists();
            if (!$isTitleDuplicate) {
                $score += 2;
            }
            $technicalGroup['checks'][] = [
                'key' => 'unique_seo_title',
                'label' => 'Độc nhất SEO Title',
                'status' => $isTitleDuplicate ? 'fail' : 'pass',
                'message' => $isTitleDuplicate ? 'Cảnh báo: SEO Title đã bị trùng với bài viết khác.' : 'SEO Title là độc nhất, không bị trùng lặp.',
                'points' => 2
            ];

            // 3. Không trùng meta description với bài khác (2 điểm)
            $descQuery = Article::where('meta_description', $metaDesc);
            if ($article && $article->exists) {
                $descQuery->where('id', '!=', $article->id);
            }
            $isDescDuplicate = ($metaDesc !== '') && $descQuery->exists();
            if (!$isDescDuplicate) {
                $score += 2;
            }
            $technicalGroup['checks'][] = [
                'key' => 'unique_meta_description',
                'label' => 'Độc nhất Meta Description',
                'status' => $isDescDuplicate ? 'fail' : 'pass',
                'message' => $isDescDuplicate ? 'Cảnh báo: Meta Description bị trùng lặp với bài viết khác.' : 'Meta Description là độc nhất, không bị trùng lặp.',
                'points' => 2
            ];

            // 4. Có schema type phù hợp (khác None) (3 điểm)
            $hasSchema = !in_array(strtolower($schemaType), ['none', '']);
            if ($hasSchema) {
                $score += 3;
            }
            $technicalGroup['checks'][] = [
                'key' => 'schema_type_check',
                'label' => 'Chọn cấu hình Schema JSON-LD',
                'status' => $hasSchema ? 'pass' : 'fail',
                'message' => $hasSchema ? "Đang sử dụng loại Schema: $schemaType" : 'Bạn chưa cấu hình Schema JSON-LD (nên chọn Article/MedicalWebPage).',
                'points' => 3
            ];

        } else {
            // Default checks if focus keyword is empty
            $basicGroup['checks'][] = ['key' => 'focus_keyword', 'label' => 'Từ khóa chính', 'status' => 'fail', 'message' => 'Vui lòng nhập từ khóa chính để bắt đầu phân tích.'];
            $basicGroup['checks'][] = ['key' => 'keyword_in_title', 'label' => 'Từ khóa chính trong H1', 'status' => 'fail', 'message' => 'Chưa cấu hình từ khóa chính.'];
            $basicGroup['checks'][] = ['key' => 'keyword_in_seo_title', 'label' => 'Từ khóa chính trong SEO Title', 'status' => 'fail', 'message' => 'Chưa cấu hình từ khóa chính.'];
            $basicGroup['checks'][] = ['key' => 'keyword_in_meta_description', 'label' => 'Từ khóa chính trong Meta Description', 'status' => 'fail', 'message' => 'Chưa cấu hình từ khóa chính.'];
            $basicGroup['checks'][] = ['key' => 'keyword_in_slug', 'label' => 'Từ khóa chính trong Slug URL', 'status' => 'fail', 'message' => 'Chưa cấu hình từ khóa chính.'];
            $basicGroup['checks'][] = ['key' => 'canonical_check', 'label' => 'Thẻ Canonical', 'status' => 'warning', 'message' => 'Chưa cấu hình thẻ Canonical.'];

            $titleMetaGroup['checks'][] = ['key' => 'title_length', 'label' => 'Độ dài Tiêu đề bài viết', 'status' => 'fail', 'message' => 'Hãy viết tiêu đề từ 40-70 ký tự.'];
            $titleMetaGroup['checks'][] = ['key' => 'seo_title_length', 'label' => 'Độ dài SEO Title', 'status' => 'fail', 'message' => 'Hãy viết Meta Title từ 50-60 ký tự.'];
            $titleMetaGroup['checks'][] = ['key' => 'meta_description_length', 'label' => 'Độ dài Meta Description', 'status' => 'fail', 'message' => 'Hãy viết Meta Description từ 140-160 ký tự.'];
            $titleMetaGroup['checks'][] = ['key' => 'meta_description_cta', 'label' => 'Kêu gọi hành động (CTA)', 'status' => 'fail', 'message' => 'Meta Description cần chứa từ kích thích click.'];

            $contentGroup['checks'][] = ['key' => 'content_length', 'label' => 'Độ dài bài viết', 'status' => 'fail', 'message' => 'Nội dung bài viết quá ngắn.'];
            $contentGroup['checks'][] = ['key' => 'has_h2', 'label' => 'Sử dụng thẻ Heading H2', 'status' => 'fail', 'message' => 'Nội dung cần chứa tiêu đề phụ H2.'];
            $contentGroup['checks'][] = ['key' => 'headings_structure', 'label' => 'Số lượng Heading H2 & H3', 'status' => 'fail', 'message' => 'Nên có 2-6 tiêu đề phụ H2/H3.'];
            $contentGroup['checks'][] = ['key' => 'keyword_density', 'label' => 'Mật độ từ khóa chính', 'status' => 'fail', 'message' => 'Chưa thể tính toán mật độ từ khóa.'];
            $contentGroup['checks'][] = ['key' => 'keyword_in_first_150_words', 'label' => 'Từ khóa ở đoạn mở đầu', 'status' => 'fail', 'message' => 'Chưa có từ khóa chính.'];

            $linkImageGroup['checks'][] = ['key' => 'has_thumbnail', 'label' => 'Ảnh đại diện (Thumbnail)', 'status' => 'fail', 'message' => 'Chưa cài đặt ảnh đại diện.'];
            $linkImageGroup['checks'][] = ['key' => 'has_content_image', 'label' => 'Ảnh trong nội dung', 'status' => 'fail', 'message' => 'Chưa có hình ảnh nào trong nội dung.'];
            $linkImageGroup['checks'][] = ['key' => 'images_alt', 'label' => 'Thuộc tính Alt của hình ảnh', 'status' => 'fail', 'message' => 'Chưa có thuộc tính Alt cho ảnh.'];
            $linkImageGroup['checks'][] = ['key' => 'internal_link', 'label' => 'Liên kết nội bộ (Internal Link)', 'status' => 'fail', 'message' => 'Chưa có liên kết nội bộ.'];
            $linkImageGroup['checks'][] = ['key' => 'external_link', 'label' => 'Liên kết ngoài (External Link)', 'status' => 'fail', 'message' => 'Chưa có liên kết ngoài.'];

            $technicalGroup['checks'][] = ['key' => 'slug_length', 'label' => 'Độ dài Slug URL', 'status' => 'fail', 'message' => 'Hãy điền đường dẫn slug ngắn dưới 80 ký tự.'];
            $technicalGroup['checks'][] = ['key' => 'unique_seo_title', 'label' => 'Độc nhất SEO Title', 'status' => 'fail', 'message' => 'Chưa kiểm tra được trùng lặp.'];
            $technicalGroup['checks'][] = ['key' => 'unique_meta_description', 'label' => 'Độc nhất Meta Description', 'status' => 'fail', 'message' => 'Chưa kiểm tra được trùng lặp.'];
            $technicalGroup['checks'][] = ['key' => 'schema_type_check', 'label' => 'Chọn cấu hình Schema JSON-LD', 'status' => 'fail', 'message' => 'Hãy chọn một Schema type phù hợp.'];
        }

        // Clamp score to 100 max, 0 min
        $score = (int) round(min(max($score, 0), 100));

        $level = 'bad';
        if ($score >= 75) {
            $level = 'good';
        } elseif ($score >= 50) {
            $level = 'average';
        }

        return [
            'score' => $score,
            'level' => $level,
            'groups' => [
                $basicGroup,
                $titleMetaGroup,
                $contentGroup,
                $linkImageGroup,
                $technicalGroup
            ]
        ];
    }

    /**
     * Helper to remove Vietnamese signs (accents) for string search.
     */
    protected function removeVietnameseSign(string $str): string
    {
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|À|Ỷ|Ỹ|Ỵ',
        ];
        
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
}
