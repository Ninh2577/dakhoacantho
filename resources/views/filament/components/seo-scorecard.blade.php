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
    serverChecksRaw: $wire.entangle('data.seo_checks')
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
                        :stroke="score >= 80 ? '#10b981' : (score >= 50 ? '#f59e0b' : '#ef4444')"
                        stroke-width="6"
                        stroke-linecap="round"
                        :stroke-dasharray="188.5"
                        :stroke-dashoffset="188.5 - (188.5 * score / 100)"
                        style="transition:stroke-dashoffset 0.5s ease;"
                    />
                </svg>
                <div class="absolute flex flex-col items-center leading-none">
                    <span class="text-lg font-black" x-text="score" :style="'color:' + (score >= 80 ? '#10b981' : (score >= 50 ? '#f59e0b' : '#ef4444'))"></span>
                    <span class="text-[9px] text-slate-400 font-semibold">/100</span>
                </div>
            </div>
        </div>

        <!-- Grade label -->
        <div class="mt-2 text-center text-xs font-bold"
             :class="score >= 80 ? 'text-emerald-600' : (score >= 50 ? 'text-amber-600' : 'text-rose-600')"
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
                :class="activeTab === 'content' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Danh sách kiểm tra</button>
        <button type="button" @click="activeTab = 'preview'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'preview' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Xem trước</button>
        <button type="button" @click="activeTab = 'advanced'" class="px-3 py-1.5 rounded-md transition-all shrink-0"
                :class="activeTab === 'advanced' ? 'bg-white shadow text-slate-900' : 'text-slate-500 hover:text-slate-900'">Nâng cao</button>
    </div>

    <!-- Tab 1: Basic SEO -->
    <div x-show="activeTab === 'basic'" class="space-y-3">
        <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider">SEO cơ bản</h4>
        <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
            <!-- Focus keyword -->
            <li class="flex items-start gap-2">
                <span x-show="checks.hasKeyword" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.hasKeyword" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa chính: <span x-text="keyword && typeof keyword === 'string' ? '«' + keyword + '»' : (keyword ? '«' + String(keyword) + '»' : 'Chưa nhập')"></span></span>
            </li>
            <!-- Meta Title length with counter bar -->
            <li class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <span x-show="checks.isTitleLengthGood" class="text-emerald-500 text-sm shrink-0">✔️</span>
                    <span x-show="!checks.isTitleLengthGood" class="text-rose-500 text-sm shrink-0">❌</span>
                    <span>Meta Title: <span class="font-bold" x-text="checks.titleLength"></span>/60 ký tự
                        <span x-show="checks.isTitleLengthGood" class="text-emerald-600">(Tốt)</span>
                        <span x-show="!checks.isTitleLengthGood && checks.titleLength > 0" class="text-amber-600">(50-60 là tối ưu)</span>
                    </span>
                </div>
                <div class="ml-6 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         :style="'width:' + Math.min(checks.titleLength/60*100,100) + '%;background-color:' + (checks.isTitleLengthGood ? '#10b981' : (checks.titleLength > 60 ? '#ef4444' : '#f59e0b'))"
                    ></div>
                </div>
            </li>
            <!-- Meta Description length with counter bar -->
            <li class="flex flex-col gap-1">
                <div class="flex items-start gap-2">
                    <span x-show="checks.isDescLengthGood" class="text-emerald-500 text-sm shrink-0">✔️</span>
                    <span x-show="!checks.isDescLengthGood" class="text-rose-500 text-sm shrink-0">❌</span>
                    <span>Meta Description: <span class="font-bold" x-text="checks.descLength"></span>/160 ký tự
                        <span x-show="checks.isDescLengthGood" class="text-emerald-600">(Tốt)</span>
                        <span x-show="!checks.isDescLengthGood && checks.descLength > 0" class="text-amber-600">(140-160 là tối ưu)</span>
                    </span>
                </div>
                <div class="ml-6 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300"
                         :style="'width:' + Math.min(checks.descLength/160*100,100) + '%;background-color:' + (checks.isDescLengthGood ? '#10b981' : (checks.descLength > 160 ? '#ef4444' : '#f59e0b'))"
                    ></div>
                </div>
            </li>
            <!-- Keyword in Meta Title -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isTitleKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isTitleKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa xuất hiện trong Meta Title</span>
            </li>
            <!-- Keyword in Meta Description -->
            <li class="flex items-start gap-2">
                <span x-show="checks.isDescKwGood" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!checks.isDescKwGood" class="text-rose-500 text-sm">❌</span>
                <span>Từ khóa xuất hiện trong Meta Description</span>
            </li>
            <!-- Duplicate meta warnings -->
            <li class="flex items-start gap-2">
                <span x-show="!isServerDuplicateTitle" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="isServerDuplicateTitle" class="text-amber-500 text-sm">⚠️</span>
                <span>Kiểm tra trùng lặp Meta Title (độc nhất)</span>
            </li>
            <li class="flex items-start gap-2">
                <span x-show="!isServerDuplicateDesc" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="isServerDuplicateDesc" class="text-amber-500 text-sm">⚠️</span>
                <span>Kiểm tra trùng lặp Meta Description (độc nhất)</span>
            </li>
        </ul>
    </div>

    <!-- Tab 2: Content Checklist -->
    <div x-show="activeTab === 'content'" class="space-y-4">
        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Tối ưu nội dung</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                <li class="flex items-start gap-2">
                    <span x-show="checks.isWordCountGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isWordCountGood" class="text-rose-500 text-sm">❌</span>
                    <span>Độ dài nội dung (tối thiểu 600 từ) - <span class="font-bold" x-text="checks.wordCount"></span> từ</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isTitleMainKwGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isTitleMainKwGood" class="text-rose-500 text-sm">❌</span>
                    <span>Từ khóa chính xuất hiện trong Tiêu đề chính (H1)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isKeywordFirst10Good" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isKeywordFirst10Good" class="text-rose-500 text-sm">❌</span>
                    <span>Từ khóa xuất hiện trong 10% đầu bài viết</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasH2" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasH2" class="text-rose-500 text-sm">❌</span>
                    <span>Nội dung có chứa thẻ tiêu đề phụ H2</span>
                </li>
                <li class="flex items-start gap-2">
                    <span x-show="checks.isDensityGood" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.isDensityGood" class="text-amber-500 text-sm">⚠️</span>
                    <span>Mật độ từ khóa chính (0.5% - 2.5%) - Hiện tại: <span class="font-bold" x-text="checks.density.toFixed(2) + '%'"></span> (<span x-text="checks.keywordCount"></span> lần)</span>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider mb-2.5">Hình ảnh & Liên kết</h4>
            <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
                <!-- Image tags -->
                <li class="flex items-start gap-2">
                    <span x-show="checks.totalContentImgs > 0 && checks.hasAltOnImages" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="checks.totalContentImgs > 0 && !checks.hasAltOnImages" class="text-amber-500 text-sm">⚠️</span>
                    <span x-show="checks.totalContentImgs === 0" class="text-rose-500 text-sm">❌</span>
                    <span>Ảnh trong nội dung (<span x-text="checks.totalContentImgs"></span>) có thuộc tính Alt</span>
                </li>
                <!-- Internal links -->
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasInternal" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasInternal" class="text-rose-500 text-sm">❌</span>
                    <span>Liên kết nội bộ (Internal Link)</span>
                </li>
                <!-- External links -->
                <li class="flex items-start gap-2">
                    <span x-show="checks.hasExternal" class="text-emerald-500 text-sm">✔️</span>
                    <span x-show="!checks.hasExternal" class="text-rose-500 text-sm">❌</span>
                    <span>Liên kết ngoài (External Link)</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab 3: Google & Social Previews -->
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

    <!-- Tab 4: Advanced -->
    <div x-show="activeTab === 'advanced'" class="space-y-3">
        <h4 class="text-xs font-extrabold uppercase text-slate-400 tracking-wider">Cấu hình nâng cao</h4>
        <ul class="space-y-2.5 text-xs text-slate-600 font-semibold">
            <!-- Canonical -->
            <li class="flex items-start gap-2">
                <span x-show="canonical" class="text-emerald-500 text-sm">✔️</span>
                <span x-show="!canonical" class="text-amber-500 text-sm">⚠️</span>
                <span>Thẻ Canonical: <span x-text="canonical ? 'Tự chọn' : 'Mặc định (Tự nhận URL bài viết)'"></span></span>
            </li>
            <!-- Robots Index -->
            <li class="flex items-start gap-2">
                <span class="text-emerald-500 text-sm">✔️</span>
                <span>Trạng thái Lập chỉ mục: <span class="font-bold" x-text="robotsIndex ? 'Index' : 'Noindex'"></span></span>
            </li>
            <!-- Robots Follow -->
            <li class="flex items-start gap-2">
                <span class="text-emerald-500 text-sm">✔️</span>
                <span>Trạng thái Liên kết: <span class="font-bold" x-text="robotsFollow ? 'Follow' : 'Nofollow'"></span></span>
            </li>
        </ul>
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
                // Tiptap JSON: {type:'doc', content:[...]}
                if (val.type === 'doc' && Array.isArray(val.content)) {
                    return this.tiptapToText(val);
                }
                // Check if Generic Object
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

        // Extract HTML-like string from Tiptap JSON for regex checks
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
            let basic = pc.groups.find(g => g.name === 'SEO cơ bản');
            if (!basic) return false;
            let dup = basic.checks.find(c => c.key === 'meta_title_duplicate');
            return dup && dup.status === 'warning';
        },

        get isServerDuplicateDesc() {
            let pc = this.parsedServerChecks;
            if (!pc || !pc.groups) return false;
            let basic = pc.groups.find(g => g.name === 'SEO cơ bản');
            if (!basic) return false;
            let dup = basic.checks.find(c => c.key === 'meta_description_duplicate');
            return dup && dup.status === 'warning';
        },

        get checks() {
            // Safe conversions: handle Tiptap JSON objects and null values
            let keywordVal   = this.toStr(this.keyword).trim();
            let titleVal     = this.toStr(this.title).trim();
            let mainTitleVal = this.toStr(this.mainTitle).trim();
            let descVal      = this.toStr(this.description).trim();
            let slugVal      = this.toStr(this.seoSlug || this.slug).trim();

            // Handle Tiptap JSON content
            let rawContent  = this.content;
            let contentHtml = '';
            if (rawContent && typeof rawContent === 'object' && rawContent.type === 'doc') {
                contentHtml = this.tiptapToHtml(rawContent);
            } else if (typeof rawContent === 'string') {
                contentHtml = rawContent;
            }
            let contentVal = contentHtml;

            let kwLower = keywordVal.toLowerCase();

            // Basic checks
            let hasKeyword        = !!keywordVal;
            let titleLength       = titleVal.length;
            let isTitleLengthGood = titleLength >= 50 && titleLength <= 60;
            let isTitleKwGood     = hasKeyword && titleVal.toLowerCase().includes(kwLower);

            let descLength        = descVal.length;
            let isDescLengthGood  = descLength >= 150 && descLength <= 160;
            let isDescKwGood      = hasKeyword && descVal.toLowerCase().includes(kwLower);

            let slugKw       = kwLower.replace(/\s+/g, '-');
            let cleanSlugKw  = this.removeAccents(slugKw);
            let cleanSlugVal = this.removeAccents(slugVal);
            let isSlugKwGood     = hasKeyword && cleanSlugVal.toLowerCase().includes(cleanSlugKw.toLowerCase());
            let isSlugFriendly   = slugVal.length <= 50 && /^[a-z0-9\-]+$/i.test(slugVal);

            // Content checks
            let cleanText    = contentVal.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
            let wordCount    = cleanText ? cleanText.split(/\s+/).length : 0;
            let isWordCountGood = wordCount >= 600;

            let isTitleMainKwGood = hasKeyword && mainTitleVal.toLowerCase().includes(kwLower);

            // Keyword in first 10%
            let first10PercentLimit = Math.max(Math.floor(cleanText.length * 0.1), 200);
            let first10PercentText  = cleanText.substring(0, first10PercentLimit);
            let isKeywordFirst10Good = hasKeyword && first10PercentText.toLowerCase().includes(kwLower);

            let hasH2 = /<h2[^>]*>/i.test(contentVal);
            let hasH3 = /<h3[^>]*>/i.test(contentVal);

            // Keyword count & density
            let keywordCount = 0;
            if (hasKeyword && cleanText) {
                let escaped = kwLower.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                let matches = cleanText.toLowerCase().match(new RegExp(escaped, 'g'));
                keywordCount = matches ? matches.length : 0;
            }
            let density      = wordCount > 0 ? (keywordCount / wordCount) * 100 : 0;
            let isDensityGood = density >= 0.5 && density <= 2.5;

            // Images — use regex that avoids double-quotes in the pattern itself
            let imgMatches       = contentVal.match(/<img[^>]+>/ig) || [];
            let totalContentImgs = imgMatches.length;
            let missingAlt       = false;
            if (totalContentImgs > 0) {
                imgMatches.forEach(function(img) {
                    // Check for alt attribute with a non-empty value
                    if (!/alt=('[^']*'|"[^"]*")/i.test(img)) {
                        missingAlt = true;
                    }
                });
            }
            let hasAltOnImages = totalContentImgs > 0 && !missingAlt;

            // Links checks — avoid literal double-quotes inside regex
            let linkMatches = contentVal.match(/href=('[^']*'|"[^"]*")/ig) || [];
            let hasInternal = false;
            let hasExternal = false;
            linkMatches.forEach(function(link) {
                // Extract the URL value (strip quotes)
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

            // Social SEO complete check
            let hasOgTitle      = !!this.toStr(this.ogTitle).trim();
            let hasOgDesc       = !!this.toStr(this.ogDesc).trim();
            let hasOgImg        = !!this.toStr(this.ogImage);
            let hasTwitterTitle = !!this.toStr(this.twitterTitle).trim();
            let hasTwitterDesc  = !!this.toStr(this.twitterDesc).trim();
            let hasTwitterImg   = !!this.toStr(this.twitterImage);
            let isSocialComplete = hasOgTitle && hasOgDesc && hasOgImg && hasTwitterTitle && hasTwitterDesc && hasTwitterImg;

            return {
                hasKeyword, titleLength, isTitleLengthGood, isTitleKwGood,
                descLength, isDescLengthGood, isDescKwGood,
                isSlugKwGood, isSlugFriendly,
                wordCount, isWordCountGood,
                isTitleMainKwGood, isKeywordFirst10Good,
                hasH2, hasH3,
                keywordCount, density, isDensityGood,
                totalContentImgs, hasAltOnImages,
                hasInternal, hasExternal, isSocialComplete
            };
        },

        get score() {
            let total = 0;
            let c = this.checks;
            if (!c.hasKeyword) return 0;

            total += 10; // 1. Focus Keyword

            // 2. Meta Title (15 pts)
            if (c.isTitleLengthGood) total += 7.5;
            if (c.isTitleKwGood)     total += 7.5;

            // 3. Meta Description (15 pts)
            if (c.isDescLengthGood) total += 7.5;
            if (c.isDescKwGood)     total += 7.5;

            // 4. Slug SEO (10 pts)
            if (c.isSlugKwGood)    total += 5;
            if (c.isSlugFriendly)  total += 5;

            // 5. Keyword placement & density (15 pts)
            if (c.isKeywordFirst10Good) total += 5;
            if (c.isTitleMainKwGood)    total += 5;
            if (c.isDensityGood)        total += 5;

            // 6. Content length & headings (15 pts)
            if (c.isWordCountGood) total += 7.5;
            if (c.hasH2)           total += 7.5;

            // 7. Image + alt (10 pts)
            let hasThumbnail = !!this.$wire.get('data.thumbnail_image');
            if (hasThumbnail) total += 5;
            if (c.totalContentImgs > 0 && c.hasAltOnImages) total += 5;

            // 8. Links (5 pts)
            if (c.hasInternal) total += 2.5;
            if (c.hasExternal) total += 2.5;

            // 9. Social SEO (5 pts)
            if (c.isSocialComplete) total += 5;

            return Math.min(Math.round(total), 100);
        },

        get scoreColor() {
            if (this.score >= 80) return 'border-emerald-500 bg-emerald-50/30 text-emerald-950 dark:bg-emerald-950/10 dark:border-emerald-800';
            if (this.score >= 50) return 'border-amber-500 bg-amber-50/30 text-amber-950 dark:bg-amber-950/10 dark:border-amber-800';
            return 'border-rose-500 bg-rose-50/30 text-rose-950 dark:bg-rose-950/10 dark:border-rose-800';
        },

        get publishState() {
            if (!this.isPublished) return null;
            if (this.score >= 80) return { text: 'Bài viết đã sẵn sàng xuất bản', bg: 'bg-emerald-500 text-white', icon: '✅' };
            if (this.score >= 50) return { text: 'Cảnh báo: Điểm SEO trung bình, nên tối ưu thêm trước khi công khai.', bg: 'bg-amber-500 text-white', icon: '⚠️' };
            return { text: 'Cảnh báo mạnh: Điểm SEO quá thấp! Hãy tối ưu thêm trước khi xuất bản.', bg: 'bg-rose-600 text-white animate-pulse', icon: '🚨' };
        }
    }));
});
</script>
