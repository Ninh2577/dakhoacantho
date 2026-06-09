@extends('layouts.app')

@section('title', 'Xét Nghiệm Chính Xác - Kết Quả Tin Cậy | Đa Khoa Gia Phước')

@section('meta')
    @php
        $category = \App\Models\Category::where('slug', 'xet-nghiem')->first();
        $categoryUrl = $category ? $category->public_url : route('category.show', ['category_path' => 'xet-nghiem']);
    @endphp
    <x-seo 
        title="Xét Nghiệm Chính Xác - Kết Quả Tin Cậy | Đa Khoa Gia Phước" 
        description="Dịch vụ xét nghiệm tổng quát, tầm soát ung thư, xét nghiệm gen di truyền, xét nghiệm nội tiết tại Đa Khoa Gia Phước Cần Thơ. Quy trình nhanh chóng, kết quả chính xác." 
        canonical="{{ $categoryUrl }}"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Xét Nghiệm', 'url' => $categoryUrl]
        ]"
        :faqs="[
            ['q' => 'Thời gian nhận kết quả xét nghiệm tại Gia Phước là bao lâu?', 'a' => 'Thời gian trả kết quả tùy thuộc vào từng loại xét nghiệm cụ thể. Với các xét nghiệm cơ bản, kết quả sẽ có sau 2 - 4 giờ và có thể nhận kết quả trực tuyến nhanh chóng.'],
            ['q' => 'Tôi cần chuẩn bị gì trước khi thực hiện xét nghiệm?', 'a' => 'Tùy thuộc vào loại xét nghiệm, một số xét nghiệm máu yêu cầu nhịn ăn từ 8 - 12 giờ trước khi lấy mẫu. Đội ngũ tư vấn sẽ liên hệ hướng dẫn chi tiết cho bạn trước khi thực hiện.'],
            ['q' => 'Kết quả xét nghiệm tại Đa Khoa Gia Phước có đảm bảo độ chính xác không?', 'a' => 'Có, hệ thống phòng Lab đạt tiêu chuẩn ISO 15189:2012 cùng các trang thiết bị hiện đại từ Roche, Abbott giúp đảm bảo kết quả phân tích chính xác và tin cậy nhất.']
        ]"
    />
@endsection


