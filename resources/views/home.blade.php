@extends('layouts.app')

@section('title', 'Phòng Khám Đa Khoa Gia Phước | Uy Tín - Tận Tâm')

@section('meta')
    <x-seo 
        title="Phòng Khám Đa Khoa Gia Phước | Uy Tín - Tận Tâm" 
        description="Phòng Khám Đa Khoa Gia Phước - Địa chỉ khám chữa bệnh uy tín, riêng tư và chuyên nghiệp hàng đầu tại Cần Thơ. Đăng ký tư vấn trực tuyến nhanh chóng." 
        canonical="{{ url('/') }}"
        :breadcrumbs="[['name' => 'Trang chủ', 'url' => url('/')]]"
        :faqs="[
            ['q' => 'Thời gian làm việc của phòng khám là khi nào?', 'a' => 'Phòng Khám Đa Khoa Gia Phước hoạt động từ 07:30 đến 20:00 tất cả các ngày trong tuần, kể cả ngày lễ.'],
            ['q' => 'Thông tin đăng ký có được bảo mật không?', 'a' => 'Mọi hồ sơ đăng ký và bệnh án của bệnh nhân đều được bảo mật tuyệt đối theo quy trình khép kín nội bộ.'],
            ['q' => 'Tôi có thể liên hệ bằng số hotline nào?', 'a' => 'Vui lòng liên hệ trực tiếp qua số hotline 0966.332.352 để nhận tư vấn và hướng dẫn kịp thời.']
        ]"
    />
@endsection

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 via-white to-teal-50/30 py-12 md:py-20 overflow-hidden">
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
                    <a href="{{ route('contact') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all text-sm tracking-wide">
                        Đặt lịch tư vấn ngay
                    </a>
                    <a href="tel:0966332352" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white border border-slate-200 text-clinic-blue hover:bg-slate-50 font-extrabold rounded-xl text-sm transition-all shadow-sm">
                        Gọi tư vấn: 0966.332.352
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
                    <img src="{{ asset('images/doctor.png') }}" alt="Phòng Khám Đa Khoa Gia Phước" class="w-full h-auto object-cover max-h-[500px]">
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

<x-home.appointment-banner />

<!-- Specialties Section ("Chuyên khoa mũi nhọn") -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12 md:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Lĩnh vực hoạt động chính</h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Chúng tôi cung cấp các dịch vụ tư vấn sức khỏe chuyên sâu và quy trình điều trị cá nhân hóa tối ưu.
            </p>
        </div>

        <!-- Specialty Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Card 1: Nam khoa -->
            <x-specialty-card 
                title="Nam khoa" 
                description="Điều trị yếu sinh lý, xuất tinh sớm và các bệnh lý nam khoa chuyên sâu."
                slug="nam-khoa"
                bgImage="{{ asset('images/nam_khoa.png') }}"
                icon='<svg class="w-6 h-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>'
            />
            <!-- Card 2: Phụ khoa -->
            <x-specialty-card 
                title="Phụ khoa" 
                description="Chăm sóc sức khỏe phụ nữ toàn diện và tầm soát bệnh lý phụ khoa định kỳ."
                slug="phu-khoa"
                bgImage="{{ asset('images/phu_khoa.png') }}"
                icon='<svg class="w-6 h-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>'
            />
            <!-- Card 3: Bệnh xã hội -->
            <x-specialty-card 
                title="Bệnh xã hội" 
                description="Xét nghiệm bảo mật tuyệt đối, điều trị dứt điểm sùi mào gà, lậu, giang mai."
                slug="benh-xa-hoi"
                bgImage="{{ asset('images/benh_xa_hoi.png') }}"
                icon='<svg class="w-6 h-6 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
            />
        </div>

        <!-- Highlight specialty banner -->
        <div class="mt-8 md:mt-12 p-6 md:p-8 bg-slate-50 rounded-3xl border border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <span class="p-3 bg-clinic-blue text-white rounded-2xl shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </span>
                <div>
                    <h3 class="text-base md:text-lg font-extrabold text-slate-900">Hậu môn - Trực tràng</h3>
                    <p class="text-xs md:text-sm text-slate-500 leading-normal">Chẩn đoán trĩ nội, trĩ ngoại và rò hậu môn bằng phương pháp HCPT hiện đại, giảm thiểu xâm lấn.</p>
                </div>
            </div>
            <a href="{{ route('category.show', ['category_path' => 'nam-khoa']) }}" class="inline-flex items-center text-xs font-extrabold text-clinic-teal hover:underline">
                <span>Tìm hiểu quy trình điều trị</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

    </div>
