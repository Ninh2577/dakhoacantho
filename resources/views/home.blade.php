@extends('layouts.app')

@section('title', \App\Models\Setting::site('clinic_name') . ' | Uy Tín - Tận Tâm')

@section('meta')
    <x-seo 
        title="{{ \App\Models\Setting::site('clinic_name') }} | Uy Tín - Tận Tâm" 
        description="{{ \App\Models\Setting::site('clinic_name') }} - Địa chỉ khám chữa bệnh uy tín, riêng tư và chuyên nghiệp hàng đầu tại Cần Thơ. Đăng ký tư vấn trực tuyến nhanh chóng." 
        canonical="{{ url('/') }}"
        :breadcrumbs="[['name' => 'Trang chủ', 'url' => url('/')]]"
    />
@endsection

@section('content')
<!-- Hero Section -->
<section id="gioi-thieu" class="bg-gradient-to-br from-blue-50 via-white to-teal-50/30 py-12 md:py-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Info -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-clinic-sky/10 text-clinic-blue uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-clinic-teal animate-ping"></span>
                    Hỗ trợ tư vấn 24/7
                </span>
                
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight">
                    Chăm sóc sức khỏe <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-blue to-clinic-teal">toàn diện</span> cho gia đình bạn
                </h1>
                
                <p class="text-slate-600 text-sm sm:text-base md:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0 font-semibold">
                    Đội ngũ tư vấn giàu kinh nghiệm cùng trang thiết bị y tế hỗ trợ hiện đại, mang lại quy trình thăm khám nhanh chóng, hiệu quả và bảo mật tuyệt đối thông tin khách hàng.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    @php
                        $zaloUrl = \App\Models\Setting::site('zalo_url') ?: 'https://zalo.me/0966332352';
                    @endphp
                    <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all text-sm tracking-wide">
                        Đặt lịch tư vấn ngay
                    </a>
                    <a href="tel:{{ preg_replace('/\D/', '', \App\Models\Setting::site('hotline')) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white border border-slate-200 text-clinic-blue hover:bg-slate-50 font-extrabold rounded-xl text-sm transition-all shadow-sm">
                        Gọi tư vấn: {{ \App\Models\Setting::site('hotline') }}
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-100 max-w-lg mx-auto lg:mx-0">
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl sm:text-3xl font-extrabold text-clinic-blue">15+</span>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Năm hoạt động</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl sm:text-3xl font-extrabold text-clinic-blue">20k+</span>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Lượt hỗ trợ</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-2xl sm:text-3xl font-extrabold text-clinic-blue">20+</span>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Phòng tư vấn</span>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div class="lg:col-span-5 relative flex justify-center lg:justify-end">
                <!-- Background decorative shapes -->
                <div class="absolute -inset-4 bg-teal-400/10 rounded-full blur-3xl -z-10 animate-pulse"></div>
                <div class="absolute inset-10 bg-clinic-blue/10 rounded-full blur-3xl -z-10"></div>
                
                <div class="relative max-w-sm sm:max-w-md lg:max-w-none rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-white">
                    <img src="{{ asset('images/doctor.webp') }}" alt="{{ \App\Models\Setting::site('clinic_name') }}" class="w-full h-auto object-cover max-h-[500px]" decoding="async" fetchpriority="high">
                    <div class="absolute bottom-4 left-4 right-4 p-4 bg-white/90 backdrop-blur-md rounded-2xl border border-white/50 shadow-lg flex items-center gap-3">
                        <span class="p-2.5 bg-clinic-teal text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </span>
                        <div>
                            <span class="block text-xs font-extrabold text-slate-900">Chuyên nghiệp & An toàn</span>
                            <span class="block text-[10px] text-slate-500 font-bold uppercase tracking-wide">Tiêu chuẩn chất lượng</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<x-home.services-banner />

