@extends('layouts.app')

@section('title', 'Vì Cộng Đồng - Trách Nhiệm Xã Hội & Giáo Dục Sức Khỏe | Đa Khoa Gia Phước')

@section('meta')
    <x-seo 
        title="Vì Cộng Đồng - Trách Nhiệm Xã Hội & Giáo Dục Sức Khỏe | Đa Khoa Gia Phước" 
        description="Hoạt động trách nhiệm xã hội, giáo dục sức khỏe học đường và lan tỏa thông tin phòng ngừa bệnh truyền nhiễm tại địa phương của Đa Khoa Gia Phước Cần Thơ." 
        canonical="{{ route('category.show', ['category_path' => 'vi-cong-dong']) }}"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Vì Cộng Đồng', 'url' => route('category.show', ['category_path' => 'vi-cong-dong'])]
        ]"
        :faqs="[
            ['q' => 'Các hoạt động cộng đồng của Đa Khoa Gia Phước là gì?', 'a' => 'Đa Khoa Gia Phước chủ yếu tập trung vào giáo dục sức khỏe cộng đồng, nâng cao nhận thức phòng chống bệnh truyền nhiễm, phổ biến các kiến thức y học và hướng dẫn giữ gìn vệ sinh học đường tại địa phương.'],
            ['q' => 'Tôi có thể đăng ký tham gia các chiến dịch nâng cao nhận thức sức khỏe không?', 'a' => 'Bạn có thể liên hệ trực tiếp với chúng tôi qua hotline 0966.332.352 để đăng ký nhận thông tin về các chương trình, hoạt động truyền thông và giáo dục sức khỏe cộng đồng sắp tới.'],
            ['q' => 'Phòng khám Gia Phước hỗ trợ bà con thế nào?', 'a' => 'Phòng khám định kỳ chia sẻ các tài liệu y tế, hướng dẫn phòng ngừa dịch bệnh miễn phí cho người dân và sẵn sàng hỗ trợ tư vấn sức khỏe qua tổng đài hotline chính thức.']
        ]"
    />
@endsection