</section>

<x-home.services-banner />

<!-- Vì sao chọn Gia Phước? Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Vì sao chọn Gia Phước?</h2>
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

<!-- Quy trình thăm khám Section -->
<section class="py-16 md:py-24 bg-slate-50/50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Quy trình thăm khám</h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                4 bước đơn giản giúp bạn tiết kiệm thời gian chờ đợi khi đến phòng khám.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <!-- step 1 -->
            <div class="relative flex flex-col items-center text-center space-y-4">
                <span class="w-16 h-16 rounded-full bg-clinic-blue text-white font-extrabold flex items-center justify-center text-lg shadow-md">1</span>
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">Gửi thông tin tư vấn</h3>
                <p class="text-slate-500 text-xs leading-relaxed font-semibold">Đăng ký thông tin liên hệ và tình trạng sức khỏe qua form hoặc hotline.</p>
            </div>
            <!-- step 2 -->
            <div class="relative flex flex-col items-center text-center space-y-4">
                <span class="w-16 h-16 rounded-full bg-clinic-blue text-white font-extrabold flex items-center justify-center text-lg shadow-md">2</span>
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">Xác nhận lịch hẹn</h3>
                <p class="text-slate-500 text-xs leading-relaxed font-semibold">Đội ngũ tư vấn gọi điện xác nhận mốc thời gian khám phù hợp.</p>
            </div>
            <!-- step 3 -->
            <div class="relative flex flex-col items-center text-center space-y-4">
                <span class="w-16 h-16 rounded-full bg-clinic-blue text-white font-extrabold flex items-center justify-center text-lg shadow-md">3</span>
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">Thăm khám theo lịch</h3>
                <p class="text-slate-500 text-xs leading-relaxed font-semibold">Đến phòng khám theo giờ đã hẹn, được ưu tiên hướng dẫn làm thủ tục.</p>
            </div>
            <!-- step 4 -->
            <div class="relative flex flex-col items-center text-center space-y-4">
                <span class="w-16 h-16 rounded-full bg-clinic-blue text-white font-extrabold flex items-center justify-center text-lg shadow-md">4</span>
                <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wide">Nhận hướng dẫn phù hợp</h3>
                <p class="text-slate-500 text-xs leading-relaxed font-semibold">Nhận kết quả chẩn đoán và hướng dẫn chăm sóc y tế chi tiết.</p>
            </div>
        </div>
    </div>
</section>