@section('content')
<div x-data="{ isOpen: false }" class="bg-slate-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative min-h-[500px] flex items-center py-20 lg:py-28 overflow-hidden text-white">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1664447972888-751765c92c42?auto=format&fit=crop&q=80&w=2000" 
                 alt="Phòng Lab Xét Nghiệm" class="w-full h-full object-cover object-center" decoding="async" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-r from-clinic-blue/90 via-clinic-blue/80 to-clinic-sky/40"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="max-w-3xl space-y-6">
                <nav class="flex text-xs md:text-sm text-slate-350 gap-2 items-center" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                    <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-clinic-sky">Dịch Vụ Xét Nghiệm</span>
                </nav>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-clinic-sky/20 text-clinic-sky border border-clinic-sky/30 uppercase tracking-widest">
                    Dịch vụ xét nghiệm chuẩn quốc tế
                </span>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    Xét Nghiệm Chính Xác <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-sky to-white">Kết Quả Tin Cậy</span>
                </h1>

                <p class="text-slate-200 text-base md:text-lg leading-relaxed max-w-xl">
                    Hệ thống phòng Lab hiện đại, đạt tiêu chuẩn ISO 15189:2012, cam kết mang đến kết quả phân tích chính xác nhất phục vụ chẩn đoán và điều trị.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 pt-2">
                    <button @click="isOpen = true" class="inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-clinic-sky to-blue-600 hover:from-clinic-sky hover:to-blue-700 text-white font-extrabold rounded-xl transition-all shadow-lg shadow-clinic-sky/20 hover:shadow-xl text-sm">
                        Đăng ký xét nghiệm ngay
                    </button>
                    <button @click="document.getElementById('goi-xet-nghiem').scrollIntoView({ behavior: 'smooth' })" class="inline-flex items-center justify-center px-6 py-3.5 bg-white/10 hover:bg-white/20 border border-white/25 text-white font-extrabold rounded-xl transition-all text-sm">
                        Xem bảng giá chi tiết
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Gói Xét Nghiệm Chuyên Sâu -->
    <section id="goi-xet-nghiem" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Gói Xét Nghiệm Chuyên Sâu
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Chúng tôi cung cấp đa dạng các danh mục xét nghiệm từ cơ bản đến nâng cao, đáp ứng mọi nhu cầu kiểm tra sức khỏe của bạn.
                </p>
            </div>

            <!-- Asymmetric Grid -->
            <div class="space-y-6">
                <!-- Top Row: 2/3 and 1/3 layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left (2/3 width) Card -->
                    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-8 flex flex-col md:flex-row justify-between gap-8 hover:shadow-xl transition-all duration-300">
                        <div class="space-y-6 flex-1">
                            <span class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-2xl font-bold text-slate-900">Kiểm Tra Sức Khỏe Tổng Quát</h3>
                                <p class="text-slate-500 text-sm mt-2 leading-relaxed">
                                    Đánh giá chức năng gan, thận, mỡ máu, đường huyết và các chỉ số huyết học cơ bản. Phù hợp cho kiểm tra định kỳ hàng năm.
                                </p>
                            </div>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs font-semibold text-slate-600">
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-clinic-sky"></span>
                                    25 chỉ số sinh hóa quan trọng.
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-clinic-sky"></span>
                                    Tư vấn kết quả cùng Bác sĩ chuyên khoa.
                                </li>
                            </ul>
                        </div>
                        <div class="flex flex-col justify-between items-start md:items-end min-w-[200px] border-t md:border-t-0 md:border-l border-slate-100 pt-6 md:pt-0 md:pl-8">
                            <div class="mb-6">
                                <span class="text-xs text-slate-400 font-bold line-through">1.200.000đ</span>
                                <div class="text-3xl font-black text-clinic-blue mt-1">890.000đ</div>
                            </div>
                            <button @click="isOpen = true" class="w-full md:w-auto px-6 py-3 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl transition-all shadow-md text-xs">
                                Đăng ký
                            </button>
                        </div>
                    </div>

                    <!-- Right (1/3 width) Card (Dark Blue) -->
                    <div class="bg-clinic-blue rounded-3xl p-8 text-white flex flex-col justify-between hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-clinic-sky/25 rounded-full blur-2xl pointer-events-none"></div>

                        <div class="space-y-6 relative z-10">
                            <span class="w-12 h-12 rounded-xl bg-white/10 text-clinic-sky flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-xl font-bold">Tầm Soát Ung Thư</h3>
                                <p class="text-blue-150 text-xs mt-2 leading-relaxed">
                                    Phát hiện sớm các dấu hiệu ung thư phổ biến như Gan, Phổi, Tiêu hóa, Tuyến giáp...
                                </p>
                            </div>
                        </div>
                        <div class="mt-8 relative z-10">
                            <div class="text-xs text-blue-200 font-semibold">Giá chỉ từ</div>
                            <div class="text-2xl font-black text-white mt-1">2.500.000đ</div>
                            <button @click="isOpen = true" class="w-full mt-4 py-3 bg-white hover:bg-blue-50 text-clinic-blue font-extrabold rounded-xl transition-all text-xs">
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: 2 equal-width cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Card -->
                    <div class="bg-blue-50/15 rounded-3xl border border-slate-100 p-8 flex flex-col justify-between items-start hover:shadow-lg transition-all duration-300 group">
                        <div class="space-y-4 w-full">
                            <div class="flex justify-between items-start">
                                <span class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A4 4 0 0111 8c0 1.553-.648 2.955-1.688 3.957M16.136 4.243A4 4 0 0013 8c0 1.553.648 2.955 1.688 3.957M9 12h.008M15 12h.008M12 15h.008M12 18h.008M9 6.75h.008M15 6.75h.008M12 9h.008M12 12h.008"></path>
                                    </svg>
                                </span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-clinic-blue transition-colors">Xét Nghiệm Gen & Di Truyền</h3>
                            <p class="text-slate-505 text-xs leading-relaxed">
                                Giải mã hệ gen để xác định nguy cơ mắc các bệnh di truyền và tối ưu hóa lối sống của bạn.
                            </p>
                        </div>
                        <button @click="isOpen = true" class="mt-6 inline-flex items-center text-clinic-blue font-bold text-xs hover:underline">
                            Tìm hiểu thêm &rarr;
                        </button>
                    </div>

                    <!-- Right Card -->
                    <div class="bg-blue-50/15 rounded-3xl border border-slate-100 p-8 flex flex-col justify-between items-start hover:shadow-lg transition-all duration-300 group">
                        <div class="space-y-4 w-full">
                            <span class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v13.012c0 .827-.673 1.5-1.5 1.5w-1.5a1.5 1.5 0 01-1.5-1.5V3.104m10.5 0v13.012c0 .827-.673 1.5-1.5 1.5h-1.5a1.5 1.5 0 01-1.5-1.5V3.104M3 20h18M12 4h.008"></path>
                                </svg>
                            </span>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-clinic-blue transition-colors">Xét Nghiệm Nội Tiết</h3>
                            <p class="text-slate-505 text-xs leading-relaxed">
                                Kiểm tra cân bằng Hormone giúp điều chỉnh sức khỏe sinh sản, tâm lý và chuyển hóa tối ưu.
                            </p>
                        </div>
                        <button @click="isOpen = true" class="mt-6 inline-flex items-center text-clinic-blue font-bold text-xs hover:underline">
                            Tìm hiểu thêm &rarr;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Công Nghệ Hiện Đại & Tiêu Chuẩn Quốc Tế -->
    <section class="py-16 lg:py-24 bg-gradient-to-tr from-slate-100 via-white to-blue-50/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Text detail & checkmarks -->
                <div class="lg:col-span-6 space-y-6">
                    <span class="text-xs font-bold text-clinic-blue uppercase tracking-widest">Công Nghệ Hiện Đại & Tiêu Chuẩn Quốc Tế</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Hệ thống thiết bị xét nghiệm đạt tiêu chuẩn ISO 15189:2012
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-sm md:text-base">
                        Chúng tôi đầu tư mạnh mẽ vào hệ thống máy móc tự động hóa hoàn toàn từ các tập đoàn hàng đầu thế giới như Roche, Abbott, Siemens. Quy trình xét nghiệm được kiểm soát nghiêm ngặt theo tiêu chuẩn quốc tế.
                    </p>

                    <div class="space-y-4 pt-2">
                        <!-- Bullet 1 -->
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-clinic-blue rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Đạt chuẩn ISO 15189:2012</h4>
                                <p class="text-slate-500 text-xs mt-0.5">Đảm bảo độ tin cậy và giá trị pháp lý cho mọi kết quả xét nghiệm.</p>
                            </div>
                        </div>

                        <!-- Bullet 2 -->
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-100 text-clinic-blue rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Kết quả nhanh chóng</h4>
                                <p class="text-slate-500 text-xs mt-0.5">Tối ưu hóa quy trình tự động hóa, trả kết quả trực tuyến sau 2 - 4 giờ.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: High tech image and badge -->
                <div class="lg:col-span-6 relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3] bg-slate-100">
                        <img src="https://images.unsplash.com/photo-1579154767073-4fc018a4788a?auto=format&fit=crop&q=80&w=800" 
                             alt="Máy xét nghiệm hiện đại Gia Phước" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <!-- Overlapping Blue badge bottom left -->
                    <div class="absolute -bottom-6 left-6 bg-clinic-blue text-white p-5 rounded-2xl shadow-xl border border-white/10 max-w-[240px]">
                        <h4 class="text-base font-extrabold">Đạt Chuẩn</h4>
                        <p class="text-[11px] text-blue-200 mt-1 leading-normal font-semibold">Kết quả được kiểm soát bởi Hệ thống Mỹ & Đức</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quy Trình 3 Bước Đơn Giản -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Quy Trình 3 Bước Đơn Giản
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-sm max-w-lg mx-auto">
                    Tiết kiệm thời gian tối đa, mang đến sự tiện lợi nhất cho khách hàng.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Line connecting steps (hidden on mobile) -->
                <div class="hidden md:block absolute top-10 left-1/6 right-1/6 h-0.5 bg-slate-100 -z-10"></div>

                <!-- Step 1 -->
                <div class="text-center space-y-4 flex flex-col items-center">
                    <span class="w-20 h-20 bg-blue-50 text-clinic-blue border-4 border-white shadow-md rounded-full flex items-center justify-center text-2xl font-black relative">
                        1
                    </span>
                    <h4 class="font-extrabold text-slate-900 text-lg">1. Đặt Lịch</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-xs mx-auto">
                        Đăng ký dịch vụ qua Website, App hoặc Hotline phòng khám.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center space-y-4 flex flex-col items-center">
                    <span class="w-20 h-20 bg-blue-50 text-clinic-blue border-4 border-white shadow-md rounded-full flex items-center justify-center text-2xl font-black relative">
                        2
                    </span>
                    <h4 class="font-extrabold text-slate-900 text-lg">2. Lấy Mẫu</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-xs mx-auto">
                        Thực hiện lấy mẫu tại phòng khám hoặc sử dụng dịch vụ lấy mẫu tại nhà.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center space-y-4 flex flex-col items-center">
                    <span class="w-20 h-20 bg-blue-50 text-clinic-blue border-4 border-white shadow-md rounded-full flex items-center justify-center text-2xl font-black relative">
                        3
                    </span>
                    <h4 class="font-extrabold text-slate-900 text-lg">3. Nhận Kết Quả</h4>
                    <p class="text-slate-500 text-xs leading-relaxed max-w-xs mx-auto">
                        Xem kết quả online qua hệ thống điện tử và nhận tư vấn, hướng dẫn trực tiếp từ đội ngũ chuyên môn.
                    </p>
                </div>
            </div>
        </div>
    </section>
 
    <!-- FAQ Section -->
    <section class="py-20 md:py-24 bg-slate-50 border-t border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Câu Hỏi Thường Gặp</h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
            </div>

            <div x-data="{ active: null }" class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Thời gian nhận kết quả xét nghiệm tại Gia Phước là bao lâu?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 1 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Thời gian trả kết quả tùy thuộc vào từng loại xét nghiệm cụ thể. Với các xét nghiệm cơ bản, kết quả sẽ có sau 2 - 4 giờ và có thể nhận kết quả trực tuyến nhanh chóng.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Tôi cần chuẩn bị gì trước khi thực hiện xét nghiệm?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 2 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Tùy thuộc vào loại xét nghiệm, một số xét nghiệm máu yêu cầu nhịn ăn từ 8 - 12 giờ trước khi lấy mẫu. Đội ngũ tư vấn sẽ liên hệ hướng dẫn chi tiết cho bạn trước khi thực hiện.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Kết quả xét nghiệm tại Đa Khoa Gia Phước có đảm bảo độ chính xác không?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 3 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Có, hệ thống phòng Lab đạt tiêu chuẩn ISO 15189:2012 cùng các trang thiết bị hiện đại từ Roche, Abbott giúp đảm bảo kết quả phân tích chính xác và tin cậy nhất.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles Section -->
    <x-related-articles-carousel
        title="Bài viết liên quan về Xét nghiệm"
        subtitle="Những lưu ý trước khi xét nghiệm, thời gian nhận kết quả và cách lựa chọn gói kiểm tra phù hợp."
        :articles="$relatedArticles"
        :viewAllUrl="route('categories.index')"
    />

    <!-- Bottom CTA Banner -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-clinic-blue to-clinic-sky text-white p-8 md:p-12 rounded-[2rem] shadow-xl text-center space-y-6">
                <h3 class="text-2xl md:text-4xl font-extrabold tracking-tight">
                    Sẵn sàng để kiểm tra sức khỏe?
                </h3>
                <p class="text-sm md:text-base text-blue-150 max-w-xl mx-auto font-medium">
                    Hãy đặt lịch khám hôm nay để tầm soát bệnh lý sớm, chăm sóc và bảo vệ sức khỏe của bạn và gia đình.
                </p>
                <div class="flex flex-wrap justify-center gap-4 pt-2">
                    <button @click="isOpen = true" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-clinic-blue font-extrabold rounded-xl transition-all shadow-md text-sm">
                        Đăng ký ngay
                    </button>
                    <a href="tel:0966332352" class="inline-flex items-center justify-center px-6 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl transition-all border border-white/20 text-sm">
                        Liên hệ tư vấn
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Alpine Modal Booking Form -->
    <div x-show="isOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div @click.away="isOpen = false" 
             x-data="{ submitting: false, name: '', phone: '', isValidPhone() { return /^(03|05|07|08|09)\d{8}$/.test(this.phone); } }"
             class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full relative shadow-2xl border border-slate-100 overflow-y-auto max-h-[90vh]">
            
            <button @click="isOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-650 outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l18 18"></path>
                </svg>
            </button>

            <h3 class="text-xl font-extrabold text-slate-900 text-center mb-1">
                Đăng Ký Xét Nghiệm
            </h3>
            <p class="text-slate-500 text-xs text-center mb-6">
                Vui lòng cung cấp thông tin, đội ngũ tư vấn sẽ liên hệ hỗ trợ ngay.
            </p>

            <form id="booking-form-xetnghiem" action="{{ route('consultation.store') }}" method="POST" @submit="if(name && isValidPhone()) { submitting = true; mergeXetNghiemFields(); } else { $event.preventDefault(); }" class="space-y-4">
                @csrf
                <input type="hidden" name="department" value="Xét Nghiệm">
                <input type="hidden" id="xet-symptoms-hidden" name="symptoms" value="">

                <!-- Họ và tên -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                    <input type="text" name="name" required x-model="name" placeholder="Nguyễn Văn A" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                    @error('name')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Số điện thoại -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                    <input type="tel" name="phone" required x-model="phone" placeholder="0966332352" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                    <p x-show="phone.length > 0 && !isValidPhone()" class="text-xs font-semibold text-red-500 mt-1" x-cloak>
                        Số điện thoại hợp lệ gồm 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).
                    </p>
                    @error('phone')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gói xét nghiệm -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Gói xét nghiệm quan tâm</label>
                    <select id="xet-package-select" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                        <option value="Kiểm Tra Sức Khỏe Tổng Quát (890k)">Kiểm Tra Sức Khỏe Tổng Quát (890.000đ)</option>
                        <option value="Tầm Soát Ung Thư (từ 2.5tr)">Tầm Soát Ung Thư (từ 2.500.000đ)</option>
                        <option value="Xét Nghiệm Gen & Di Truyền">Xét Nghiệm Gen & Di Truyền</option>
                        <option value="Xét Nghiệm Nội Tiết">Xét Nghiệm Nội Tiết</option>
                        <option value="Xét Nghiệm Khác / Chưa Xác Định">Xét Nghiệm Khác / Chưa Xác Định</option>
                    </select>
                </div>

                <!-- Ghi chú thêm -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Triệu chứng hoặc yêu cầu riêng (Tùy chọn)</label>
                    <textarea id="xet-notes-textarea" rows="2" placeholder="Ví dụ: Tôi muốn lấy mẫu tại nhà..." 
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all resize-none"></textarea>
                </div>

                <!-- Privacy Agreement Checkbox -->
                <div class="flex items-start gap-2.5 pt-1">
                    <input type="checkbox" id="form-privacy-agree-xetnghiem" required checked class="mt-1 w-4 h-4 text-clinic-blue border-slate-300 rounded focus:ring-clinic-blue">
                    <label for="form-privacy-agree-xetnghiem" class="text-xs text-slate-505 leading-normal select-none font-semibold">
                        Tôi đồng ý với chính sách bảo mật thông tin và quy trình tư vấn riêng tư của phòng khám.
                    </label>
                </div>

                <button type="submit" 
                        :disabled="submitting || !name || !isValidPhone()"
                        class="w-full py-4 bg-clinic-blue disabled:bg-slate-300 disabled:cursor-not-allowed hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all text-sm tracking-wide">
                    <span x-show="!submitting">Xác nhận đăng ký</span>
                    <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Đang gửi...
                    </span>
                </button>
            </form>
            <script>
                function mergeXetNghiemFields() {
                    const pkg = document.getElementById('xet-package-select').value;
                    const notes = document.getElementById('xet-notes-textarea').value;
                    document.getElementById('xet-symptoms-hidden').value = `Gói quan tâm: ${pkg}. Ghi chú thêm: ${notes}`;
                }
            </script>
        </div>
    </div>
</div>
@endsection
