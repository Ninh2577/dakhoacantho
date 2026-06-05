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
        $thumbnail = trim($article->thumbnail_image ?? '');
        $canonical = trim($article->canonical_url ?? '');
        
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
        $contentGroup = ['name' => 'Nội dung', 'checks' => []];
        $imageGroup = ['name' => 'Hình ảnh', 'checks' => []];
        $linkGroup = ['name' => 'Liên kết', 'checks' => []];
        $socialGroup = ['name' => 'Mạng xã hội (Social SEO)', 'checks' => []];
        $advancedGroup = ['name' => 'Nâng cao', 'checks' => []];

        // 1. Focus Keyword (10 pts)
        if ($keyword !== '') {
            $score += 10;
            $basicGroup['checks'][] = [
                'key' => 'focus_keyword',
                'label' => 'Từ khóa chính (Focus Keyword)',
                'status' => 'pass',
                'message' => 'Đã cấu hình từ khóa chính: "' . $keyword . '"'
            ];
        } else {
            $basicGroup['checks'][] = [
                'key' => 'focus_keyword',
                'label' => 'Từ khóa chính (Focus Keyword)',
                'status' => 'fail',
                'message' => 'Vui lòng nhập từ khóa chính để kích hoạt phân tích.'
            ];
        }

        if ($keyword !== '') {
            $kwLower = mb_strtolower($keyword);

            // 2. Meta Title (15 pts)
            $titleLength = mb_strlen($metaTitle);
            $titleLenPass = ($titleLength >= 50 && $titleLength <= 60);
            $titleKwPass = mb_strpos(mb_strtolower($metaTitle), $kwLower) !== false;
            
            $score += ($titleLenPass ? 7.5 : 0) + ($titleKwPass ? 7.5 : 0);
            $basicGroup['checks'][] = [
                'key' => 'meta_title_length',
                'label' => 'Độ dài Meta Title',
                'status' => $titleLenPass ? 'pass' : 'fail',
                'message' => "Độ dài Meta Title tốt nhất là 50-60 ký tự. Hiện tại: $titleLength ký tự."
            ];
            $basicGroup['checks'][] = [
                'key' => 'meta_title_keyword',
                'label' => 'Từ khóa trong Meta Title',
                'status' => $titleKwPass ? 'pass' : 'fail',
                'message' => $titleKwPass ? 'Từ khóa xuất hiện trong Meta Title.' : 'Từ khóa chính không tìm thấy trong Meta Title.'
            ];

            // 3. Meta Description (15 pts)
            $descLength = mb_strlen($metaDesc);
            $descLenPass = ($descLength >= 150 && $descLength <= 160);
            $descKwPass = mb_strpos(mb_strtolower($metaDesc), $kwLower) !== false;
            
            $score += ($descLenPass ? 7.5 : 0) + ($descKwPass ? 7.5 : 0);
            $basicGroup['checks'][] = [
                'key' => 'meta_desc_length',
                'label' => 'Độ dài Meta Description',
                'status' => $descLenPass ? 'pass' : 'fail',
                'message' => "Độ dài Meta Description tốt nhất là 150-160 ký tự. Hiện tại: $descLength ký tự."
            ];
            $basicGroup['checks'][] = [
                'key' => 'meta_desc_keyword',
                'label' => 'Từ khóa trong Meta Description',
                'status' => $descKwPass ? 'pass' : 'fail',
                'message' => $descKwPass ? 'Từ khóa xuất hiện trong Meta Description.' : 'Từ khóa chính không tìm thấy trong Meta Description.'
            ];

            // Duplicate Meta Title Check
            $titleQuery = Article::where('meta_title', $metaTitle);
            if ($article && $article->exists) {
                $titleQuery->where('id', '!=', $article->id);
            }
            $isTitleDuplicate = ($metaTitle !== '') && $titleQuery->exists();
            $basicGroup['checks'][] = [
                'key' => 'meta_title_duplicate',
                'label' => 'Trùng lặp Meta Title',
                'status' => $isTitleDuplicate ? 'warning' : 'pass',
                'message' => $isTitleDuplicate ? 'Cảnh báo: Meta Title trùng với bài viết khác trên hệ thống.' : 'Meta Title là độc nhất.'
            ];

            // Duplicate Meta Description Check
            $descQuery = Article::where('meta_description', $metaDesc);
            if ($article && $article->exists) {
                $descQuery->where('id', '!=', $article->id);
            }
            $isDescDuplicate = ($metaDesc !== '') && $descQuery->exists();
            $basicGroup['checks'][] = [
                'key' => 'meta_description_duplicate',
                'label' => 'Trùng lặp Meta Description',
                'status' => $isDescDuplicate ? 'warning' : 'pass',
                'message' => $isDescDuplicate ? 'Cảnh báo: Meta Description trùng với bài viết khác trên hệ thống.' : 'Meta Description là độc nhất.'
            ];

            // 4. Slug / SEO Slug SEO (10 pts)
            $targetSlug = $seoSlug !== '' ? $seoSlug : $slug;
            $slugKw = str_replace(' ', '-', $kwLower);
            // Quick clean slug keyword check
            $cleanSlugKw = $this->removeVietnameseSign($slugKw);
            $cleanTargetSlug = $this->removeVietnameseSign($targetSlug);
            
            $slugKwPass = mb_strpos(mb_strtolower($cleanTargetSlug), mb_strtolower($cleanSlugKw)) !== false;
            $slugFriendly = (mb_strlen($targetSlug) <= 50 && preg_match('/^[a-z0-9\-]+$/i', $targetSlug));
            
            $score += ($slugKwPass ? 5 : 0) + ($slugFriendly ? 5 : 0);
            $basicGroup['checks'][] = [
                'key' => 'slug_keyword',
                'label' => 'Từ khóa trong URL Slug',
                'status' => $slugKwPass ? 'pass' : 'fail',
                'message' => $slugKwPass ? 'Từ khóa xuất hiện trong URL.' : 'Từ khóa chính không tìm thấy trong URL.'
            ];
            $basicGroup['checks'][] = [
                'key' => 'slug_friendly',
                'label' => 'Cấu trúc Slug URL',
                'status' => $slugFriendly ? 'pass' : 'fail',
                'message' => $slugFriendly ? 'Slug ngắn và chuẩn hóa ký tự.' : 'Slug chứa ký tự lạ hoặc dài hơn 50 ký tự.'
            ];

            // 5. Keyword Placement & Content checks (15 pts)
            $titleKwPass = mb_strpos(mb_strtolower($title), $kwLower) !== false;
            $contentLen = mb_strlen($content);
            $first10PercentLimit = (int) ($contentLen * 0.1);
            $first10PercentContent = mb_substr($content, 0, max($first10PercentLimit, 200));
            $contentFirst10Pass = mb_strpos(mb_strtolower($first10PercentContent), $kwLower) !== false;
            
            $score += ($contentFirst10Pass ? 5 : 0) + ($titleKwPass ? 5 : 0);
            $contentGroup['checks'][] = [
                'key' => 'title_keyword',
                'label' => 'Từ khóa trong Tiêu đề bài viết',
                'status' => $titleKwPass ? 'pass' : 'fail',
                'message' => $titleKwPass ? 'Từ khóa xuất hiện trong tiêu đề H1.' : 'Không tìm thấy từ khóa trong tiêu đề H1.'
            ];
            $contentGroup['checks'][] = [
                'key' => 'content_first_10_keyword',
                'label' => 'Từ khóa xuất hiện ở đoạn đầu',
                'status' => $contentFirst10Pass ? 'pass' : 'fail',
                'message' => $contentFirst10Pass ? 'Đoạn đầu bài viết chứa từ khóa chính.' : 'Đoạn đầu bài viết không chứa từ khóa.'
            ];

            // Keyword density check (5 pts)
            $keywordCount = mb_substr_count(mb_strtolower($cleanContent), $kwLower);
            $density = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;
            
            $densityStatus = 'warning';
            $densityMsg = "Mật độ từ khóa chính: " . number_format($density, 2) . "% ($keywordCount lần). ";
            if ($density >= 0.5 && $density <= 2.5) {
                $score += 5;
                $densityStatus = 'pass';
                $densityMsg .= "Đạt mật độ khuyên dùng (0.5% - 2.5%).";
            } elseif ($density < 0.5) {
                $densityMsg .= "Từ khóa xuất hiện hơi ít (Khuyên dùng: > 0.5%).";
            } else {
                $densityMsg .= "Mật độ từ khóa hơi cao, nguy cơ nhồi nhét (Khuyên dùng: < 2.5%).";
            }
            $contentGroup['checks'][] = [
                'key' => 'keyword_density',
                'label' => 'Mật độ từ khóa chính (Keyword Density)',
                'status' => $densityStatus,
                'message' => $densityMsg
            ];

            // 6. Content Length & Heading (15 pts)
            $wordCountPass = ($wordCount >= 600);
            $hasH2 = preg_match('/<h2[^>]*>/i', $content);
            $hasH3 = preg_match('/<h3[^>]*>/i', $content);
            $headingPass = $hasH2; // has at least H2
            
            $score += ($wordCountPass ? 7.5 : 0) + ($headingPass ? 7.5 : 0);
            $contentGroup['checks'][] = [
                'key' => 'content_length',
                'label' => 'Độ dài bài viết',
                'status' => $wordCountPass ? 'pass' : 'fail',
                'message' => "Bài viết có $wordCount từ (Khuyên dùng: từ 600 từ trở lên)."
            ];
            $contentGroup['checks'][] = [
                'key' => 'content_h2',
                'label' => 'Sử dụng thẻ Heading H2',
                'status' => $hasH2 ? 'pass' : 'fail',
                'message' => $hasH2 ? 'Tìm thấy thẻ H2.' : 'Vui lòng bổ sung thẻ H2 để phân chia bố cục rõ ràng.'
            ];

            // 7. Image + Alt (10 pts)
            $hasThumbnail = ($thumbnail !== '');
            // Content images analyzer
            preg_match_all('/<img[^>]+>/i', $content, $imgTags);
            $totalContentImgs = count($imgTags[0] ?? []);
            
            $hasAltOnImages = false;
            $missingAltCount = 0;
            if ($totalContentImgs > 0) {
                foreach ($imgTags[0] as $imgTag) {
                    if (!preg_match('/alt=["\'][^"\']+["\']/i', $imgTag)) {
                        $missingAltCount++;
                    }
                }
                $hasAltOnImages = ($missingAltCount === 0);
            }

            // Image checklist status logic
            $imageStatus = 'fail';
            $imageMsg = '';
            if ($hasThumbnail && $totalContentImgs > 0 && $hasAltOnImages) {
                $score += 10;
                $imageStatus = 'pass';
                $imageMsg = 'Đã có thumbnail & ảnh trong nội dung kèm Alt đầy đủ.';
            } elseif ($hasThumbnail) {
                $score += 5;
                $imageStatus = 'warning';
                $imageMsg = 'Có thumbnail. ';
                if ($totalContentImgs > 0 && !$hasAltOnImages) {
                    $imageMsg .= "Cảnh báo: Có $missingAltCount ảnh trong nội dung thiếu thuộc tính Alt.";
                } else {
                    $imageMsg .= 'Gợi ý: Bổ sung thêm ảnh kèm Alt trong nội dung.';
                }
            } else {
                $imageMsg = 'Thiếu ảnh đại diện (Thumbnail) và ảnh trong nội dung.';
            }

            $imageGroup['checks'][] = [
                'key' => 'image_alt_check',
                'label' => 'Tối ưu hình ảnh & Thuộc tính Alt',
                'status' => $imageStatus,
                'message' => $imageMsg
            ];

            // 8. Links (5 pts)
            $hasInternalLink = false;
            $hasExternalLink = false;
            preg_match_all('/href=["\']([^"\']+)["\']/i', $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $href) {
                    if (str_starts_with($href, '/') || str_contains($href, request()->getHost())) {
                        $hasInternalLink = true;
                    } else if (str_starts_with($href, 'http') || str_starts_with($href, 'https')) {
                        $hasExternalLink = true;
                    }
                }
            }
            $score += ($hasInternalLink ? 2.5 : 0) + ($hasExternalLink ? 2.5 : 0);
            $linkGroup['checks'][] = [
                'key' => 'internal_link',
                'label' => 'Liên kết nội bộ (Internal Link)',
                'status' => $hasInternalLink ? 'pass' : 'fail',
                'message' => $hasInternalLink ? 'Đã thêm liên kết nội bộ.' : 'Khuyên dùng: Bổ sung link liên kết nội bộ dẫn tới các trang chuyên khoa liên quan.'
            ];
            $linkGroup['checks'][] = [
                'key' => 'external_link',
                'label' => 'Liên kết ngoài (External Link)',
                'status' => $hasExternalLink ? 'pass' : 'fail',
                'message' => $hasExternalLink ? 'Đã thêm liên kết ngoài.' : 'Khuyên dùng: Bổ sung liên kết ngoài uy tín để tăng độ tin cậy của bài viết.'
            ];

            // 9. Social SEO (5 pts)
            $socialPass = ($ogTitle !== '' && $ogDesc !== '' && $ogImage !== '' && $twitterTitle !== '' && $twitterDesc !== '' && $twitterImage !== '');
            $score += $socialPass ? 5 : 0;
            $socialGroup['checks'][] = [
                'key' => 'social_meta',
                'label' => 'Thông tin mạng xã hội',
                'status' => $socialPass ? 'pass' : 'warning',
                'message' => $socialPass ? 'Thông tin Open Graph & Twitter đầy đủ.' : 'Gợi ý: Cấu hình đầy đủ tiêu đề, mô tả và hình ảnh chia sẻ mạng xã hội.'
            ];

            // 10. Advanced (No specific score points, but checklist output)
            $hasCanonical = ($canonical !== '');
            $advancedGroup['checks'][] = [
                'key' => 'canonical_check',
                'label' => 'Thẻ Canonical',
                'status' => $hasCanonical ? 'pass' : 'warning',
                'message' => $hasCanonical ? "Canonical URL: $canonical" : 'Chưa cấu hình (Hệ thống sẽ tự động dùng link bài viết hiện tại làm canonical).'
            ];
            $advancedGroup['checks'][] = [
                'key' => 'robots_meta',
                'label' => 'Robots Meta',
                'status' => 'pass',
                'message' => 'Lập chỉ mục: ' . ($article->robots_index ?? true ? 'Index' : 'Noindex') . ', Liên kết: ' . ($article->robots_follow ?? true ? 'Follow' : 'Nofollow')
            ];

        } else {
            // Default blank checks
            $basicGroup['checks'][] = ['key' => 'meta_title_length', 'label' => 'Độ dài Meta Title', 'status' => 'fail', 'message' => 'Chưa cấu hình Meta Title.'];
            $basicGroup['checks'][] = ['key' => 'meta_desc_length', 'label' => 'Độ dài Meta Description', 'status' => 'fail', 'message' => 'Chưa cấu hình Meta Description.'];
            $contentGroup['checks'][] = ['key' => 'content_length', 'label' => 'Độ dài bài viết', 'status' => 'fail', 'message' => 'Vui lòng bổ sung nội dung bài viết.'];
            $imageGroup['checks'][] = ['key' => 'image_alt_check', 'label' => 'Tối ưu hình ảnh & Alt', 'status' => 'fail', 'message' => 'Không tìm thấy ảnh đại diện và Alt tags.'];
            $linkGroup['checks'][] = ['key' => 'internal_link', 'label' => 'Liên kết nội bộ (Internal Link)', 'status' => 'fail', 'message' => 'Không có liên kết nội bộ nào.'];
            $linkGroup['checks'][] = ['key' => 'external_link', 'label' => 'Liên kết ngoài (External Link)', 'status' => 'fail', 'message' => 'Không có liên kết ngoài nào.'];
            $socialGroup['checks'][] = ['key' => 'social_meta', 'label' => 'Thông tin mạng xã hội', 'status' => 'warning', 'message' => 'Chưa có cấu hình Social metadata.'];
            $advancedGroup['checks'][] = ['key' => 'canonical_check', 'label' => 'Thẻ Canonical', 'status' => 'warning', 'message' => 'Chưa cấu hình thẻ Canonical.'];
        }

        // Clamp score to 100 max
        $score = (int) round(min($score, 100));

        $level = 'bad';
        if ($score >= 80) {
            $level = 'good';
        } elseif ($score >= 50) {
            $level = 'average';
        }

        return [
            'score' => $score,
            'level' => $level,
            'groups' => [
                $basicGroup,
                $contentGroup,
                $imageGroup,
                $linkGroup,
                $socialGroup,
                $advancedGroup
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