<!-- Medical Knowledge Section ("Kiến thức y khoa") -->
<section class="py-16 md:py-24 bg-slate-50/50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12 md:mb-16">
            <div class="text-center md:text-left space-y-2">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Kiến thức y khoa</h2>
                <p class="text-slate-600 text-sm sm:text-base leading-normal">
                    Cập nhật những thông tin mới nhất về sức khỏe và các lời khuyên từ đội ngũ tư vấn sức khỏe.
                </p>
            </div>
            <a href="{{ route('category.show', ['category_path' => 'nam-khoa']) }}" class="inline-flex items-center px-4 py-2 border border-slate-200 hover:bg-slate-50 text-xs font-extrabold text-slate-700 rounded-xl transition-all shadow-sm">
                Xem tất cả chuyên khoa
            </a>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @forelse($articles as $article)
                <x-article-card :article="$article" />
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    Chưa có bài viết nào được đăng tải.
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- Patient Reviews Section ("Ý kiến từ bệnh nhân") -->
<section class="py-16 md:py-24 bg-[#0a3875] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-12 md:mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black tracking-tight text-white">Ý kiến từ bệnh nhân</h2>
            <p class="text-slate-200 text-sm sm:text-base leading-relaxed">
                Niềm tin và sự hồi phục của người bệnh là thước đo chính xác nhất cho uy tín của chúng tôi.
            </p>
        </div>

        <!-- Reviews Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <!-- Review 1 -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 space-y-4">
                <div class="flex text-teal-400">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    @endfor
                </div>
                <p class="text-sm italic leading-relaxed text-slate-200">
                    "Dịch vụ rất chuyên nghiệp, đội ngũ nhân viên tư vấn tận tình, không làm tôi cảm thấy e ngại. Cơ sở vật chất rất hiện đại và sạch sẽ."
                </p>
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-teal-400 text-[#0a3875] font-black flex items-center justify-center text-xs">AT</span>
                    <div>
                        <span class="block text-xs font-bold">Anh Nguyễn V. T.</span>
                        <span class="block text-[10px] text-slate-300">Cần Thơ</span>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 space-y-4">
                <div class="flex text-teal-400">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    @endfor
                </div>
                <p class="text-sm italic leading-relaxed text-slate-200">
                    "Tôi đã điều trị trĩ tại đây bằng phương pháp HCPT, rất nhanh chóng và nhẹ nhàng. Rất cảm ơn đội ngũ hỗ trợ tại phòng khám."
                </p>
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-teal-400 text-[#0a3875] font-black flex items-center justify-center text-xs">TM</span>
                    <div>
                        <span class="block text-xs font-bold">Chị Trần H. M.</span>
                        <span class="block text-[10px] text-slate-300">Vĩnh Long</span>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 space-y-4">
                <div class="flex text-teal-400">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    @endfor
                </div>
                <p class="text-sm italic leading-relaxed text-slate-200">
                    "Thủ tục nhanh gọn, bảo mật thông tin tuyệt đối. Chi phí cũng rất rõ ràng, không có phí ẩn trong quá trình điều trị."
                </p>
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-teal-400 text-[#0a3875] font-black flex items-center justify-center text-xs">LB</span>
                    <div>
                        <span class="block text-xs font-bold">Chị Lê T. B.</span>
                        <span class="block text-[10px] text-slate-300">Sóc Trăng</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- FAQ Section -->
<section class="py-16 md:py-24 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Câu hỏi thường gặp</h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-semibold">
                Giải đáp nhanh một số lo lắng thường gặp của quý khách hàng khi thăm khám.
            </p>
        </div>

        <div x-data="{ active: null }" class="max-w-3xl mx-auto space-y-4">
            <!-- FAQ 1 -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl overflow-hidden">
                <button @click="active = (active === 1 ? null : 1)" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-905 outline-none focus:outline-none">
                    <span class="text-sm">Phòng khám có làm việc cuối tuần không?</span>
                    <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{'rotate-180': active === 1}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="active === 1" x-cloak class="px-5 pb-5 text-slate-500 text-xs leading-relaxed font-semibold pt-2 border-t border-slate-100/50">
                    Phòng Khám Đa Khoa Gia Phước mở cửa từ 07:30 đến 20:00 tất cả các ngày trong tuần, kể cả ngày Thứ 7, Chủ Nhật và các ngày nghỉ Lễ.
                </div>
            </div>
            <!-- FAQ 2 -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl overflow-hidden">
                <button @click="active = (active === 2 ? null : 2)" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-905 outline-none focus:outline-none">
                    <span class="text-sm">Quy trình đăng ký hẹn trước như thế nào?</span>
                    <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{'rotate-180': active === 2}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="active === 2" x-cloak class="px-5 pb-5 text-slate-500 text-xs leading-relaxed font-semibold pt-2 border-t border-slate-100/50">
                    Quý khách chỉ cần điền họ tên, số điện thoại vào form đăng ký trên website hoặc gọi trực tiếp đến hotline 0966.332.352. Đội ngũ nhân viên tư vấn sẽ liên hệ lại ngay để sắp xếp khung giờ khám thuận tiện nhất.
                </div>
            </div>
            <!-- FAQ 3 -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl overflow-hidden">
                <button @click="active = (active === 3 ? null : 3)" class="w-full flex items-center justify-between p-5 text-left font-bold text-slate-905 outline-none focus:outline-none">
                    <span class="text-sm">Thông tin cá nhân có được giữ bí mật không?</span>
                    <svg class="w-5 h-5 text-slate-400 transform transition-transform" :class="{'rotate-180': active === 3}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="active === 3" x-cloak class="px-5 pb-5 text-slate-500 text-xs leading-relaxed font-semibold pt-2 border-t border-slate-100/50">
                    Đa Khoa Gia Phước cam kết bảo mật tuyệt đối mọi thông tin đăng ký, hồ sơ bệnh án và lịch sử trao đổi của khách hàng theo quy trình khép kín nội bộ.
                </div>
            </div>
        </div>
    </div>
</section>

<x-home.contact-banner />
@endsection