@section('content')
<div class="bg-slate-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white overflow-hidden min-h-[500px] flex items-center">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=2000" 
                 alt="Vì Cộng Đồng" class="w-full h-full object-cover opacity-30 object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16 md:py-24">
            <div class="max-w-3xl">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6 text-xs md:text-sm text-slate-300 gap-2 items-center" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                    <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-slate-400">Vì Cộng Đồng</span>
                </nav>

                <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-400/30 uppercase tracking-widest mb-4">
                    Trách Nhiệm Xã Hội
                </span>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                    Vì Cộng Đồng: <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-sky to-clinic-teal">Chia Sẻ Yêu Thương</span>, <br>
                    Lan Tỏa Sức Khỏe
                </h1>

                <p class="text-slate-300 text-lg md:text-xl leading-relaxed mb-8 max-w-2xl">
                    Chúng tôi cam kết mang lại giá trị sức khỏe và hạnh phúc cho mọi người qua các hoạt động thiện nguyện, tư vấn y khoa miễn phí và giáo dục sức khỏe cộng đồng.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#activities-section" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all duration-200 text-base tracking-wide">
                        Xem Các Dự Án
                    </a>
                    <a href="#cta-section" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/40 font-extrabold rounded-xl text-base transition-all duration-200 backdrop-blur-sm">
                        Trở Thành Tình Nguyện Viên
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Hoạt Động Tiêu Biểu (Asymmetric Grid) -->
    <section id="activities-section" class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Hoạt Động Tiêu Biểu</h2>
                    <p class="text-slate-500 mt-2 max-w-xl">Kiến tạo những giá trị bền vững cho cộng đồng thông qua chuyên môn y khoa và sự tận tâm của tập thể y tế phòng khám Đa Khoa Gia Phước.</p>
                </div>
                <a href="#cta-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                    Xem tất cả hoạt động &rarr;
                </a>
            </div>

            <!-- Asymmetric Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Large Highlight Card (lg:col-span-2) -->
                <div class="lg:col-span-2 bg-slate-900 rounded-3xl overflow-hidden relative group min-h-[400px] flex flex-col justify-end shadow-sm">
                    <!-- Background image -->
                    <div class="absolute inset-0 z-0">
                        <img src="https://images.unsplash.com/photo-1469571486040-7a3081cde312?auto=format&fit=crop&q=80&w=1200" 
                             alt="Chiến dịch khám bệnh" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-60">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                    </div>

                    <!-- Text details overlay -->
                    <div class="p-8 relative z-10 space-y-3">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-clinic-blue text-white uppercase tracking-wider">
                            Hành Trình 2024
                        </span>
                        <h3 class="text-2xl md:text-3xl font-bold text-white leading-tight">
                            Chiến dịch Khám Sức Khỏe Miễn Phí: Hơn 500 Người Được Hỗ Trợ
                        </h3>
                        <p class="text-slate-200 text-sm leading-relaxed max-w-xl">
                            Mang dịch vụ y tế chất lượng cao đến vùng sâu vùng xa, hỗ trợ tầm soát bệnh lý và cấp phát thuốc miễn phí cho bà con nghèo hoàn cảnh khó khăn.
                        </p>
                    </div>
                </div>

                <!-- Column of 2 Stacked Smaller Cards (lg:col-span-1) -->
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <!-- Smaller Card 1 (light blue) -->
                    <div class="bg-clinic-sky/5 rounded-3xl border border-slate-100 p-8 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow group">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-clinic-sky/15 text-clinic-blue flex items-center justify-center mb-6">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-slate-900 mb-2">Giáo Dục Sức Khỏe Học Đường</h4>
                            <p class="text-slate-500 text-xs leading-relaxed mb-4">
                                Nâng cao nhận thức về vệ sinh học đường, phòng chống bệnh truyền nhiễm và dinh dưỡng khoa học cho các em nhỏ vùng cao.
                            </p>
                        </div>
                        <a href="#cta-section" class="text-clinic-blue text-xs font-bold hover:underline inline-flex items-center gap-1">
                            Tìm hiểu thêm &rarr;
                        </a>
                    </div>

                    <!-- Smaller Card 2 (dark blue) -->
                    <div class="bg-clinic-blue rounded-3xl p-8 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow text-white group">
                        <div>
                            <div class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center mb-6">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold mb-2">Quà Từ Thiện Hàng Tháng</h4>
                            <p class="text-white/80 text-xs leading-relaxed mb-4">
                                Phát tặng các phần quà nhu yếu phẩm và hỗ trợ viện phí cho các bệnh nhân có hoàn cảnh khó khăn đang điều trị nội trú.
                            </p>
                        </div>
                        <a href="#cta-section" class="text-white text-xs font-bold hover:underline inline-flex items-center gap-1">
                            Chi tiết &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Câu Chuyện Nhân Văn -->
    <section class="py-20 md:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Caring Image (Col 5) -->
                <div class="lg:col-span-5 relative">
                    <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/5] bg-slate-200">
                        <img src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?auto=format&fit=crop&q=80&w=600" 
                             alt="Human story" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Right: Content & Statistics (Col 7) -->
                <div class="lg:col-span-7 space-y-8">
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-clinic-blue uppercase tracking-wider">Góc nhìn người trong cuộc</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Lan Tỏa Kiến Thức Phòng Ngừa Dịch Bệnh
                        </h2>
                        <p class="text-slate-500 text-sm md:text-base leading-relaxed">
                            Bên cạnh công tác tư vấn y tế, Đa Khoa Gia Phước chú trọng hoạt động chia sẻ cẩm nang, tài liệu y khoa phòng chống dịch bệnh truyền nhiễm cho bà con địa phương nhằm xây dựng cộng đồng khỏe mạnh và chủ động bảo vệ bản thân.
                        </p>
                    </div>

                    <!-- Statistics grid -->
                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-slate-200">
                        <!-- Stat 1 -->
                        <div class="space-y-1">
                            <span class="block text-4xl md:text-5xl font-black text-clinic-teal tracking-tight">&check;</span>
                            <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Giáo dục sức khỏe học đường</span>
                        </div>

                        <!-- Stat 2 -->
                        <div class="space-y-1">
                            <span class="block text-4xl md:text-5xl font-black text-clinic-teal tracking-tight">&check;</span>
                            <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tài liệu phòng ngừa miễn phí</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 md:py-24 bg-white border-t border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Câu Hỏi Thường Gặp</h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
            </div>

            <div x-data="{ active: null }" class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-slate-50 rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Các hoạt động cộng đồng của Đa Khoa Gia Phước là gì?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-white flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 1 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-150 pt-4 bg-white">
                            Đa Khoa Gia Phước chủ yếu tập trung vào giáo dục sức khỏe cộng đồng, nâng cao nhận thức phòng chống bệnh truyền nhiễm, phổ biến các kiến thức y học và hướng dẫn giữ gìn vệ sinh học đường tại địa phương.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-slate-50 rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Tôi có thể đăng ký tham gia các chiến dịch nâng cao nhận thức sức khỏe không?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-white flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 2 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-150 pt-4 bg-white">
                            Bạn có thể liên hệ trực tiếp với chúng tôi qua hotline 0966.332.352 để đăng ký nhận thông tin về các chương trình, hoạt động truyền thông và giáo dục sức khỏe cộng đồng sắp tới.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-slate-50 rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Phòng khám Gia Phước hỗ trợ bà con thế nào?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-white flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 3 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-150 pt-4 bg-white">
                            Phòng khám định kỳ chia sẻ các tài liệu y tế, hướng dẫn phòng ngừa dịch bệnh miễn phí cho người dân và sẵn sàng hỗ trợ tư vấn sức khỏe qua tổng đài hotline chính thức.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles Section -->
    <x-related-articles-carousel
        title="Bài viết vì sức khỏe cộng đồng"
        subtitle="Chia sẻ kiến thức, hoạt động truyền thông sức khỏe và thông tin hữu ích cho cộng đồng."
        :articles="$relatedArticles"
        :viewAllUrl="route('categories.index')"
    />

    <!-- Bottom CTA Banner (Chung tay vì cộng đồng) -->
    <section id="cta-section" class="py-16 md:py-20 bg-gradient-to-br from-clinic-blue to-[#0b4c8c] text-white text-center relative overflow-hidden">
        <!-- background decor -->
        <div class="absolute inset-0 z-0 opacity-10">
            <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-white blur-2xl"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white blur-2xl"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 relative z-10 space-y-6">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">
                CHUNG TAY VÌ CỘNG ĐỒNG – ĐÓNG GÓP NGAY
            </h2>
            <p class="text-slate-200 text-sm md:text-base max-w-2xl mx-auto leading-relaxed font-medium">
                Mọi sự đóng góp của bạn, dù lớn hay nhỏ, đều góp phần tạo nên những thay đổi kỳ diệu cho những cuộc đời còn gặp nhiều khó khăn xung quanh ta.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-2">
                <a href="#cta-section" onclick="alert('Cổng quyên góp sẽ sớm được kích hoạt. Xin cảm ơn sự chung tay của bạn!');" 
                   class="inline-flex items-center justify-center px-8 py-3.5 bg-white hover:bg-slate-50 text-clinic-blue font-extrabold rounded-full transition-all shadow-md text-sm uppercase tracking-wide">
                    Quyên Góp Online
                </a>
                <a href="#cta-section" onclick="alert('Đang kết nối đến bộ phận tiếp nhận tình nguyện viên...');" 
                   class="inline-flex items-center justify-center px-8 py-3.5 bg-transparent border border-white hover:bg-white/10 text-white font-extrabold rounded-full transition-all text-sm uppercase tracking-wide">
                    Đăng Ký Tình Nguyện
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
