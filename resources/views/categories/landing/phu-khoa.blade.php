@extends('layouts.app')

@section('title', 'Chuyên Khoa Phụ Khoa - Tư Vấn Uy Tín & Kín Đáo | Đa Khoa Gia Phước')

@section('meta')
    <x-seo 
        title="Chuyên Khoa Phụ Khoa - Tư Vấn Uy Tín & Kín Đáo | Đa Khoa Gia Phước" 
        description="Dịch vụ hỗ trợ tư vấn phụ khoa định kỳ, viêm nhiễm phụ khoa, kế hoạch hóa gia đình tại Đa Khoa Gia Phước Cần Thơ. Quy trình riêng tư, bảo mật." 
        canonical="{{ route('category.show', ['category_path' => 'phu-khoa']) }}"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Phụ Khoa', 'url' => route('category.show', ['category_path' => 'phu-khoa'])]
        ]"
        :faqs="[
            ['q' => 'Khi nào chị em phụ nữ cần chủ động đi khám phụ khoa?', 'a' => 'Chị em phụ nữ nên chủ động đi khám phụ khoa định kỳ từ 3 - 6 tháng hoặc ngay khi xuất hiện các triệu chứng bất thường như khí hư bất thường, ngứa ngáy vùng kín, đau bụng dưới âm ỉ hoặc rối loạn kinh nguyệt.'],
            ['q' => 'Quy trình thăm khám phụ khoa tại Gia Phước có bảo mật không?', 'a' => 'Phòng khám cam kết bảo mật nghiêm ngặt thông tin cá nhân và hồ sơ bệnh án theo quy trình khép kín, đảm bảo sự riêng tư và tôn trọng quyền cá nhân của từng khách hàng.'],
            ['q' => 'Chi phí tư vấn và khám phụ khoa là bao nhiêu?', 'a' => 'Chi phí thăm khám phụ khoa tùy thuộc vào gói dịch vụ và các xét nghiệm cụ thể được thực hiện. Mọi chi phí đều được niêm yết công khai rõ ràng theo quy định.']
        ]"
    />
@endsection


