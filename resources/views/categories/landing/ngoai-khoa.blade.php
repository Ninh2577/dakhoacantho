@extends('layouts.app')

@section('title', 'Chuyên Khoa Ngoại Khoa - Quy Trình An Toàn & Chuyên Nghiệp | Đa Khoa Gia Phước')

@section('meta')
    @php
        $category = \App\Models\Category::where('slug', 'ngoai-khoa')->first();
        $categoryUrl = $category ? $category->public_url : route('category.show', ['category_path' => 'ngoai-khoa']);
    @endphp
    <x-seo 
        title="Chuyên Khoa Ngoại Khoa - Quy Trình An Toàn & Chuyên Nghiệp | Đa Khoa Gia Phước" 
        description="Dịch vụ hỗ trợ tư vấn và thực hiện các tiểu phẫu ngoại khoa (bệnh trĩ, bao quy đầu, tiểu phẫu tổng quát...) tại Đa Khoa Gia Phước Cần Thơ. Quy trình vô trùng đạt chuẩn." 
        canonical="{{ $categoryUrl }}"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Ngoại Khoa', 'url' => $categoryUrl]
        ]"
        :faqs="[
            ['q' => 'Thời gian hồi phục sau tiểu phẫu ngoại khoa là bao lâu?', 'a' => 'Thời gian hồi phục tùy thuộc vào loại tiểu phẫu cụ thể và cơ địa từng người. Thông thường với các phương pháp xâm lấn tối thiểu tại Đa Khoa Gia Phước, thời gian hồi phục nhanh chóng và bệnh nhân có thể ra về trong ngày.'],
            ['q' => 'Quy trình tiểu phẫu ngoại khoa có đảm bảo vô trùng không?', 'a' => 'Có, toàn bộ trang thiết bị và phòng phẫu thuật đều tuân thủ nghiêm ngặt quy trình khử trùng, khử khuẩn theo tiêu chuẩn y khoa.'],
            ['q' => 'Thông tin bệnh án của tôi có được bảo mật không?', 'a' => 'Có, mọi thông tin cá nhân và bệnh án được mã hóa và bảo mật nghiêm ngặt theo quy trình riêng tư nội bộ của phòng khám.']
        ]"
    />
@endsection


