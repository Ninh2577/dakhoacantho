@extends('layouts.app')

@section('title', 'Chuyên Khoa Bệnh Xã Hội - Bảo Mật & Kết Quả Nhanh | Đa Khoa Gia Phước')

@section('content')
<div class="bg-slate-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white overflow-hidden py-16 md:py-24">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1504813184591-015556c5c522?auto=format&fit=crop&q=80&w=2000" 
                 alt="Bệnh Xã Hội" class="w-full h-full object-cover opacity-20 object-center">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/95 to-slate-950/90"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Content Description (Col 7) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Breadcrumbs -->
                    <nav class="flex text-xs md:text-sm text-slate-300 gap-2 items-center" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}" class="hover:text-white transition-colors">Trang chủ</a>
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-slate-400">Chuyên Khoa Bệnh Xã Hội</span>
                    </nav>

                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-clinic-blue/20 text-clinic-sky border border-clinic-sky/30 uppercase tracking-widest">
                        Tư Vấn Riêng Tư & Kín Đáo
                    </span>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                        Chuyên Khoa <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-sky to-clinic-teal">Bệnh Xã Hội</span>
                    </h1>

                    <p class="text-slate-300 text-lg md:text-xl leading-relaxed max-w-xl">
                        Chăm sóc sức khỏe riêng tư, an toàn và bảo mật tuyệt đối. Đội ngũ chuyên gia hàng đầu, xét nghiệm chính xác, điều trị hiệu quả bằng công nghệ hiện đại nhất.
                    </p>

                    <!-- Trust Tags -->
                    <div class="flex flex-wrap gap-4 pt-2">
                        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-sm font-bold text-white">Bảo mật 100%</span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-bold text-white">Kết quả nhanh</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Form Card (Col 5) -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl text-slate-800 border border-slate-100">
                        <h3 class="text-xl font-extrabold text-slate-900 text-center mb-1">
                            Đặt Lịch Hẹn & Tư Vấn
                        </h3>
                        <p class="text-slate-500 text-xs text-center mb-6">
                            Vui lòng cung cấp thông tin, bác sĩ sẽ liên hệ lại ngay.
                        </p>

                        @if(session('success'))
                            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 p-4 rounded-xl mb-6 text-xs font-bold">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('consultation.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="department" value="Bệnh Xã Hội">

                            <!-- Họ và tên -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                                <input type="text" name="name" required placeholder="Nguyễn Văn A..." 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                                @error('name')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                                <input type="tel" name="phone" required placeholder="Nhập số điện thoại liên lạc..." 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                                @error('phone')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Triệu chứng của bạn -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Triệu chứng của bạn</label>
                                <textarea name="symptoms" rows="3" placeholder="Mô tả ngắn gọn tình trạng sức khỏe..." 
                                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all resize-none"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all text-sm tracking-wide">
                                Đăng Ký Tư Vấn Ngay
                            </button>

                            <p class="text-[10px] text-center text-slate-400 font-medium">
                                * Thông tin của bạn được bảo mật tuyệt đối 100% theo quy định của Bộ Y tế.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Xét Nghiệm & Điều Trị Toàn Diện -->
    <section class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Xét Nghiệm & Điều Trị Toàn Diện
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Chẩn đoán chính xác bằng trang thiết bị hiện đại, lên phác đồ điều trị tối ưu cho từng trường hợp.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-clinic-sky/5 rounded-3xl border border-slate-100 p-8 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-clinic-sky/15 text-clinic-blue flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Sùi Mào Gà</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Bệnh lây qua đường tình dục phổ biến, gây ra bởi virus HPV. Triệu chứng thường là các nốt sùi, mụn thịt nhỏ. Tư vấn để điều trị sớm tránh biến chứng ung thư.
                        </p>
                    </div>
                    <a href="#consultation-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Xem thêm chi tiết &rarr;
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-clinic-sky/5 rounded-3xl border border-slate-100 p-8 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-clinic-sky/15 text-clinic-blue flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Bệnh Lậu</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Nhiễm khuẩn do vi khuẩn Neisseria gonorrhoeae. Gây viêm nhiễm đường tiết niệu, sinh dục. Phát hiện sớm tránh biến chứng vô sinh ở cả nam và nữ.
                        </p>
                    </div>
                    <a href="#consultation-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Xem thêm chi tiết &rarr;
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-clinic-sky/5 rounded-3xl border border-slate-100 p-8 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-clinic-sky/15 text-clinic-blue flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Bệnh Giang Mai</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Bệnh do xoắn khuẩn Treponema pallidum. Diễn biến phức tạp, trải qua nhiều giai đoạn. Xét nghiệm chuyên sâu, điều trị kịp thời để bảo vệ hệ thần kinh và tim mạch.
                        </p>
                    </div>
                    <a href="#consultation-section" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Xem thêm chi tiết &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Vì Sao Chọn Chúng Tôi -->
    <section class="py-20 md:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Doctor image overlap (Col 5) -->
                <div class="lg:col-span-5 relative">
                    <div class="rounded-3xl overflow-hidden shadow-xl aspect-[4/5] bg-slate-200">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=600" 
                             alt="Doctor Trust" class="w-full h-full object-cover">
                    </div>
                    <!-- Overlapping Stat Card -->
                    <div class="absolute -bottom-6 -right-6 bg-gradient-to-br from-clinic-blue to-[#0b4c8c] text-white p-6 rounded-2xl shadow-xl max-w-[280px] border border-white/10">
                        <h4 class="text-xl font-extrabold mb-1">Hơn 15 năm</h4>
                        <p class="text-xs text-slate-200 leading-normal font-medium">
                            Kinh nghiệm lâm sàng thực tế. "Chúng tôi cam kết đặt sự riêng tư và sức khỏe của bệnh nhân lên hàng đầu."
                        </p>
                    </div>
                </div>

                <!-- Right: Text Content and Feature block list (Col 7) -->
                <div class="lg:col-span-7 space-y-8">
                    <div>
                        <span class="text-xs font-bold text-clinic-blue uppercase tracking-wider">Vì sao chọn chúng tôi</span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mt-1 leading-tight">
                            Niềm Tin Từ Sự Chuyên Nghiệp & Bảo Mật
                        </h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Feature 1 -->
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm text-clinic-blue flex items-center justify-center border border-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">Bảo mật hồ sơ tuyệt đối</h4>
                                <p class="text-slate-500 text-sm mt-1 leading-relaxed">
                                    Mọi thông tin cá nhân và bệnh án được mã hóa kỹ thuật số. Chỉ bác sĩ trực tiếp điều trị mới có quyền truy cập thông tin của bạn.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm text-clinic-blue flex items-center justify-center border border-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">Đội ngũ bác sĩ đầu ngành</h4>
                                <p class="text-slate-500 text-sm mt-1 leading-relaxed">
                                    Các bác sĩ giàu kinh nghiệm chuyên môn, từng công tác tại các bệnh viện tuyến đầu cả nước, thấu hiểu tâm lý e ngại của người bệnh.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-12 h-12 bg-white rounded-xl shadow-sm text-clinic-blue flex items-center justify-center border border-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-900 text-base">Công nghệ hiện đại</h4>
                                <p class="text-slate-500 text-sm mt-1 leading-relaxed">
                                    Hệ thống máy xét nghiệm đạt tiêu chuẩn chất lượng quốc tế, cung cấp kết quả chẩn đoán bệnh lý chính xác cao chỉ sau 30-45 phút.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 md:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight font-black">
                    Câu Hỏi Thường Gặp
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Giải đáp nhanh những thắc mắc phổ biến nhất về xét nghiệm bệnh xã hội và quy trình điều trị bảo mật.
                </p>
            </div>

            <!-- Accordion items inside container with Alpine.js -->
            <div x-data="{ active: null }" class="space-y-4 max-w-3xl mx-auto">
                <!-- FAQ Item 1 -->
                <div class="bg-white border border-slate-150 rounded-2xl overflow-hidden shadow-sm transition-all hover:border-slate-300">
                    <button @click="active = (active === 1 ? null : 1)" 
                            class="w-full flex items-center justify-between p-5 md:p-6 text-left font-bold text-slate-900 focus:outline-none transition-colors duration-200">
                        <span class="pr-4 text-sm md:text-base">Chi phí xét nghiệm bệnh xã hội có đắt không?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transform transition-transform duration-300" :class="{'rotate-180': active === 1}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak 
                         class="px-5 md:px-6 pb-6 text-slate-500 text-sm leading-relaxed border-t border-slate-50 pt-4">
                        Chi phí xét nghiệm tại Đa Khoa Gia Phước luôn được niêm yết công khai và tư vấn rõ ràng trước khi thực hiện. Chúng tôi có các gói xét nghiệm từ cơ bản đến nâng cao để phù hợp với nhu cầu và khả năng tài chính của từng bệnh nhân.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white border border-slate-150 rounded-2xl overflow-hidden shadow-sm transition-all hover:border-slate-300">
                    <button @click="active = (active === 2 ? null : 2)" 
                            class="w-full flex items-center justify-between p-5 md:p-6 text-left font-bold text-slate-900 focus:outline-none transition-colors duration-200">
                        <span class="pr-4 text-sm md:text-base">Bệnh xã hội có chữa khỏi hoàn toàn được không?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transform transition-transform duration-300" :class="{'rotate-180': active === 2}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak 
                         class="px-5 md:px-6 pb-6 text-slate-500 text-sm leading-relaxed border-t border-slate-50 pt-4">
                        Nhiều bệnh xã hội (như bệnh lậu, giang mai, chlamydia...) có thể chữa khỏi hoàn toàn nếu được phát hiện sớm và điều trị đúng phác đồ. Với sùi mào gà hay mụn rộp sinh dục, việc điều trị giúp kiểm soát triệu chứng, ngăn ngừa tái phát và lây nhiễm hiệu quả.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white border border-slate-150 rounded-2xl overflow-hidden shadow-sm transition-all hover:border-slate-300">
                    <button @click="active = (active === 3 ? null : 3)" 
                            class="w-full flex items-center justify-between p-5 md:p-6 text-left font-bold text-slate-900 focus:outline-none transition-colors duration-200">
                        <span class="pr-4 text-sm md:text-base">Tôi có cần đặt lịch trước khi đến khám không?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transform transition-transform duration-300" :class="{'rotate-180': active === 3}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak 
                         class="px-5 md:px-6 pb-6 text-slate-500 text-sm leading-relaxed border-t border-slate-50 pt-4">
                        Bạn hoàn toàn có thể đến trực tiếp, tuy nhiên chúng tôi luôn khuyến khích đặt lịch hẹn trước qua website hoặc hotline để được ưu tiên khám ngay, không phải chờ đợi và nhận thêm các chương trình ưu đãi (nếu có).
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Consultation Banner Form anchor link -->
    <section id="consultation-section" class="py-12 bg-slate-900 text-center text-white relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute -right-32 -bottom-32 w-80 h-80 bg-clinic-blue/20 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 space-y-4">
            <h3 class="text-2xl md:text-3xl font-extrabold">Cần tư vấn trực tiếp từ bác sĩ chuyên khoa?</h3>
            <p class="text-sm text-slate-300 max-w-xl mx-auto">Đội ngũ y tá, bác sĩ chuyên môn luôn thường trực để giải đáp mọi câu hỏi thầm kín của bạn hoàn toàn miễn phí.</p>
            <a href="#booking-form" @click="document.querySelector('[name=name]').focus()" 
               class="inline-flex items-center justify-center px-6 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl transition-all shadow-md text-sm mt-2">
                Kết Nối Tư Vấn Ngay
            </a>
        </div>
    </section>
</div>
@endsection