<!-- Vì sao chọn Đa Khoa Cần Thơ? Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Vì sao chọn Đa Khoa Cần Thơ?</h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Chúng tôi nỗ lực tối đa mang lại dịch vụ y tế an tâm, riêng tư và hiệu quả cho từng khách hàng.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Box 1 -->
            <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-6 text-center space-y-3">
                <span class="w-12 h-12 rounded-full bg-clinic-blue/10 text-clinic-blue flex items-center justify-center mx-auto text-xl font-bold">🔒</span>
                <h3 class="font-extrabold text-slate-900 text-base">Bảo mật thông tin</h3>
                <p class="text-slate-550 text-xs leading-relaxed font-semibold">Quy trình lưu trữ và mã hóa hồ sơ bệnh án nghiêm ngặt, cam kết giữ kín quyền riêng tư của khách hàng.</p>
            </div>
            <!-- Box 2 -->
            <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-6 text-center space-y-3">
                <span class="w-12 h-12 rounded-full bg-clinic-blue/10 text-clinic-blue flex items-center justify-center mx-auto text-xl font-bold">📋</span>
                <h3 class="font-extrabold text-slate-900 text-base">Quy trình rõ ràng</h3>
                <p class="text-slate-550 text-xs leading-relaxed font-semibold">Không cần bốc số chờ đợi lâu, bạn chủ động sắp xếp thời gian thăm khám theo nhu cầu.</p>
            </div>
            <!-- Box 3 -->
            <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-6 text-center space-y-3">
                <span class="w-12 h-12 rounded-full bg-clinic-blue/10 text-clinic-blue flex items-center justify-center mx-auto text-xl font-bold">🔬</span>
                <h3 class="font-extrabold text-slate-900 text-base">Thiết bị hiện đại</h3>
                <p class="text-slate-550 text-xs leading-relaxed font-semibold">Hệ thống máy xét nghiệm, siêu âm chất lượng cao, hỗ trợ phân tích kết quả chính xác.</p>
            </div>
            <!-- Box 4 -->
            <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-6 text-center space-y-3">
                <span class="w-12 h-12 rounded-full bg-clinic-blue/10 text-clinic-blue flex items-center justify-center mx-auto text-xl font-bold">💬</span>
                <h3 class="font-extrabold text-slate-900 text-base">Tư vấn trực tuyến</h3>
                <p class="text-slate-550 text-xs leading-relaxed font-semibold">Đội ngũ hỗ trợ liên tục giải đáp các lo lắng về triệu chứng bệnh qua điện thoại hoặc Zalo.</p>
            </div>
        </div>
    </div>
</section>