@section('content')
<div class="bg-slate-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white overflow-hidden min-h-[550px] flex items-center">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&q=80&w=2000" 
                 alt="Ngoại Khoa" class="w-full h-full object-cover opacity-30 object-center" decoding="async" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/80 to-transparent"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full py-16 md:py-24">
            <div class="max-w-3xl">
                <!-- Breadcrumbs -->
                <nav class="flex mb-6 text-xs md:text-sm text-slate-300 gap-2 items-center" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                    <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-slate-400">Chuyên Khoa Ngoại Khoa</span>
                </nav>

                <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold bg-clinic-blue/20 text-clinic-sky border border-clinic-sky/30 uppercase tracking-widest mb-4">
                    Khoa Ngoại Gia Phước
                </span>
                
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight mb-6">
                    Ngoại Khoa – <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-sky to-clinic-teal">Đỉnh Cao Phẫu Thuật</span>, <br>
                    Chăm Sóc Toàn Diện
                </h1>

                <p class="text-slate-300 text-lg md:text-xl leading-relaxed mb-8 max-w-2xl">
                    Chúng tôi kết hợp công nghệ hỗ trợ ngoại khoa tiên tiến với đội ngũ tư vấn chuyên môn tận tâm nhằm mang lại kết quả tối ưu và sự an tâm cho bệnh nhân.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#consult-section" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all duration-200 text-base tracking-wide transform hover:-translate-y-0.5">
                        Đăng Ký Tư Vấn Ngay
                    </a>
                    <a href="#process-section" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 hover:border-white/40 font-extrabold rounded-xl text-base transition-all duration-200 backdrop-blur-sm">
                        Tìm Hiểu Quy Trình
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Các Dịch Vụ Mũi Nhọn -->
    <section class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Các Dịch Vụ Mũi Nhọn
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Sở hữu thế mạnh vượt trội với các phương pháp điều trị tiên tiến nhất hiện nay, rút ngắn thời gian điều trị và tối ưu chi phí.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-clinic-sky/10 flex items-center justify-center mb-6 group-hover:bg-clinic-blue transition-colors duration-300">
                            <svg class="w-7 h-7 text-clinic-blue group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Bệnh Trĩ & Hậu Môn Trực Tràng</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Điều trị dứt điểm các bệnh lý trĩ nội, trĩ ngoại bằng phương pháp PPH & HCPT hiện đại, không đau, phục hồi nhanh.
                        </p>
                    </div>
                    <a href="#consult-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:text-opacity-80 transition-colors group-hover:translate-x-1 duration-200">
                        Tìm Hiểu Thêm
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-clinic-sky/10 flex items-center justify-center mb-6 group-hover:bg-clinic-blue transition-colors duration-300">
                            <svg class="w-7 h-7 text-clinic-blue group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Phẫu Thuật Tổng Quát</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Phẫu thuật nội soi ruột thừa, sỏi mật, thoát vị bẹn và các bệnh lý ổ bụng với độ chính xác cao và vết mổ thẩm mỹ.
                        </p>
                    </div>
                    <a href="#consult-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:text-opacity-80 transition-colors group-hover:translate-x-1 duration-200">
                        Tìm Hiểu Thêm
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-clinic-sky/10 flex items-center justify-center mb-6 group-hover:bg-clinic-blue transition-colors duration-300">
                            <svg class="w-7 h-7 text-clinic-blue group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Chăm Sóc Hậu Phẫu</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Chế độ theo dõi sát sao sau mổ, quản lý đau hiệu quả và hỗ trợ phục hồi chức năng nhanh chóng cho bệnh nhân.
                        </p>
                    </div>
                    <a href="#consult-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:text-opacity-80 transition-colors group-hover:translate-x-1 duration-200">
                        Tìm Hiểu Thêm
                        <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Quy trình & Hỗ trợ trước/sau tiểu phẫu -->
    <section class="py-20 md:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Quy Trình & Hỗ Trợ Trước và Sau Tiểu Phẫu</h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">Chúng tôi luôn đặt sự an toàn và thoải mái của người bệnh lên hàng đầu thông qua quy trình hỗ trợ chặt chẽ, chu đáo.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Hướng dẫn trước tiểu phẫu -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-clinic-sky/10 text-clinic-blue flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Chuẩn bị trước tiểu phẫu</h3>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">1</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Tư vấn tiền phẫu:</strong> Đội ngũ tư vấn hỗ trợ giải đáp thắc mắc về phương pháp và quy trình thực hiện một cách rõ ràng.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">2</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Kiểm tra sức khỏe:</strong> Thực hiện các xét nghiệm cơ bản cần thiết trước khi tiến hành để đảm bảo đủ điều kiện y tế.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">3</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Hướng dẫn cá nhân:</strong> Chỉ dẫn chi tiết về chế độ ăn uống, vệ sinh cá nhân và các lưu ý quan trọng trước giờ thực hiện.</p>
                        </li>
                    </ul>
                </div>

                <!-- Chăm sóc phục hồi sau tiểu phẫu -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-clinic-teal/10 text-clinic-teal flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Hỗ trợ chăm sóc phục hồi</h3>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">1</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Theo dõi hậu phẫu:</strong> Nghỉ ngơi tại phòng lưu bệnh tiện nghi, được theo dõi sát sao các chỉ số sinh tồn và mức độ phục hồi.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">2</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Quản lý đau & Dinh dưỡng:</strong> Sử dụng các phương pháp hỗ trợ giảm đau tiên tiến và tư vấn chế độ dinh dưỡng phù hợp.</p>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold mt-0.5">3</span>
                            <p class="text-slate-600 text-sm leading-relaxed"><strong class="text-slate-900">Theo dõi từ xa:</strong> Đội ngũ nhân viên y tế liên hệ hỗ trợ nhắc lịch hẹn tái khám và hướng dẫn chăm sóc tại nhà.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Quy Trình Phẫu Thuật An Toàn -->
    <section id="process-section" class="py-20 md:py-24 bg-gradient-to-b from-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Quy Trình Phẫu Thuật An Toàn
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Quy trình khép kín, tuân thủ nghiêm ngặt tiêu chuẩn vô trùng của Bộ Y tế nhằm hạn chế tối đa nguy cơ nhiễm trùng và biến chứng.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                    <div class="absolute -right-2 -bottom-2 text-7xl font-black text-slate-100 group-hover:text-slate-200/60 transition-colors pointer-events-none select-none">01</div>
                    <div class="w-12 h-12 rounded-xl bg-clinic-sky/10 text-clinic-blue flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 relative z-10">Tư Vấn & Chỉ Định</h3>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Đội ngũ hỗ trợ chuyên môn tiếp nhận tình trạng lâm sàng, đề xuất xét nghiệm và chuẩn bị hồ sơ trước tiểu phẫu.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                    <div class="absolute -right-2 -bottom-2 text-7xl font-black text-slate-100 group-hover:text-slate-200/60 transition-colors pointer-events-none select-none">02</div>
                    <div class="w-12 h-12 rounded-xl bg-clinic-sky/10 text-clinic-blue flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 relative z-10">Hội Chẩn Chuyên Môn</h3>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Hội đồng chuyên môn phân tích kỹ hồ sơ bệnh lý, thống nhất phương án hỗ trợ điều trị tốt nhất.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                    <div class="absolute -right-2 -bottom-2 text-7xl font-black text-slate-100 group-hover:text-slate-200/60 transition-colors pointer-events-none select-none">03</div>
                    <div class="w-12 h-12 rounded-xl bg-clinic-sky/10 text-clinic-blue flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 relative z-10">Phẫu Thuật Hiện Đại</h3>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Thực hiện phẫu thuật tại phòng mổ vô khuẩn một chiều dưới sự hỗ trợ của trang thiết bị tối tân hàng đầu.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-2xl border border-slate-100 p-6 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                    <div class="absolute -right-2 -bottom-2 text-7xl font-black text-slate-100 group-hover:text-slate-200/60 transition-colors pointer-events-none select-none">04</div>
                    <div class="w-12 h-12 rounded-xl bg-clinic-sky/10 text-clinic-blue flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 relative z-10">Phục Hồi & Tái Khám</h3>
                    <p class="text-slate-500 text-sm leading-relaxed relative z-10">
                        Theo dõi chặt chẽ tại phòng hậu phẫu tiêu chuẩn, tư vấn dinh dưỡng và đặt lịch tái khám định kỳ.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cơ Sở Vật Chất -->
    <section class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Masonry Gallery (Col 6) -->
                <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-3xl overflow-hidden shadow-sm h-64">
                            <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=600" 
                                 alt="Corridor" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                        </div>
                        <div class="rounded-3xl overflow-hidden shadow-sm h-48">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&q=80&w=600" 
                                 alt="Equipment" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="space-y-4 pt-8">
                        <div class="rounded-3xl overflow-hidden shadow-sm h-48">
                            <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&q=80&w=600" 
                                 alt="Operating Room" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                        </div>
                        <div class="rounded-3xl overflow-hidden shadow-sm h-64">
                            <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&q=80&w=600" 
                                 alt="Clinic Exterior" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>

                <!-- Right: Text Content (Col 6) -->
                <div class="lg:col-span-6 space-y-6">
                    <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold bg-clinic-sky/10 text-clinic-blue uppercase tracking-wider">
                        Đầu Tư Đồng Bộ
                    </span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Hệ Thống Cơ Sở Vật Chất Đạt Chuẩn Quốc Tế
                    </h2>
                    <p class="text-slate-500 text-sm md:text-base leading-relaxed">
                        Đa Khoa Gia Phước tự hào sở hữu hệ thống phòng mổ vô khuẩn một chiều, được trang bị các thiết bị chẩn đoán hình ảnh và phẫu thuật hiện đại từ các tập đoàn y tế hàng đầu thế giới như GE, Siemens, Olympus.
                    </p>
                    
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-5 h-5 bg-clinic-teal/10 text-clinic-teal rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-bold text-sm">Phòng mổ vô trùng đạt chuẩn chất lượng y khoa.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-5 h-5 bg-clinic-teal/10 text-clinic-teal rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-bold text-sm">Máy phẫu thuật nội soi sắc nét, ít xâm lấn.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-5 h-5 bg-clinic-teal/10 text-clinic-teal rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-bold text-sm">Hệ thống oxy trung tâm và thiết bị cấp cứu tiên tiến.</span>
                        </li>
                    </ul>

                    <div class="pt-4">
                        <a href="#consult-section" class="inline-flex items-center justify-center px-6 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-md transition-all text-sm">
                            Khám Phá Cơ Sở Vật Chất
                        </a>
                    </div>
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
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Thời gian hồi phục sau tiểu phẫu ngoại khoa là bao lâu?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 1 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Thời gian hồi phục tùy thuộc vào loại tiểu phẫu cụ thể và cơ địa từng người. Thông thường với các phương pháp xâm lấn tối thiểu tại Đa Khoa Gia Phước, thời gian hồi phục nhanh chóng và bệnh nhân có thể ra về trong ngày dưới sự hướng dẫn của đội ngũ tư vấn.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Quy trình tiểu phẫu ngoại khoa có đảm bảo vô trùng không?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 2 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Có, toàn bộ trang thiết bị và phòng phẫu thuật đều tuân thủ nghiêm ngặt quy trình khử trùng, khử khuẩn theo tiêu chuẩn y khoa đạt chuẩn chất lượng của cơ quan quản lý.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-slate-150 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Thông tin bệnh án của tôi có được bảo mật không?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 3 ? 'rotate-180 bg-clinic-blue text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Có, mọi thông tin cá nhân và bệnh án được mã hóa và bảo mật nghiêm ngặt theo quy trình riêng tư nội bộ của phòng khám nhằm tôn trọng quyền riêng tư của khách hàng.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles Section -->
    <x-related-articles-carousel
        title="Bài viết liên quan về Ngoại khoa"
        subtitle="Các kiến thức cần biết trước và sau thăm khám, thủ thuật và chăm sóc sức khỏe ngoại khoa."
        :articles="$relatedArticles"
        :viewAllUrl="route('categories.index')"
    />

    <!-- Bottom CTA (Banner with inline form) -->
    <section id="consult-section" class="py-16 md:py-20 bg-slate-900 relative overflow-hidden">
        <!-- background decorative blur -->
        <div class="absolute -right-32 -bottom-32 w-96 h-96 bg-clinic-blue/20 rounded-full blur-3xl"></div>
        <div class="absolute -left-32 -top-32 w-96 h-96 bg-clinic-teal/10 rounded-full blur-3xl"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-2xl mb-8 max-w-2xl mx-auto text-left flex items-start gap-4">
                    <span class="p-2 bg-emerald-500/20 rounded-xl text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                    <div>
                        <h4 class="font-extrabold text-white text-base">Đăng ký thành công!</h4>
                        <p class="text-sm text-slate-300 mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Bạn Cần Tư Vấn Ngoại Khoa?</h2>
            <p class="text-slate-300 text-sm md:text-base max-w-2xl mx-auto mb-8 leading-relaxed">
                Để lại thông tin dưới đây, đội ngũ tư vấn sức khỏe của chúng tôi sẽ liên hệ tư vấn hoàn toàn miễn phí và bảo mật cho bạn trong vòng 15 phút.
            </p>

            <div x-data="{ submitting: false, phone: '', isValidPhone() { return /^(03|05|07|08|09)\d{8}$/.test(this.phone); } }" class="max-w-2xl mx-auto">
                <form action="{{ route('consultation.store') }}" method="POST" @submit="if(isValidPhone()) { submitting = true; } else { $event.preventDefault(); }" class="space-y-4">
                    @csrf
                    <!-- Pass default name hidden to satisfy controller requirement -->
                    <input type="hidden" name="name" value="Khách hàng Ngoại Khoa">
                    <input type="hidden" name="department" value="Ngoại Khoa">
                    <input type="hidden" name="symptoms" value="Đăng ký nhận tư vấn từ chuyên khoa Ngoại khoa">

                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-grow">
                            <input type="tel" name="phone" required x-model="phone" placeholder="Số điện thoại của bạn (ví dụ: 0966332352)..." 
                                   class="w-full px-5 py-4 bg-white/10 border border-white/15 focus:border-clinic-blue focus:bg-white/20 outline-none text-white placeholder-white/50 text-sm rounded-xl font-medium transition-all">
                            <p x-show="phone.length > 0 && !isValidPhone()" class="text-xs font-semibold text-red-400 mt-1.5 text-left" x-cloak>
                                Số điện thoại hợp lệ gồm 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).
                            </p>
                            @error('phone')
                                <p class="text-rose-400 text-xs text-left mt-1.5 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" 
                                :disabled="submitting || !isValidPhone()"
                                class="px-8 py-4 bg-clinic-blue disabled:bg-slate-700 disabled:cursor-not-allowed hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg transition-all text-sm tracking-wide shrink-0">
                            <span x-show="!submitting">Yêu Cầu Tư Vấn</span>
                            <span x-show="submitting" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Đang gửi...
                            </span>
                        </button>
                    </div>

                    <!-- Privacy Policy Agreement -->
                    <div class="flex items-start gap-2.5 pt-2 text-left justify-center sm:justify-start">
                        <input type="checkbox" id="form-privacy-agree-bottom" required checked class="mt-1 w-4 h-4 text-clinic-blue border-white/25 bg-slate-900 rounded focus:ring-clinic-blue">
                        <label for="form-privacy-agree-bottom" class="text-xs text-slate-300 leading-normal select-none font-semibold">
                            Tôi đồng ý với chính sách bảo mật thông tin và quy trình tư vấn riêng tư của phòng khám.
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