@section('content')
<div class="bg-rose-50/30 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-tr from-rose-50 via-white to-pink-50/50 py-16 lg:py-24 overflow-hidden">
        <!-- Floating shapes for aesthetic depth -->
        <div class="absolute top-10 left-1/10 w-72 h-72 bg-pink-200/30 rounded-full blur-3xl -z-10"></div>
        <div class="absolute bottom-10 right-1/10 w-96 h-96 bg-rose-200/20 rounded-full blur-3xl -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Headline & Description -->
                <div class="lg:col-span-7 space-y-6">
                    <nav class="flex text-xs md:text-sm text-slate-500 gap-2 items-center" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}" class="hover:text-clinic-blue transition-colors">Trang chủ</a>
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-rose-400">Chuyên Khoa Phụ Khoa</span>
                    </nav>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-rose-600 border border-pink-200/50 uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                        Chăm sóc sức khỏe phụ nữ toàn diện
                    </span>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-slate-900">
                        Chuyên Khoa Phụ Khoa <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-500 to-pink-600">Tận tâm & Kín đáo</span>
                    </h1>

                    <p class="text-slate-600 text-lg md:text-xl leading-relaxed max-w-xl">
                        Chúng tôi mang đến môi trường thăm khám chuyên nghiệp, nhẹ nhàng, ưu tiên tuyệt đối sự riêng tư và thoải mái cho mọi phụ nữ.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 pt-2">
                        <a href="#booking-section" class="inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-extrabold rounded-xl transition-all shadow-lg shadow-rose-500/20 hover:shadow-xl hover:-translate-y-0.5 text-sm">
                            Tư vấn ngay
                        </a>
                        <a href="tel:0966332352" class="inline-flex items-center justify-center px-6 py-3.5 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-extrabold rounded-xl transition-all text-sm gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.502-5.174-3.852-6.678-6.679l1.293-.97a1.243 1.243 0 00.37-1.173L6.745 3.34a1.243 1.243 0 00-1.202-.852H3.75a2.25 2.25 0 00-2.25 2.25v1.372z"></path>
                            </svg>
                            0966.332.352
                        </a>
                    </div>
                </div>

                <!-- Right: Doctor Image with Overlapping Badge -->
                <div class="lg:col-span-5 relative flex justify-center">
                    <div class="relative w-full max-w-md aspect-[4/5] rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white/80 bg-rose-100">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=800" 
                             alt="Đội ngũ hỗ trợ Phụ Khoa" class="w-full h-full object-cover">
                    </div>
                    <!-- Overlapping Security Badge -->
                    <div class="absolute bottom-6 -left-4 md:-left-8 bg-white/90 backdrop-blur-md border border-rose-100 p-4 rounded-2xl shadow-xl max-w-[240px] flex items-start gap-3 transform hover:scale-105 transition-all">
                        <span class="flex-shrink-0 w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center text-rose-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-extrabold text-slate-900">Bảo mật thông tin</h4>
                            <p class="text-[11px] text-slate-500 leading-normal font-medium mt-0.5">Quy trình tư vấn và thăm khám riêng tư, bảo mật thông tin nội bộ nghiêm ngặt.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Các bệnh lý phụ khoa thường gặp -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Các bệnh lý phụ khoa thường gặp
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-pink-500 mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Phát hiện sớm các dấu hiệu bất thường giúp bảo vệ thiên chức làm mẹ và chất lượng cuộc sống.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl border border-rose-100 p-8 hover:shadow-2xl hover:border-pink-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-rose-600 transition-colors">Viêm nhiễm phụ khoa</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Điều trị dứt điểm các tình trạng viêm âm đạo, nấm, ngứa và các bệnh lây truyền qua đường tình dục.
                        </p>
                    </div>
                    <a href="#booking-section" class="inline-flex items-center text-rose-500 font-bold text-sm hover:underline">
                        Xem chi tiết &rarr;
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-3xl border border-rose-100 p-8 hover:shadow-2xl hover:border-pink-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-rose-600 transition-colors">Kế hoạch hóa gia đình</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Tư vấn các biện pháp tránh thai an toàn, đặt vòng, cấy que và chăm sóc sức khỏe sinh sản.
                        </p>
                    </div>
                    <a href="#booking-section" class="inline-flex items-center text-rose-500 font-bold text-sm hover:underline">
                        Xem chi tiết &rarr;
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-3xl border border-rose-100 p-8 hover:shadow-2xl hover:border-pink-200 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-rose-600 transition-colors">Rối loạn kinh nguyệt</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Chẩn đoán và điều trị tình trạng kinh nguyệt không đều, đau bụng kinh và các dấu hiệu bất thường ở mọi lứa tuổi.
                        </p>
                    </div>
                    <a href="#booking-section" class="inline-flex items-center text-rose-500 font-bold text-sm hover:underline">
                        Xem chi tiết &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dịch vụ Chẩn đoán Công nghệ cao -->
    <section class="py-16 lg:py-24 bg-gradient-to-br from-rose-50/50 via-white to-pink-50/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left: CSS Grid of 2 images -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-3xl overflow-hidden shadow-lg aspect-square border-4 border-white bg-slate-100 hover:scale-105 transition-transform duration-300">
                        <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?auto=format&fit=crop&q=80&w=800" 
                             alt="Siêu âm phụ khoa" class="w-full h-full object-cover">
                    </div>
                    <div class="rounded-3xl overflow-hidden shadow-lg aspect-square border-4 border-white bg-slate-100 hover:scale-105 transition-transform duration-300 translate-y-6">
                        <img src="https://images.unsplash.com/photo-1579154767073-4fc018a4788a?auto=format&fit=crop&q=80&w=800" 
                             alt="Nội soi cổ tử cung" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Right: Text & Details -->
                <div class="space-y-6 lg:pl-6">
                    <span class="text-xs font-bold text-rose-500 uppercase tracking-widest">Dịch vụ Chẩn đoán Công nghệ cao</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Phòng khám Gia Phước đầu tư trang thiết bị y tế hiện đại bậc nhất
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-sm md:text-base">
                        Chúng tôi trang bị các hệ thống máy siêu âm 4D, máy nội soi cổ tử cung kỹ thuật số độ phân giải cao giúp phát hiện sớm các bệnh lý tử cung, buồng trứng và tầm soát ung thư chính xác nhất.
                    </p>

                    <ul class="space-y-3.5">
                        <li class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-medium text-sm">Siêu âm đầu dò 4D: Quan sát chi tiết tử cung và buồng trứng.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-medium text-sm">Nội soi cổ tử cung: Phát hiện sớm viêm lộ tuyến, ung thư.</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-5 h-5 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                                </svg>
                            </span>
                            <span class="text-slate-700 font-medium text-sm">Dịch vụ lấy mẫu tại nhà: Tiện lợi và bảo mật cho các xét nghiệm cơ bản.</span>
                        </li>
                    </ul>

                    <div class="pt-4">
                        <a href="#booking-section" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-extrabold rounded-xl transition-all text-sm gap-2">
                            Bảng giá dịch vụ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Đặt lịch hẹn kín đáo -->
    <section id="booking-section" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-tr from-rose-50/40 via-white to-pink-50/40 rounded-[2.5rem] border border-rose-100 shadow-xl overflow-hidden grid grid-cols-1 lg:grid-cols-2">
                <!-- Left: Solid clinic blue block -->
                <div class="bg-clinic-blue text-white p-8 md:p-12 relative flex flex-col justify-between overflow-hidden">
                    <!-- Giant female icon watermark in background -->
                    <div class="absolute -right-16 -bottom-16 w-80 h-80 opacity-5 pointer-events-none text-white">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                            <path d="M12 2C8.69 2 6 4.69 6 8c0 2.96 2.15 5.43 5 5.91V17H9v2h3v3h2v-3h3v-2h-3v-3.09c2.85-.48 5-2.95 5-5.91 0-3.31-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                        </svg>
                    </div>

                    <div class="space-y-6 relative z-10">
                        <h3 class="text-3xl font-extrabold tracking-tight">Đặt lịch hẹn kín đáo</h3>
                        <p class="text-blue-100 text-sm md:text-base leading-relaxed max-w-md">
                            Hãy để chúng tôi đồng hành cùng sức khỏe của bạn. Vui lòng để lại thông tin, đội ngũ tư vấn sẽ liên hệ lại trong vòng 15 phút.
                        </p>
                    </div>

                    <div class="mt-12 space-y-6 relative z-10">
                        <div class="flex items-center gap-4">
                            <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-clinic-sky">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <h5 class="text-xs text-blue-200 font-bold uppercase tracking-wider">Giờ làm việc</h5>
                                <p class="text-sm font-semibold">07:30 - 20:00 (Tất cả các ngày)</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-clinic-sky">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <h5 class="text-xs text-blue-200 font-bold uppercase tracking-wider">Địa chỉ</h5>
                                <p class="text-sm font-semibold">Số 57 Hùng Vương, P.Ninh Kiều, TP.Cần Thơ</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: White Booking Form -->
                <div x-data="{ submitting: false, name: '', phone: '', isValidPhone() { return /^(03|05|07|08|09)\d{8}$/.test(this.phone); } }" class="p-8 md:p-12 bg-white flex flex-col justify-center">
                    @if(session('success'))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 p-4 rounded-xl mb-6 text-sm font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form id="booking-form-phukhoa" action="{{ route('consultation.store') }}" method="POST" @submit="if(name && isValidPhone()) { submitting = true; mergePhuKhoaFields(); } else { $event.preventDefault(); }" class="space-y-5">
                        @csrf
                        <input type="hidden" name="department" value="Phụ Khoa">
                        <input type="hidden" id="symptoms-hidden" name="symptoms" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Họ và tên -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                                <input type="text" name="name" required x-model="name" placeholder="Nguyễn Văn A" 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-rose-400 focus:bg-white focus:ring-1 focus:ring-rose-400/20 outline-none text-slate-850 text-sm font-semibold rounded-xl transition-all">
                                @error('name')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                                <input type="tel" name="phone" required x-model="phone" placeholder="0966332352" 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-rose-400 focus:bg-white focus:ring-1 focus:ring-rose-400/20 outline-none text-slate-850 text-sm font-semibold rounded-xl transition-all">
                                <p x-show="phone.length > 0 && !isValidPhone()" class="text-xs font-semibold text-red-500 mt-1" x-cloak>
                                    Số điện thoại hợp lệ gồm 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).
                                </p>
                                @error('phone')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Service Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dịch vụ quan tâm</label>
                            <select id="service-select" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-rose-400 focus:bg-white focus:ring-1 focus:ring-rose-400/20 outline-none text-slate-880 text-sm font-semibold rounded-xl transition-all">
                                <option value="Khám phụ khoa định kỳ">Khám phụ khoa định kỳ</option>
                                <option value="Điều trị viêm nhiễm phụ khoa">Điều trị viêm nhiễm phụ khoa</option>
                                <option value="Tầm soát ung thư cổ tử cung">Tầm soát ung thư cổ tử cung</option>
                                <option value="Kế hoạch hóa gia đình / Tránh thai">Kế hoạch hóa gia đình / Tránh thai</option>
                                <option value="Tư vấn hiếm muộn">Tư vấn hiếm muộn</option>
                            </select>
                        </div>

                        <!-- Ghi chú thêm -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ghi chú thêm (Tùy chọn)</label>
                            <textarea id="notes-textarea" rows="3" placeholder="Nhập triệu chứng của bạn..." 
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-rose-400 focus:bg-white focus:ring-1 focus:ring-rose-400/20 outline-none text-slate-850 text-sm font-semibold rounded-xl transition-all resize-none"></textarea>
                        </div>

                        <!-- Privacy Agreement Checkbox -->
                        <div class="flex items-start gap-2.5 pt-1">
                            <input type="checkbox" id="form-privacy-agree-phukhoa" required checked class="mt-1 w-4 h-4 text-rose-500 border-slate-300 rounded focus:ring-rose-500">
                            <label for="form-privacy-agree-phukhoa" class="text-xs text-slate-500 leading-normal select-none font-semibold">
                                Tôi đồng ý với chính sách bảo mật thông tin và quy trình tư vấn riêng tư của phòng khám.
                            </label>
                        </div>

                        <button type="submit" 
                                :disabled="submitting || !name || !isValidPhone()"
                                class="w-full py-4 bg-gradient-to-r from-rose-500 to-pink-600 disabled:bg-slate-300 disabled:cursor-not-allowed hover:from-rose-600 hover:to-pink-700 text-white font-extrabold rounded-xl shadow-lg shadow-rose-500/20 hover:shadow-xl transition-all text-sm tracking-wide">
                            <span x-show="!submitting">Xác nhận đăng ký</span>
                            <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Đang gửi...
                            </span>
                        </button>
                        <p class="text-[10px] text-center text-slate-400 font-medium">
                            * Mọi thông tin trao đổi được cam kết bảo mật nghiêm ngặt theo quy trình riêng tư nội bộ của phòng khám.
                        </p>
                    </form>

                    <script>
                        function mergePhuKhoaFields() {
                            const service = document.getElementById('service-select').value;
                            const notes = document.getElementById('notes-textarea').value;
                            document.getElementById('symptoms-hidden').value = `Dịch vụ quan tâm: ${service}. Ghi chú thêm: ${notes}`;
                        }
                    </script>
                </div>
            </div>
        </div>
    </section>

    <!-- Không gian tư vấn Phụ khoa riêng tư & Quy trình chăm sóc kín đáo -->
    <section class="py-16 lg:py-24 bg-rose-50/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold text-rose-500 uppercase tracking-widest">Quy trình chuyên nghiệp</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Không Gian Riêng Tư & Quy Trình Chăm Sóc Kín Đáo
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-pink-500 mx-auto rounded-full"></div>
                <p class="text-slate-500 text-sm max-w-xl mx-auto">
                    Chúng tôi thấu hiểu tâm lý e ngại của phụ nữ và nỗ lực mang đến một quy trình chăm sóc kín đáo, tôn trọng quyền riêng tư tuyệt đối.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Point 1 -->
                <div class="bg-white p-8 rounded-3xl border border-rose-100/50 shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-slate-900 text-lg mb-3">Mô hình tư vấn 1-1</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Quy trình tư vấn khép kín giúp khách hàng thoải mái chia sẻ, trao đổi mọi thắc mắc tế nhị liên quan đến sức khỏe phụ khoa.
                    </p>
                </div>

                <!-- Point 2 -->
                <div class="bg-white p-8 rounded-3xl border border-rose-100/50 shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-slate-900 text-lg mb-3">Bảo mật thông tin nội bộ</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Mọi hồ sơ y tế, thông tin cá nhân và nội dung tư vấn đều được quản lý bảo mật nghiêm ngặt trong hệ thống quản trị nội bộ.
                    </p>
                </div>

                <!-- Point 3 -->
                <div class="bg-white p-8 rounded-3xl border border-rose-100/50 shadow-sm hover:shadow-md transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-slate-900 text-lg mb-3">Đồng hành tâm lý</h4>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Đội ngũ tư vấn viên nữ chu đáo, thấu cảm, hỗ trợ giảm bớt lo âu và hướng dẫn tận tình trong suốt thời gian kết nối.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cảm nhận khách hàng -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Cảm nhận từ khách hàng
                </h2>
                <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-pink-500 mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Sự hài lòng và hạnh phúc của người bệnh là phần thưởng lớn nhất của chúng tôi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-rose-50/20 border border-rose-100 p-8 rounded-3xl relative flex flex-col justify-between hover:shadow-lg transition-shadow">
                    <div>
                        <!-- Quote decoration -->
                        <span class="absolute top-6 right-8 text-pink-200 text-6xl font-serif select-none">&ldquo;</span>
                        <div class="flex text-amber-400 gap-1 mb-4">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6 italic relative z-10">
                            "Tôi rất hài lòng với sự tận tâm của đội ngũ tư vấn và nhân viên y tế tại đây. Phòng khám riêng tư, sạch sẽ, quy trình nhẹ nhàng và giải thích rất cặn kẽ."
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-200 overflow-hidden flex items-center justify-center text-rose-600 font-bold text-sm">
                            MA
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Chị Minh Anh</h5>
                            <p class="text-[10px] text-slate-400">Ninh Kiều, Cần Thơ</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-rose-50/20 border border-rose-100 p-8 rounded-3xl relative flex flex-col justify-between hover:shadow-lg transition-shadow">
                    <div>
                        <span class="absolute top-6 right-8 text-pink-200 text-6xl font-serif select-none">&ldquo;</span>
                        <div class="flex text-amber-400 gap-1 mb-4">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6 italic relative z-10">
                            "Dịch vụ siêu âm 4D ở đây hình ảnh rõ nét, nhân viên y tế hỗ trợ chu đáo từng chi tiết nhỏ. Cảm giác hoàn toàn thoải mái và an tâm khi sử dụng dịch vụ tại đây."
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-200 overflow-hidden flex items-center justify-center text-rose-600 font-bold text-sm">
                            NB
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Chị Ngọc Bích</h5>
                            <p class="text-[10px] text-slate-400">Bình Thủy, Cần Thơ</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-rose-50/20 border border-rose-100 p-8 rounded-3xl relative flex flex-col justify-between hover:shadow-lg transition-shadow">
                    <div>
                        <span class="absolute top-6 right-8 text-pink-200 text-6xl font-serif select-none">&ldquo;</span>
                        <div class="flex text-amber-400 gap-1 mb-4">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-6 italic relative z-10">
                            "Nhân viên hướng dẫn rất ân cần, giúp đỡ tôi làm thủ tục nhanh chóng, không phải chờ lâu như các bệnh viện lớn."
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-200 overflow-hidden flex items-center justify-center text-rose-600 font-bold text-sm">
                            TD
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Chị Thùy Dương</h5>
                            <p class="text-[10px] text-slate-400">Ô Môn, Cần Thơ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 lg:py-24 bg-rose-50/20 border-t border-rose-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-4">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Câu Hỏi Thường Gặp</h2>
                <div class="w-16 h-1 bg-gradient-to-r from-rose-500 to-pink-500 mx-auto rounded-full"></div>
            </div>

            <div x-data="{ active: null }" class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 1 ? null : 1" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Khi nào chị em phụ nữ cần chủ động đi khám phụ khoa?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 1 ? 'rotate-180 bg-rose-500 text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Chị em phụ nữ nên chủ động đi khám phụ khoa định kỳ từ 3 - 6 tháng hoặc ngay khi xuất hiện các triệu chứng bất thường như khí hư bất thường, ngứa ngáy vùng kín, đau bụng dưới âm ỉ hoặc rối loạn kinh nguyệt.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 2 ? null : 2" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Quy trình thăm khám phụ khoa tại Gia Phước có bảo mật không?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 2 ? 'rotate-180 bg-rose-500 text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Phòng khám cam kết bảo mật nghiêm ngặt thông tin cá nhân và hồ sơ bệnh án theo quy trình khép kín, đảm bảo sự riêng tư và tôn trọng quyền cá nhân của từng khách hàng.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden transition-all duration-300">
                    <button @click="active = active === 3 ? null : 3" class="w-full flex items-center justify-between p-6 text-left focus:outline-none">
                        <span class="font-bold text-slate-900 pr-4 text-sm md:text-base">Chi phí tư vấn và khám phụ khoa là bao nhiêu?</span>
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="active === 3 ? 'rotate-180 bg-rose-500 text-white' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </span>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-50 pt-4">
                            Chi phí thăm khám phụ khoa tùy thuộc vào gói dịch vụ và các xét nghiệm cụ thể được thực hiện. Mọi chi phí đều được niêm yết công khai rõ ràng theo quy định.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles Section -->
    <x-related-articles-carousel
        title="Bài viết liên quan về Phụ khoa"
        subtitle="Thông tin sức khỏe phụ khoa, dấu hiệu thường gặp và lưu ý trong chăm sóc sức khỏe nữ giới."
        :articles="$relatedArticles"
        :viewAllUrl="route('categories.index')"
    />
</div>
@endsection
