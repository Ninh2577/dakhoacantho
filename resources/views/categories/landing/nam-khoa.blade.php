@extends('layouts.app')

@section('title', 'Chuyên Khoa Nam Khoa - Tư Vấn Uy Tín & Riêng Tư | Đa Khoa Gia Phước')

@section('meta')
    <x-seo 
        title="Chuyên Khoa Nam Khoa - Tư Vấn Uy Tín & Riêng Tư | Đa Khoa Gia Phước" 
        description="Hỗ trợ tư vấn và xét nghiệm nam khoa (bao quy đầu, tinh hoàn, yếu sinh lý...) tại Đa Khoa Gia Phước Cần Thơ. Quy trình riêng tư, bảo mật thông tin." 
        canonical="{{ route('category.show', ['category_path' => 'nam-khoa']) }}"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Nam khoa', 'url' => route('category.show', ['category_path' => 'nam-khoa'])]
        ]"
        :faqs="[
            ['q' => 'Khi nào nam giới nên chủ động đi khám nam khoa?', 'a' => 'Nam giới nên đi khám khi xuất hiện các triệu chứng lạ ở bao quy đầu, đau nhức tinh hoàn, giảm ham muốn, xuất tinh sớm hoặc tiểu buốt.'],
            ['q' => 'Chi phí thăm khám nam khoa tại Gia Phước khoảng bao nhiêu?', 'a' => 'Chi phí tùy thuộc vào từng gói dịch vụ và loại xét nghiệm cần thực hiện. Mọi chi phí đều được công khai niêm yết rõ ràng theo quy định.'],
            ['q' => 'Quy trình tư vấn nam khoa có bảo mật không?', 'a' => 'Có, mọi thông tin cá nhân và bệnh án được mã hóa và bảo mật nghiêm ngặt theo quy trình riêng tư nội bộ của phòng khám.']
        ]"
    />
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-slate-100 via-white to-blue-50/40 py-16 lg:py-24 overflow-hidden border-b border-slate-100">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-100/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Headline & Trust Badges -->
                <div class="lg:col-span-7 space-y-6">
                    <nav class="flex text-xs md:text-sm text-slate-500 gap-2 items-center" aria-label="Breadcrumb">
                        <a href="{{ route('home') }}" class="hover:text-clinic-blue transition-colors">Trang chủ</a>
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-clinic-blue">Chuyên Khoa Nam Khoa</span>
                    </nav>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-clinic-blue border border-blue-100 uppercase tracking-wider">
                        Chuyên khoa nam học
                    </span>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight leading-none text-slate-900">
                        Giải Pháp Chăm Sóc Sức Khỏe <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-clinic-blue to-blue-600">Nam Giới Toàn Diện</span>
                    </h1>
 
                     <p class="text-slate-650 text-base md:text-lg leading-relaxed max-w-xl font-medium">
                         Với quy trình hỗ trợ tư vấn chuyên nghiệp và công nghệ y khoa hiện đại, chúng tôi cam kết mang lại sự riêng tư, chính xác và hiệu quả tối ưu cho từng trường hợp.
                     </p>

                    <!-- Trust badges -->
                    <div class="flex flex-wrap gap-4 pt-2">
                        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-100 shadow-sm">
                            <svg class="w-5 h-5 text-clinic-sky" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-xs font-bold text-slate-800">Bảo mật tuyệt đối</span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-100 shadow-sm">
                            <svg class="w-5 h-5 text-clinic-sky" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-xs font-bold text-slate-800">Phác đồ chuẩn quốc tế</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Booking Form Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl text-slate-800 border border-slate-100 relative">
                        <h3 class="text-xl font-extrabold text-slate-900 text-center mb-1">
                            Đặt Lịch Hẹn Ngay
                        </h3>
                        <p class="text-slate-500 text-xs text-center mb-6">
                            Thông tin của bạn sẽ được bảo mật 100%.
                        </p>

                        @if(session('success'))
                            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 p-4 rounded-xl mb-6 text-xs font-bold">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form id="booking-form-namkhoa" action="{{ route('consultation.store') }}" method="POST" onsubmit="mergeNamKhoaFields()" class="space-y-4">
                            @csrf
                            <input type="hidden" name="department" value="Nam Khoa">
                            <input type="hidden" id="nam-symptoms-hidden" name="symptoms" value="">

                            <!-- Họ và tên -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Họ và tên *</label>
                                <input type="text" name="name" required placeholder="Nguyễn Văn A" 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                                @error('name')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số điện thoại *</label>
                                <input type="tel" name="phone" required placeholder="090 123 4567" 
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                                @error('phone')
                                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dịch vụ quan tâm -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dịch vụ quan tâm</label>
                                <select id="nam-service-select" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white focus:ring-1 focus:ring-clinic-blue/20 outline-none text-slate-800 text-sm font-semibold rounded-xl transition-all">
                                    <option value="Khám tổng quát nam khoa">Khám tổng quát nam khoa</option>
                                    <option value="Cắt bao quy đầu">Cắt bao quy đầu</option>
                                    <option value="Bệnh lý tinh hoàn">Bệnh lý tinh hoàn</option>
                                    <option value="Yếu sinh lý / Rối loạn cương dương">Yếu sinh lý / Rối loạn cương dương</option>
                                    <option value="Bệnh lây truyền qua đường tình dục">Bệnh lây truyền qua đường tình dục</option>
                                </select>
                            </div>

                            <button type="submit" 
                                    class="w-full py-4 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all text-sm tracking-wide">
                                Gửi yêu cầu đặt hẹn
                            </button>
                        </form>
                        <script>
                            function mergeNamKhoaFields() {
                                const service = document.getElementById('nam-service-select').value;
                                document.getElementById('nam-symptoms-hidden').value = `Nhu cầu: ${service}`;
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dịch Vụ Điều Trị Chuyên Sâu -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Dịch Vụ Điều Trị Chuyên Sâu
                </h2>
                <div class="w-16 h-1 bg-clinic-blue mx-auto rounded-full"></div>
                <p class="text-slate-500 text-base md:text-lg">
                    Các gói khám và điều trị đa dạng, đáp ứng toàn diện nhu cầu chăm sóc sức khỏe của nam giới.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-blue-50/10 rounded-3xl border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center mb-6">
                            <!-- Medical Cross Badge SVG -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-clinic-blue transition-colors">Bao Quy Đầu</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Hỗ trợ tiểu phẫu bao quy đầu bằng kỹ thuật xâm lấn tối thiểu hiện đại, giảm thiểu đau đớn, thẩm mỹ cao, phục hồi nhanh chóng.
                        </p>
                    </div>
                    <a href="#booking-form-namkhoa" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Tìm hiểu thêm &rarr;
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-blue-50/10 rounded-3xl border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center mb-6">
                            <!-- Shield SVG -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-clinic-blue transition-colors">Bệnh Tinh Hoàn</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Chẩn đoán và điều trị hiệu quả viêm tinh hoàn, giãn tĩnh mạch thừng tinh, tràn dịch màng tinh hoàn.
                        </p>
                    </div>
                    <a href="#booking-form-namkhoa" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Tìm hiểu thêm &rarr;
                    </a>
                </div>

                <!-- Card 3 -->
                <div class="bg-blue-50/10 rounded-3xl border border-slate-100 p-8 hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-clinic-blue flex items-center justify-center mb-6">
                            <!-- Lightning SVG -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-clinic-blue transition-colors">Yếu Sinh Lý</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">
                            Hỗ trợ cải thiện tình trạng xuất tinh sớm, rối loạn cương dương qua tư vấn liệu pháp tâm lý và chăm sóc sức khỏe phù hợp.
                        </p>
                    </div>
                    <a href="#booking-form-namkhoa" class="inline-flex items-center text-clinic-blue font-bold text-sm hover:underline">
                        Tìm hiểu thêm &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Tại sao hàng ngàn nam giới tin tưởng Gia Phước? -->
    <section class="py-16 lg:py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute left-1/4 top-1/4 w-96 h-96 bg-clinic-blue/20 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Bullet points -->
                <div class="lg:col-span-6 space-y-8">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                            Tại sao hàng ngàn nam giới tin tưởng Gia Phước?
                        </h2>
                        <div class="w-12 h-1 bg-clinic-sky mt-4 rounded-full"></div>
                    </div>

                    <div class="space-y-6">
                        <!-- Bullet 1 -->
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-10 h-10 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center text-clinic-sky">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div>
                                <h4 class="font-bold text-white text-base">Bảo mật thông tin</h4>
                                <p class="text-slate-350 text-sm mt-1 leading-relaxed">
                                    Mọi hồ sơ bệnh án được mã hóa kỹ thuật số, bảo vệ tuyệt mật quyền riêng tư của khách hàng.
                                </p>
                            </div>
                        </div>

                         <!-- Bullet 2 -->
                         <div class="flex items-start gap-4">
                             <span class="flex-shrink-0 w-10 h-10 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center text-clinic-sky">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                 </svg>
                             </span>
                             <div>
                                 <h4 class="font-bold text-white text-base">Đội ngũ hỗ trợ chuyên môn</h4>
                                 <p class="text-slate-350 text-sm mt-1 leading-relaxed">
                                     Đội ngũ tư vấn viên am hiểu y khoa, hỗ trợ giải đáp thắc mắc và định hướng lộ trình khám phù hợp.
                                 </p>
                             </div>
                         </div>
 
                         <!-- Bullet 3 -->
                         <div class="flex items-start gap-4">
                             <span class="flex-shrink-0 w-10 h-10 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center text-clinic-sky">
                                 <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                 </svg>
                             </span>
                             <div>
                                 <h4 class="font-bold text-white text-base">Công nghệ tiên tiến</h4>
                                 <p class="text-slate-350 text-sm mt-1 leading-relaxed">
                                     Trang thiết bị hiện đại nhập khẩu trực tiếp từ Mỹ, Đức, Hàn Quốc hỗ trợ chẩn đoán chính xác.
                                 </p>
                             </div>
                         </div>
                     </div>
                 </div>
 
                 <!-- Right: Surgery image with badge -->
                 <div class="lg:col-span-6 relative">
                     <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-800 aspect-[4/3] bg-slate-800">
                         <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?auto=format&fit=crop&q=80&w=800" 
                              alt="Thiết bị hiện đại Gia Phước" class="w-full h-full object-cover" loading="lazy" decoding="async">
                     </div>
                     <!-- Overlapping Blue badge bottom left -->
                     <div class="absolute -bottom-6 left-6 bg-clinic-blue text-white p-5 rounded-2xl shadow-2xl border border-white/10 max-w-[200px]">
                         <h4 class="text-2xl font-black">Bảo mật</h4>
                         <p class="text-xs text-blue-200 mt-1 leading-normal font-bold">Riêng tư & Tin cậy</p>
                     </div>
                 </div>
             </div>
         </div>
     </section>

    <!-- Không gian tư vấn riêng tư & Cơ sở vật chất -->
    <section class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Info -->
                <div class="space-y-6">
                    <span class="text-xs font-bold text-clinic-blue uppercase tracking-widest">Không gian riêng tư</span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Không Gian Tư Vấn Kín Đáo & Cơ Sở Hiện Đại
                    </h2>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Phòng Khám Đa Khoa Gia Phước xây dựng mô hình phòng khám riêng tư, giúp khách hàng hoàn toàn thoải mái chia sẻ các lo lắng về sức khỏe nam giới trong môi trường kín đáo và thân thiện.
                    </p>
                    <ul class="space-y-3 text-xs font-bold text-slate-600">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-clinic-teal"></span>
                            Hỗ trợ tư vấn riêng tư, bảo vệ thông tin bệnh án tuyệt mật.
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-clinic-teal"></span>
                            Trang thiết bị chẩn đoán hình ảnh cao cấp nhập khẩu đồng bộ.
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-clinic-teal"></span>
                            Quy trình hỗ trợ đặt hẹn nhanh, hạn chế thời gian chờ đợi.
                        </li>
                    </ul>
                </div>
                <!-- Right: Visual -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl overflow-hidden h-48 bg-slate-100 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&q=80&w=400" alt="Phòng khám riêng tư" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    </div>
                    <div class="rounded-2xl overflow-hidden h-48 bg-slate-100 shadow-sm translate-y-4">
                        <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?auto=format&fit=crop&q=80&w=400" alt="Thiết bị y tế" class="w-full h-full object-cover" loading="lazy" decoding="async">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles Section -->
    <x-related-articles-carousel
        title="Bài viết liên quan về Nam khoa"
        subtitle="Thông tin tham khảo về sức khỏe nam giới, dấu hiệu bất thường và cách chủ động thăm khám."
        :articles="$relatedArticles"
        :viewAllUrl="route('categories.index')"
    />

    <!-- Bottom CTA Banner -->
    <section class="py-16 bg-gradient-to-r from-clinic-blue to-[#0b4c8c] text-white relative overflow-hidden text-center border-t border-white/5">
        <div class="absolute inset-0 bg-grid-white/[0.02] z-0"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 space-y-6">
            <h3 class="text-2xl md:text-4xl font-extrabold tracking-tight text-white">
                Đừng để lo lắng cản trở cuộc sống của bạn
            </h3>
            <p class="text-sm md:text-base text-blue-150 max-w-xl mx-auto font-medium">
                Liên hệ ngay để được tư vấn bảo mật cùng đội ngũ hỗ trợ nam học tại Phòng Khám Đa Khoa Gia Phước.
            </p>
            <div class="flex flex-wrap justify-center gap-4 pt-2">
                <a href="tel:0966332352" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-clinic-blue font-extrabold rounded-xl transition-all shadow-md text-sm gap-2">
                    <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.502-5.174-3.852-6.678-6.679l1.293-.97a1.243 1.243 0 00.37-1.173L6.745 3.34a1.243 1.243 0 00-1.202-.852H3.75a2.25 2.25 0 00-2.25 2.25v1.372z"></path>
                    </svg>
                    Gọi tư vấn: 0966.332.352
                </a>
                <a href="#booking-form-namkhoa" class="inline-flex items-center justify-center px-6 py-3.5 bg-clinic-sky hover:bg-opacity-95 text-white font-extrabold rounded-xl transition-all text-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Đăng ký tư vấn
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