<!-- Patient Reviews Section ("Ý kiến từ bệnh nhân") -->
<style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    .scrollbar-none::-webkit-scrollbar {
        display: none;
    }
    /* Hide scrollbar for IE, Edge and Firefox */
    .scrollbar-none {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
<section class="py-16 md:py-24 bg-[#0a3875] text-white" role="region" aria-label="Ý kiến đánh giá từ bệnh nhân">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12 md:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-white">Ý kiến từ bệnh nhân</h2>
            <p class="text-slate-200 text-sm sm:text-base leading-relaxed">
                Niềm tin và sự hồi phục của người bệnh là thước đo chính xác nhất cho uy tín của chúng tôi.
            </p>
        </div>

        @php
            $reviews = [
                [
                    'name' => 'Anh Nguyễn V. T.',
                    'location' => 'Cần Thơ',
                    'avatar' => 'AT',
                    'stars' => 5,
                    'content' => 'Dịch vụ rất chuyên nghiệp, đội ngũ nhân viên tư vấn tận tình, không làm tôi cảm thấy e ngại. Cơ sở vật chất rất hiện đại và sạch sẽ.',
                    'category' => 'Khám tổng quát'
                ],
                [
                    'name' => 'Chị Trần H. M.',
                    'location' => 'Vĩnh Long',
                    'avatar' => 'TM',
                    'stars' => 5,
                    'content' => 'Tôi đã điều trị trĩ tại đây bằng phương pháp HCPT, rất nhanh chóng và nhẹ nhàng. Rất cảm ơn đội ngũ hỗ trợ tại phòng khám.',
                    'category' => 'Hậu môn - Trực tràng'
                ],
                [
                    'name' => 'Chị Lê T. B.',
                    'location' => 'Sóc Trăng',
                    'avatar' => 'LB',
                    'stars' => 5,
                    'content' => 'Thủ tục nhanh gọn, bảo mật thông tin tuyệt đối. Chi phí cũng rất rõ ràng, không có phí ẩn trong quá trình điều trị.',
                    'category' => 'Bảo mật thông tin'
                ],
                [
                    'name' => 'Anh Hoàng M. D.',
                    'location' => 'Cần Thơ',
                    'avatar' => 'HD',
                    'stars' => 5,
                    'content' => 'Điều trị viêm bao quy đầu ở đây rất hiệu quả. Bác sĩ tư vấn kín đáo, nhiệt tình giải thích nên tôi thấy thoải mái, không lo lắng nữa.',
                    'category' => 'Nam khoa'
                ],
                [
                    'name' => 'Chị Nguyễn T. K.',
                    'location' => 'Hậu Giang',
                    'avatar' => 'NK',
                    'stars' => 5,
                    'content' => 'Tôi khám phụ khoa định kỳ ở đây. Phòng khám sạch sẽ, trang thiết bị mới, bác sĩ nữ rất nhẹ nhàng và chu đáo.',
                    'category' => 'Phụ khoa'
                ],
                [
                    'name' => 'Anh Phan Văn N.',
                    'location' => 'Vĩnh Long',
                    'avatar' => 'PN',
                    'stars' => 5,
                    'content' => 'Xét nghiệm máu ở đây nhanh chóng, không phải chờ đợi lâu như bệnh viện công. Nhân viên lấy mẫu nhẹ nhàng, kết quả trả về điện thoại tiện lợi.',
                    'category' => 'Xét nghiệm'
                ],
                [
                    'name' => 'Cô Lâm Thị S.',
                    'location' => 'Sóc Trăng',
                    'avatar' => 'LS',
                    'stars' => 5,
                    'content' => 'Quy trình khám sức khỏe tổng quát khoa học, hướng dẫn tận tình từ lúc đăng ký đến lúc lấy thuốc. Tôi rất hài lòng.',
                    'category' => 'Khám tổng quát'
                ],
                [
                    'name' => 'Anh Bùi Quốc V.',
                    'location' => 'Kiên Giang',
                    'avatar' => 'QV',
                    'stars' => 5,
                    'content' => 'Được đội ngũ tư vấn online giải thích kỹ về các triệu chứng trước khi đến khám. Tiết kiệm thời gian và giúp tôi yên tâm hơn.',
                    'category' => 'Tư vấn sức khỏe'
                ],
                [
                    'name' => 'Chị Huỳnh Mai L.',
                    'location' => 'Đồng Tháp',
                    'avatar' => 'ML',
                    'stars' => 5,
                    'content' => 'Thái độ phục vụ của nhân viên y tế rất lịch sự, nhiệt tình hướng dẫn bệnh nhân. Cảm thấy được tôn trọng khi thăm khám tại đây.',
                    'category' => 'Thái độ phục vụ'
                ],
                [
                    'name' => 'Anh Đỗ Tiến D.',
                    'location' => 'Bạc Liêu',
                    'avatar' => 'TD',
                    'stars' => 5,
                    'content' => 'Phòng khám khang trang, máy móc hiện đại và phòng chờ rất mát mẻ, sạch sẽ. Cảm giác rất thoải mái.',
                    'category' => 'Cơ sở vật chất'
                ],
                [
                    'name' => 'Anh Trương Minh K.',
                    'location' => 'An Giang',
                    'avatar' => 'MK',
                    'stars' => 5,
                    'content' => 'Đặt lịch hẹn trước qua mạng nên khi đến được vào khám ngay, không phải xếp hàng chờ đợi. Quy trình rất chuyên nghiệp.',
                    'category' => 'Quy trình khám'
                ],
                [
                    'name' => 'Anh Nguyễn Văn P.',
                    'location' => 'Cà Mau',
                    'avatar' => 'VP',
                    'stars' => 5,
                    'content' => 'Thông tin bệnh án được bảo mật tuyệt đối làm tôi rất an tâm khi khám các bệnh nhạy cảm. Rất chuyên nghiệp.',
                    'category' => 'Bảo mật thông tin'
                ],
                [
                    'name' => 'Chị Vũ Thị H.',
                    'location' => 'Trà Vinh',
                    'avatar' => 'VH',
                    'stars' => 5,
                    'content' => 'Bác sĩ dặn dò kỹ lưỡng chế độ ăn uống sau tiểu phẫu. Nhân viên còn gọi điện hỏi thăm tình hình hồi phục, chăm sóc chu đáo.',
                    'category' => 'Chăm sóc sau khám'
                ],
                [
                    'name' => 'Anh Trần Tấn L.',
                    'location' => 'Cần Thơ',
                    'avatar' => 'TL',
                    'stars' => 5,
                    'content' => 'Cắt bao quy đầu bằng công nghệ mới ở đây phục hồi nhanh, không đau. Chi phí hợp lý và công khai rõ ràng.',
                    'category' => 'Nam khoa'
                ],
                [
                    'name' => 'Chị Lê Hồng V.',
                    'location' => 'Sóc Trăng',
                    'avatar' => 'HV',
                    'stars' => 5,
                    'content' => 'Khám phụ khoa ở đây bảo mật thông tin tốt, quy trình 1 bác sĩ - 1 bệnh nhân giúp tôi dễ dàng chia sẻ tình trạng của mình.',
                    'category' => 'Phụ khoa'
                ],
                [
                    'name' => 'Anh Phạm Minh T.',
                    'location' => 'Hậu Giang',
                    'avatar' => 'MT',
                    'stars' => 5,
                    'content' => 'Dịch vụ xét nghiệm nhanh gọn, kết quả chính xác, bác sĩ phân tích kỹ các chỉ số và đưa ra lời khuyên hữu ích.',
                    'category' => 'Xét nghiệm'
                ],
                [
                    'name' => 'Bác Nguyễn Văn B.',
                    'location' => 'Vĩnh Long',
                    'avatar' => 'VB',
                    'stars' => 5,
                    'content' => 'Bác sĩ lớn tuổi khám rất kỹ, tận tâm giải thích bệnh lý. Phòng khám có lối đi rộng rãi, sạch sẽ cho người lớn tuổi.',
                    'category' => 'Khám tổng quát'
                ],
                [
                    'name' => 'Chị Đặng Thúy A.',
                    'location' => 'Kiên Giang',
                    'avatar' => 'TA',
                    'stars' => 5,
                    'content' => 'Từ lễ tân đến bác sĩ ai cũng niềm nở, chu đáo. Cảm giác e ngại ban đầu bay biến ngay khi bước vào phòng khám.',
                    'category' => 'Thái độ phục vụ'
                ]
            ];
        @endphp

        <!-- Reviews Slider Component -->
        <div x-data="{
            currentPage: 0,
            total: {{ count($reviews) }},
            autoplayInterval: null,
            isPaused: false,
            itemsPerPage: 1,
            translatePx: 0,
            get totalPages() {
                return Math.ceil(this.total / this.itemsPerPage);
            },
            get pages() {
                let p = [];
                for (let i = 0; i < this.totalPages; i++) { p.push(i); }
                return p;
            },
            computeTranslate() {
                const track = this.$refs.sliderTrack;
                if (!track || track.children.length === 0) return;
                const card = track.children[0];
                const cardWidthWithGap = card.offsetWidth + parseInt(getComputedStyle(track).gap || 0);
                const itemIndex = this.currentPage * this.itemsPerPage;
                const targetCard = track.children[itemIndex];
                if (targetCard) {
                    this.translatePx = targetCard.offsetLeft;
                }
            },
            next() {
                this.currentPage = (this.currentPage + 1) % this.totalPages;
                this.$nextTick(() => this.computeTranslate());
            },
            prev() {
                this.currentPage = (this.currentPage - 1 + this.totalPages) % this.totalPages;
                this.$nextTick(() => this.computeTranslate());
            },
            goTo(index) {
                this.currentPage = index;
                this.$nextTick(() => this.computeTranslate());
            },
            updateItemsPerPage() {
                if (window.innerWidth >= 1280) {
                    this.itemsPerPage = 3;
                } else if (window.innerWidth >= 768) {
                    this.itemsPerPage = 2;
                } else {
                    this.itemsPerPage = 1;
                }
                if (this.currentPage >= this.totalPages) {
                    this.currentPage = Math.max(0, this.totalPages - 1);
                }
                this.$nextTick(() => this.computeTranslate());
            },
            startAutoplay() {
                this.stopAutoplay();
                this.autoplayInterval = setInterval(() => {
                    if (!this.isPaused) { this.next(); }
                }, 5000);
            },
            stopAutoplay() {
                if (this.autoplayInterval) {
                    clearInterval(this.autoplayInterval);
                    this.autoplayInterval = null;
                }
            },
            pause() { this.isPaused = true; },
            resume() { this.isPaused = false; },
            init() {
                this.updateItemsPerPage();
                this.visibilityHandler = () => {
                    if (document.hidden) { this.pause(); } else { this.resume(); }
                };
                document.addEventListener('visibilitychange', this.visibilityHandler);
                this.startAutoplay();
            }
        }"
        @resize.window.debounce.150ms="updateItemsPerPage()"
        @mouseenter="pause()"
        @mouseleave="resume()"
        @touchstart="pause()"
        @touchend="resume()"
        @touchcancel="resume()"
        @keydown.arrow-left="prev()"
        @keydown.arrow-right="next()"
        class="relative">

            <!-- Slider Outer: flex row with side nav buttons on desktop -->
            <div class="flex items-center gap-2 md:gap-4">

                <!-- Prev Button - Desktop/Tablet (left of slider) -->
                <button @click="prev()"
                        class="hidden md:flex shrink-0 w-11 h-11 rounded-full bg-white/10 backdrop-blur-md border border-white/10 items-center justify-center text-white hover:bg-white hover:text-[#0a3875] transition-all duration-200 shadow-md cursor-pointer z-20"
                        aria-label="Đánh giá trước">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <!-- Viewport Wrapper: overflow-hidden -->
                <div class="relative overflow-hidden flex-1 min-w-0" aria-live="polite">
                    <!-- Track: flex, transitions via translateX px -->
                    <div x-ref="sliderTrack"
                         class="flex gap-6 transition-transform duration-500 ease-out will-change-transform"
                         :style="'transform: translateX(-' + translatePx + 'px)'">

                        @foreach($reviews as $review)
                            <!-- Card slot: fixed width per breakpoint -->
                            <div class="shrink-0 flex flex-col"
                                 :style="itemsPerPage === 3
                                    ? 'width: calc((100%) / 3 - 16px)'
                                    : (itemsPerPage === 2
                                        ? 'width: calc((100%) / 2 - 12px)'
                                        : 'width: 100%')
                                 ">
                                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 flex flex-col h-full justify-between select-none">
                                    <div class="space-y-4 flex-1 flex flex-col">
                                        <!-- Star Rating -->
                                        <div class="flex text-teal-400">
                                            @for($i = 0; $i < $review['stars']; $i++)
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                        <!-- Review Content -->
                                        <p class="text-sm italic leading-relaxed text-slate-200 flex-1">
                                            "{{ $review['content'] }}"
                                        </p>
                                    </div>
                                    <!-- Patient Footer -->
                                    <div class="pt-4 border-t border-white/10 flex items-center gap-3 mt-4 shrink-0">
                                        <span class="w-8 h-8 rounded-full bg-teal-400 text-[#0a3875] font-black flex items-center justify-center text-xs shrink-0 select-none">
                                            {{ $review['avatar'] }}
                                        </span>
                                        <div>
                                            <span class="block text-xs font-bold text-white">{{ $review['name'] }}</span>
                                            <span class="block text-[10px] text-slate-300">{{ $review['location'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <!-- Next Button - Desktop/Tablet (right of slider) -->
                <button @click="next()"
                        class="hidden md:flex shrink-0 w-11 h-11 rounded-full bg-white/10 backdrop-blur-md border border-white/10 items-center justify-center text-white hover:bg-white hover:text-[#0a3875] transition-all duration-200 shadow-md cursor-pointer z-20"
                        aria-label="Đánh giá tiếp theo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

            </div>

            <!-- Pagination Dots + Mobile Navigation -->
            <div class="flex items-center justify-center gap-4 mt-8">
                <!-- Prev Button - Mobile only -->
                <button @click="prev()"
                        class="md:hidden w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-white active:bg-white active:text-[#0a3875] transition"
                        aria-label="Đánh giá trước">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <!-- Dots -->
                <div class="flex gap-2 flex-wrap justify-center">
                    <template x-for="(pageIndex, dotIdx) in pages" :key="dotIdx">
                        <button @click="goTo(pageIndex)"
                                class="rounded-full transition-all duration-300 cursor-pointer"
                                :class="currentPage === pageIndex
                                    ? 'w-6 h-2.5 bg-teal-400'
                                    : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/60'"
                                :aria-label="'Trang đánh giá ' + (dotIdx + 1)"
                                :aria-current="currentPage === pageIndex ? 'true' : 'false'">
                        </button>
                    </template>
                </div>

                <!-- Next Button - Mobile only -->
                <button @click="next()"
                        class="md:hidden w-9 h-9 rounded-full bg-white/10 border border-white/10 flex items-center justify-center text-white active:bg-white active:text-[#0a3875] transition"
                        aria-label="Đánh giá tiếp theo">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

        </div>

    </div>
</section>

@endsection
