<div x-data="seoScorecard({
    title: $wire.entangle('data.meta_title'),
    mainTitle: $wire.entangle('data.title'),
    description: $wire.entangle('data.meta_description'),
    keyword: $wire.entangle('data.focus_keyword'),
    content: $wire.entangle('data.content'),
    slug: $wire.entangle('data.slug'),
    seoSlug: $wire.entangle('data.seo_slug'),
    canonical: $wire.entangle('data.canonical_url'),
    robotsIndex: $wire.entangle('data.robots_index'),
    robotsFollow: $wire.entangle('data.robots_follow'),
    isPublished: $wire.entangle('data.is_published'),
    ogTitle: $wire.entangle('data.og_title'),
    ogDesc: $wire.entangle('data.og_description'),
    ogImage: $wire.entangle('data.og_image'),
    twitterTitle: $wire.entangle('data.twitter_title'),
    twitterDesc: $wire.entangle('data.twitter_description'),
    twitterImage: $wire.entangle('data.twitter_image'),
    serverChecksRaw: $wire.entangle('data.seo_checks'),
    schemaType: $wire.entangle('data.schema_type')
})"
    class="rounded-xl border p-4 shadow-sm transition duration-300 flex flex-col gap-4 bg-white"
    :class="scoreColor">

    <!-- Score Header with circular progress -->
    <div>
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="font-extrabold text-sm text-slate-800 uppercase tracking-wider">Điểm SEO bài viết</h3>

            <!-- SVG Circular Score -->
            <div class="relative flex items-center justify-center" style="width:72px;height:72px;">
                <svg width="72" height="72" viewBox="0 0 72 72" class="-rotate-90">
                    <circle cx="36" cy="36" r="30" fill="none" stroke="#e2e8f0" stroke-width="6"/>
                    <circle cx="36" cy="36" r="30" fill="none"
                        :stroke="score >= 75 ? '#10b981' : (score >= 50 ? '#f59e0b' : '#ef4444')"
                        stroke-width="6"
                        stroke-linecap="round"
                        :stroke-dasharray="188.5"
                        :stroke-dashoffset="188.5 - (188.5 * score / 100)"
                        style="transition:stroke-dashoffset 0.5s ease;"
                    />
                </svg>
                <div class="absolute flex flex-col items-center leading-none">
                    <span class="text-lg font-black" x-text="score" :style="'color:' + (score >= 75 ? '#10b981' : (score >= 50 ? '#f59e0b' : '#ef4444'))"></span>
                    <span class="text-[9px] text-slate-400 font-semibold">/100</span>
                </div>
            </div>
        </div>

        <!-- Grade label -->
        <div class="mt-2 text-center text-xs font-bold"
             :class="score >= 75 ? 'text-emerald-600' : (score >= 50 ? 'text-amber-600' : 'text-rose-600')"
             x-text="score >= 90 ? '🌟 Rất tốt' : (score >= 75 ? '✅ Tốt' : (score >= 50 ? '⚠️ Cần cải thiện' : '❌ Kém'))"
        ></div>

        <!-- Publish Warning Banner -->
        <template x-if="publishState">
            <div class="mt-3 p-3 rounded-lg flex items-start gap-2 text-xs font-bold shadow-sm" :class="publishState.bg">
                <span x-text="publishState.icon" class="text-sm shrink-0"></span>
                <span x-text="publishState.text"></span>
            </div>
        </template>
    </div>

    <!-- Scorecard Navigation Tabs -->
    <div class="flex flex-wrap border-b border-slate-100 text-xs font-bold gap-1 bg-slate-50 p-1 rounded-lg">
        <button type="button" @click="activeTab = 'basic'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'basic' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">SEO cơ bản</button>
        <button type="button" @click="activeTab = 'content'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'content' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Nội dung & Link</button>
        <button type="button" @click="activeTab = 'technical'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'technical' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Tiêu đề & Kỹ thuật</button>
        <button type="button" @click="activeTab = 'preview'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'preview' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Xem trước</button>
    </div>

    <!-- Tab 1: Basic SEO -->
    <div x-show="activeTab === 'basic'" class="space-y-3">
        <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider">SEO cơ bản (30đ)</h4>
        <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
            <!-- Focus keyword -->
            <li class="flex items-start gap-2">
                <span x-show="checks.hasKeyword" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.hasKeyword" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính: <span x-text="keyword && typeof keyword === 'string' ? '«' + keyword + '»' : (keyword ? '«' + String(keyword) + '»' : 'Chưa nhập')"></span> (5đ)</span>
            </li>
            <!-- Keyword in Main Title (H1) -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isTitleMainKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isTitleMainKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính có trong Tiêu đề (H1) (5đ)</span>
            </li>
            <!-- Keyword in SEO Title -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isTitleKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isTitleKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính có trong SEO Title (5đ)</span>
            </li>
            <!-- Keyword in Meta Description -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isDescKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isDescKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính có trong Meta Description (5đ)</span>
            </li>
            <!-- Keyword in Slug -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isSlugKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isSlugKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính có trong Slug URL (5đ)</span>
            </li>
            <!-- Canonical -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isCanonicalGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isCanonicalGood" class="text-rose-500 text-sm">❌</span>
                <span>Canonical URL hợp lệ hoặc tự động (5đ)</span>
            </li>
        </ul>
    </div>

    <!-- Tab 2: Content, Links & Images -->
    <div x-show="activeTab === 'content'" class="space-y-4">
        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Nội dung (25đ)</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                <li class="flex items-start gap-2">
                    <span x-show="checks.isWordCountGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isWordCountGood" class="text-rose-500 text-sm">❌</span>
                    <span>Độ dài bài viết: <span class="font-bold" x-text="checks.wordCount"></span> từ (yêu cầu ≥ 800 từ) (5đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasH2" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasH2" class="text-rose-500 text-sm">❌</span>
                    <span>Sử dụng thẻ Heading H2 (5đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isHeadingsStructureGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isHeadingsStructureGood" class="text-rose-500 text-sm">❌</span>
                    <span>Số lượng H2/H3 hợp lý: <span class="font-bold" x-text="checks.totalHeadings"></span> (khuyên dùng 2-6) (5đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isDensityGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isDensityGood" class="text-amber-500 text-sm">⚠️</span>
                    <span>Mật độ từ khóa: <span class="font-bold" x-text="checks.density.toFixed(2) + '%'"></span> (khuyên dùng 0.5% - 2.5%) (5đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isKeywordFirst150Good" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isKeywordFirst150Good" class="text-rose-500 text-sm">❌</span>
                    <span>Từ khóa chính xuất hiện trong 150 từ đầu tiên (5đ)</span>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Hình ảnh & Liên kết (15đ)</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasThumbnail" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasThumbnail" class="text-rose-500 text-sm">❌</span>
                    <span>Có Ảnh đại diện (Thumbnail) (3đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasContentImage" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasContentImage" class="text-rose-500 text-sm">❌</span>
                    <span>Có ảnh trong nội dung (<span x-text="checks.totalContentImgs"></span> ảnh) (3đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.totalContentImgs > 0 && checks.hasAltOnImages" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="checks.totalContentImgs > 0 && !checks.hasAltOnImages" class="text-amber-500 text-sm">⚠️</span>
                    <span x-show="checks.totalContentImgs === 0" class="text-rose-500 text-sm">❌</span>
                    <span>Hình ảnh trong nội dung có Alt text (3đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasInternal" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasInternal" class="text-rose-500 text-sm">❌</span>
                    <span>Có liên kết nội bộ (Internal Link) (3đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasExternal" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasExternal" class="text-rose-500 text-sm">❌</span>
                    <span>Có liên kết ngoài (External Link) (3đ)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab 3: Title, Meta lengths and Technical -->
    <div x-show="activeTab === 'technical'" class="space-y-4">
        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Tiêu đề & Thẻ Meta (20đ)</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold font-sans">
                <!-- Main Title length -->
                <li class="flex flex-col gap-1">
                    <div class="flex items-start gap-2">
                        <span x-show="checks.isArticleTitleLengthGood" class="text-emerald-500 text-sm shrink-0">✔️</span>
                        <span x-show="!checks.isArticleTitleLengthGood" class="text-rose-500 text-sm shrink-0">❌</span>
                        <span>Độ dài Tiêu đề: <span class="font-bold" x-text="checks.articleTitleLength"></span>/70 ký tự (40-70) (5đ)</span>
                    </div>
                    <div class="ml-6 h-1 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                             :style="'width:' + Math.min(checks.articleTitleLength/70*100,100) + '%;background-color:' + (checks.isArticleTitleLengthGood ? '#10b981' : '#f59e0b')"
                        ></div>
                    </div>
                </li>
                <!-- Meta Title length -->
                <li class="flex flex-col gap-1">
                    <div class="flex items-start gap-2">
                        <span x-show="checks.isTitleLengthGood" class="text-emerald-500 text-sm shrink-0">✔️</span>
                        <span x-show="!checks.isTitleLengthGood" class="text-rose-500 text-sm shrink-0">❌</span>
                        <span>Độ dài SEO Title: <span class="font-bold" x-text="checks.titleLength"></span>/60 ký tự (50-60) (5đ)</span>
                    </div>
                    <div class="ml-6 h-1 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                             :style="'width:' + Math.min(checks.titleLength/60*100,100) + '%;background-color:' + (checks.isTitleLengthGood ? '#10b981' : '#f59e0b')"
                        ></div>
                    </div>
                </li>
                <!-- Meta Description length -->
                <li class="flex flex-col gap-1">
                    <div class="flex items-start gap-2">
                        <span x-show="checks.isDescLengthGood" class="text-emerald-500 text-sm shrink-0">✔️</span>
                        <span x-show="!checks.isDescLengthGood" class="text-rose-500 text-sm shrink-0">❌</span>
                        <span>Độ dài Meta Description: <span class="font-bold" x-text="checks.descLength"></span>/160 ký tự (140-160) (5đ)</span>
                    </div>
                    <div class="ml-6 h-1 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                             :style="'width:' + Math.min(checks.descLength/160*100,100) + '%;background-color:' + (checks.isDescLengthGood ? '#10b981' : '#f59e0b')"
                        ></div>
                    </div>
                </li>
                <!-- CTA check -->
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasCta" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasCta" class="text-rose-500 text-sm">❌</span>
                    <span>Meta Description chứa từ kêu gọi hành động (CTA) (5đ)</span>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Kỹ thuật (10đ)</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                <li class="flex items-start gap-2">
                    <span x-show="checks.isSlugLengthGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isSlugLengthGood" class="text-rose-500 text-sm">❌</span>
                    <span>Độ dài Slug URL hợp lý (dưới 80 ký tự) (3đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="!isServerDuplicateTitle" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="isServerDuplicateTitle" class="text-rose-500 text-sm">❌</span>
                    <span>SEO Title độc nhất (không trùng lặp) (2đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="!isServerDuplicateDesc" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="isServerDuplicateDesc" class="text-rose-500 text-sm">❌</span>
                    <span>Meta Description độc nhất (không trùng lặp) (2đ)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasSchema" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasSchema" class="text-rose-500 text-sm">❌</span>
                    <span>Đã chọn cấu hình loại Schema JSON-LD (3đ)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab 4: Google & Social Previews -->
    <div x-show="activeTab === 'preview'" class="space-y-4 text-slate-700">
        <!-- Preview switcher -->
        <div class="flex border-b border-slate-100 text-[10px] font-bold gap-1 bg-slate-100 p-1 rounded-lg">
            <button type="button" @click="previewType = 'google'" class="px-2.5 py-1 rounded transition-all shrink-0"
                    :class="previewType === 'google' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'">Google</button>
            <button type="button" @click="previewType = 'facebook'" class="px-2.5 py-1 rounded transition-all shrink-0"
                    :class="previewType === 'facebook' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'">Facebook</button>
            <button type="button" @click="previewType = 'twitter'" class="px-2.5 py-1 rounded transition-all shrink-0"
                    :class="previewType === 'twitter' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'">Twitter</button>
        </div>

        <!-- Google Search Snippet Card -->
        <div x-show="previewType === 'google'" class="p-3 bg-white border border-slate-200 rounded-lg space-y-1 text-left">
            <div class="text-[10px] text-slate-500 flex items-center gap-1">
                <span>{{ rtrim(config('app.url'), '/') }}</span>
                <span>&rsaquo;</span>
                <span x-text="seoSlug || slug || 'url-bai-viet'"></span>
            </div>
            <a href="#" class="text-sm font-bold text-blue-800 hover:underline block leading-snug"
               x-text="title || mainTitle || 'Vui lòng nhập Meta Title...'"></a>
            <p class="text-[11px] text-slate-600 leading-normal"
               x-text="description || 'Vui lòng cấu hình Meta Description để xem trước mô tả kết quả tìm kiếm trên Google tại đây...'"></p>
        </div>

        <!-- Facebook Feed Share Card -->
        <div x-show="previewType === 'facebook'" class="bg-white border border-slate-200 rounded-lg overflow-hidden text-left shadow-sm">
            <div class="bg-slate-100 aspect-[1.91/1] flex items-center justify-center relative">
                <template x-if="getImageUrl(ogImage)">
                    <img :src="getImageUrl(ogImage)" class="w-full h-full object-cover">
                </template>
                <template x-if="!getImageUrl(ogImage)">
                    <div class="p-4 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                        Chưa chọn ảnh Open Graph
                    </div>
                </template>
            </div>
            <div class="p-3 bg-[#f2f3f5] border-t border-slate-200">
                <div class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">{{ strtoupper(parse_url(config('app.url'), PHP_URL_HOST) ?? 'DAKHOACANTHO.VN') }}</div>
                <h4 class="font-bold text-xs text-slate-900 mt-1 line-clamp-1" x-text="ogTitle || title || mainTitle || 'Tiêu đề Facebook...'"></h4>
                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2" x-text="ogDesc || description || 'Mô tả ngắn Facebook...'"></p>
            </div>
        </div>

        <!-- Twitter Card -->
        <div x-show="previewType === 'twitter'" class="bg-white border border-slate-200 rounded-xl overflow-hidden text-left shadow-sm">
            <div class="bg-slate-100 aspect-[2/1] flex items-center justify-center relative">
                <template x-if="getImageUrl(twitterImage)">
                    <img :src="getImageUrl(twitterImage)" class="w-full h-full object-cover">
                </template>
                <template x-if="!getImageUrl(twitterImage)">
                    <div class="p-4 text-center text-xs text-slate-400 font-bold uppercase tracking-wider">
                        Chưa chọn ảnh Twitter
                    </div>
                </template>
            </div>
            <div class="p-3">
                <div class="text-[10px] text-slate-400">{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'dakhoacantho.vn' }}</div>
                <h4 class="font-bold text-xs text-slate-900 mt-0.5 line-clamp-1" x-text="twitterTitle || title || mainTitle || 'Tiêu đề Twitter...'"></h4>
                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2" x-text="twitterDesc || description || 'Mô tả Twitter...'"></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('seoScorecard', (config) => ({
        // Reactive state - bound from constructor config
        title: config.title,
        mainTitle: config.mainTitle,
        description: config.description,
        keyword: config.keyword,
        content: config.content,
        slug: config.slug,
        seoSlug: config.seoSlug,
        canonical: config.canonical,
        robotsIndex: config.robotsIndex,
        robotsFollow: config.robotsFollow,
        isPublished: config.isPublished,
        ogTitle: config.ogTitle,
        ogDesc: config.ogDesc,
        ogImage: config.ogImage,
        twitterTitle: config.twitterTitle,
        twitterDesc: config.twitterDesc,
        twitterImage: config.twitterImage,
        serverChecksRaw: config.serverChecksRaw,
        schemaType: config.schemaType,

        // UI state
        activeTab: 'basic',
        previewType: 'google',

        init() {
            // Reactive state bound via constructor config parameter
        },

        // --- Helpers ---
        toStr(val) {
            if (!val) return '';
            if (typeof val === 'string') return val;
            if (typeof val === 'object') {
                if (Array.isArray(val)) {
                    return val.length > 0 ? this.toStr(val[0]) : '';
                }
                // Tiptap JSON fallback
                if (val.type === 'doc' && Array.isArray(val.content)) {
                    return this.tiptapToText(val);
                }
                let str = String(val);
                if (str === '[object Object]') {
                    return '';
                }
                return str;
            }
            return String(val);
        },

        getImageUrl(img) {
            let path = this.toStr(img);
            if (!path) return '';
            if (path.startsWith('http://') || path.startsWith('https://')) {
                return path;
            }
            return '/storage/' + path.replace(/^\/+/, '');
        },

        // Extract plain text from Tiptap JSON doc
        tiptapToText(node) {
            if (!node) return '';
            if (node.type === 'text') return node.text || '';
            if (Array.isArray(node.content)) {
                return node.content.map(n => this.tiptapToText(n)).join(' ');
            }
            return '';
        },

        // Extract HTML-like string from Tiptap JSON
        tiptapToHtml(node) {
            if (!node) return '';
            if (typeof node === 'string') return node;
            if (typeof node !== 'object') return '';
            if (node.type === 'doc' && Array.isArray(node.content)) {
                return node.content.map(child => {
                    let tag = child.type === 'heading'
                        ? 'h' + (child.attrs?.level || 2)
                        : (child.type === 'paragraph' ? 'p' : child.type);
                    let inner = Array.isArray(child.content)
                        ? child.content.map(n => this.tiptapToText(n)).join('')
                        : '';
                    return '<' + tag + '>' + inner + '</' + tag + '>';
                }).join('');
            }
            return '';
        },

        removeAccents(str) {
            if (!str) return '';
            var unicode = {
                'a': 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
                'd': 'đ',
                'e': 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
                'i': 'í|ì|ỉ|ĩ|ị',
                'o': 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
                'u': 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
                'y': 'ý|ỳ|ỷ|ỹ|ỵ',
                'A': 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
                'D': 'Đ',
                'E': 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
                'I': 'Í|Ì|Ỉ|Ĩ|Ị',
                'O': 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
                'U': 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
                'Y': 'Ý|À|Ỷ|Ỹ|Ỵ'
            };
            for (var nonUnicode in unicode) {
                var uni = unicode[nonUnicode];
                str = str.replace(new RegExp(uni, 'gi'), nonUnicode);
            }
            return str;
        },

        get parsedServerChecks() {
            if (!this.serverChecksRaw) return null;
            try {
                return typeof this.serverChecksRaw === 'string'
                    ? JSON.parse(this.serverChecksRaw)
                    : this.serverChecksRaw;
            } catch (e) {
                return null;
            }
        },

        get isServerDuplicateTitle() {
            let pc = this.parsedServerChecks;
            if (!pc || !pc.groups) return false;
            let group = pc.groups.find(g => g.name === 'Kỹ thuật');
            if (!group) return false;
            let dup = group.checks.find(c => c.key === 'unique_seo_title');
            return dup && dup.status === 'fail';
        },

        get isServerDuplicateDesc() {
            let pc = this.parsedServerChecks;
            if (!pc || !pc.groups) return false;
            let group = pc.groups.find(g => g.name === 'Kỹ thuật');
            if (!group) return false;
            let dup = group.checks.find(c => c.key === 'unique_meta_description');
            return dup && dup.status === 'fail';
        },

        get checks() {
            let keywordVal   = this.toStr(this.keyword).trim();
            let titleVal     = this.toStr(this.mainTitle).trim();
            let metaTitleVal = this.toStr(this.title).trim();
            let descVal      = this.toStr(this.description).trim();
            let slugVal      = this.toStr(this.seoSlug || this.slug).trim();

            let rawContent  = this.content;
            let contentHtml = '';
            if (rawContent && typeof rawContent === 'object' && rawContent.type === 'doc') {
                contentHtml = this.tiptapToHtml(rawContent);
            } else if (typeof rawContent === 'string') {
                contentHtml = rawContent;
            }
            let contentVal = contentHtml;
            let kwLower = keywordVal.toLowerCase();

            // A. Basic Checks
            let hasKeyword        = !!keywordVal;
            let isTitleMainKwGood = hasKeyword && titleVal.toLowerCase().includes(kwLower);
            let isTitleKwGood     = hasKeyword && metaTitleVal.toLowerCase().includes(kwLower);
            let isDescKwGood      = hasKeyword && descVal.toLowerCase().includes(kwLower);

            let slugKw       = kwLower.replace(/\s+/g, '-');
            let cleanSlugKw  = this.removeAccents(slugKw);
            let cleanSlugVal = this.removeAccents(slugVal);
            let isSlugKwGood     = hasKeyword && cleanSlugVal.toLowerCase().includes(cleanSlugKw.toLowerCase());
            let isCanonicalGood   = true; // Canonical always valid fallback

            // B. Title & Meta
            let articleTitleLength          = titleVal.length;
            let isArticleTitleLengthGood    = articleTitleLength >= 40 && articleTitleLength <= 70;
            let titleLength                 = metaTitleVal.length;
            let isTitleLengthGood           = titleLength >= 50 && titleLength <= 60;
            let descLength                  = descVal.length;
            let isDescLengthGood            = descLength >= 140 && descLength <= 160;

            let ctaWords = ['ngay', 'nhanh chóng', 'an toàn', 'uy tín', 'hiệu quả', 'chi tiết', 'cam kết', 'tư vấn', 'miễn phí', 'liên hệ', 'click', 'xem'];
            let hasCta = ctaWords.some(word => descVal.toLowerCase().includes(word));

            // C. Content Checks
            let cleanText    = contentVal.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            let cleanWords   = cleanText.split(/\s+/).filter(w => w.length > 0);
            let wordCount    = cleanWords.length;
            let isWordCountGood = wordCount >= 800;

            let hasH2 = /<h2[^>]*>/i.test(contentVal);
            let h2Matches = contentVal.match(/<h2[^>]*>/ig) || [];
            let h3Matches = contentVal.match(/<h3[^>]*>/ig) || [];
            let totalHeadings = h2Matches.length + h3Matches.length;
            let isHeadingsStructureGood = totalHeadings >= 2 && totalHeadings <= 6;

            let keywordCount = 0;
            if (hasKeyword && cleanText) {
                let escaped = kwLower.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                let matches = cleanText.toLowerCase().match(new RegExp(escaped, 'g'));
                keywordCount = matches ? matches.length : 0;
            }
            let density      = wordCount > 0 ? (keywordCount / wordCount) * 100 : 0;
            let isDensityGood = density >= 0.5 && density <= 2.5;

            let first150Words = cleanWords.slice(0, 150).join(' ');
            let isKeywordFirst150Good = hasKeyword && first150Words.includes(kwLower);

            // D. Links & Images
            let hasThumbnail = !!this.$wire.get('data.thumbnail_image');
            let imgMatches       = contentVal.match(/<img[^>]+>/ig) || [];
            let totalContentImgs = imgMatches.length;
            let hasContentImage = totalContentImgs > 0;

            let missingAlt = false;
            if (totalContentImgs > 0) {
                imgMatches.forEach(function(img) {
                    if (!/alt=('[^']*'|"[^"]*")/i.test(img)) {
                        missingAlt = true;
                    }
                });
            }
            let hasAltOnImages = totalContentImgs > 0 && !missingAlt;

            let linkMatches = contentVal.match(/href=('[^']*'|"[^"]*")/ig) || [];
            let hasInternal = false;
            let hasExternal = false;
            linkMatches.forEach(function(link) {
                let m = link.match(/href=['"]([^'"]+)['"]/i);
                if (m && m[1]) {
                    let href = m[1];
                    if (href.startsWith('/') || href.includes(window.location.host)) {
                        hasInternal = true;
                    } else if (href.startsWith('http') || href.startsWith('https')) {
                        hasExternal = true;
                    }
                }
            });

            // E. Technical
            let slugLength = slugVal.length;
            let isSlugLengthGood = slugLength < 80;
            let schemaVal = this.toStr(this.schemaType).toLowerCase();
            let hasSchema = schemaVal !== 'none' && schemaVal !== '';

            return {
                hasKeyword, isTitleMainKwGood, isTitleKwGood, isDescKwGood, isSlugKwGood, isCanonicalGood,
                articleTitleLength, isArticleTitleLengthGood, titleLength, isTitleLengthGood, descLength, isDescLengthGood, hasCta,
                wordCount, isWordCountGood, hasH2, isHeadingsStructureGood, totalHeadings, density, isDensityGood, isKeywordFirst150Good,
                hasThumbnail, hasContentImage, hasAltOnImages, totalContentImgs, hasInternal, hasExternal,
                slugLength, isSlugLengthGood, hasSchema
            };
        },

        get score() {
            let total = 0;
            let c = this.checks;
            if (!c.hasKeyword) return 0;

            // A. SEO CƠ BẢN (30đ)
            total += 5; // Có từ khóa chính
            if (c.isTitleMainKwGood) total += 5;
            if (c.isTitleKwGood)     total += 5;
            if (c.isDescKwGood)      total += 5;
            if (c.isSlugKwGood)      total += 5;
            if (c.isCanonicalGood)   total += 5;

            // B. TIÊU ĐỀ & META (20đ)
            if (c.isArticleTitleLengthGood) total += 5;
            if (c.isTitleLengthGood)        total += 5;
            if (c.isDescLengthGood)         total += 5;
            if (c.hasCta)                   total += 5;

            // C. NỘI DUNG (25đ)
            if (c.isWordCountGood)          total += 5;
            if (c.hasH2)                    total += 5;
            if (c.isHeadingsStructureGood)  total += 5;
            if (c.isDensityGood)            total += 5;
            if (c.isKeywordFirst150Good)    total += 5;

            // D. LIÊN KẾT & HÌNH ẢNH (15đ)
            if (c.hasThumbnail)             total += 3;
            if (c.hasContentImage)          total += 3;
            if (c.hasAltOnImages)           total += 3;
            if (c.hasInternal)              total += 3;
            if (c.hasExternal)              total += 3;

            // E. KỸ THUẬT (10đ)
            if (c.isSlugLengthGood)         total += 3;
            if (!this.isServerDuplicateTitle) total += 2;
            if (!this.isServerDuplicateDesc)  total += 2;
            if (c.hasSchema)                total += 3;

            return Math.min(Math.round(total), 100);
        },

        get scoreColor() {
            if (this.score >= 75) return 'border-emerald-500 bg-emerald-50/30 text-emerald-950 dark:bg-emerald-950/10 dark:border-emerald-800';
            if (this.score >= 50) return 'border-amber-500 bg-amber-50/30 text-amber-950 dark:bg-amber-950/10 dark:border-amber-800';
            return 'border-rose-500 bg-rose-50/30 text-rose-950 dark:bg-rose-950/10 dark:border-rose-800';
        },

        get publishState() {
            if (!this.isPublished) return null;
            if (this.score >= 75) return { text: 'Bài viết đã sẵn sàng xuất bản', bg: 'bg-emerald-500 text-white', icon: '✅' };
            if (this.score >= 50) return { text: 'Cảnh báo: Điểm SEO trung bình, nên tối ưu thêm trước khi công khai.', bg: 'bg-amber-500 text-white', icon: '⚠️' };
            return { text: 'Cảnh báo mạnh: Điểm SEO quá thấp! Hãy tối ưu thêm trước khi xuất bản.', bg: 'bg-rose-600 text-white animate-pulse', icon: '🚨' };
        }
    }));
});
</script>
