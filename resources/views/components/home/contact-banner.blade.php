<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-clinic-blue via-clinic-blue to-[#07244e] text-white rounded-3xl p-8 md:p-12 shadow-xl relative overflow-hidden">
            <!-- Background mesh/glow overlay -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-clinic-teal/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -top-20 w-80 h-80 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-teal-300 uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-300"></span>
                        Liên hệ trực tiếp
                    </span>

                    <h2 class="text-2xl sm:text-3xl font-black leading-tight tracking-tight text-white">
                        Cần tư vấn sức khỏe? <br class="hidden sm:inline"> Đa Khoa Cần Thơ luôn sẵn sàng hỗ trợ
                    </h2>

                    <p class="text-blue-100 text-sm sm:text-base leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                        Liên hệ ngay với đội ngũ tư vấn viên chuyên nghiệp của chúng tôi để được giải đáp mọi thắc mắc về triệu chứng bệnh, chi phí và đặt lịch khám nhanh chóng.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="tel:{{ preg_replace('/\D/', '', \App\Models\Setting::site('hotline')) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white hover:bg-slate-50 text-clinic-blue font-extrabold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 text-sm group">
                            <!-- Custom phone icon that shakes slightly on hover -->
                            <svg class="w-4 h-4 mr-2 text-clinic-teal animate-bounce group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Gọi tư vấn ngay
                        </a>
                        <a href="{{ \App\Models\Setting::site('google_maps_url') }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-extrabold rounded-xl text-sm transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Xem chỉ đường
                        </a>
                    </div>
                </div>

                <!-- Right Visual: Glassmorphism Info Card -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-6 md:p-8 w-full max-w-sm space-y-5 shadow-lg">
                        <div class="space-y-1">
                            <span class="block text-[10px] uppercase font-bold text-teal-300 tracking-wider">Thông tin chính thức</span>
                            <h3 class="text-base md:text-lg font-black text-white leading-tight">
                                {{ \App\Models\Setting::site('clinic_name') }}
                            </h3>
                        </div>

                        <div class="space-y-4 text-xs font-semibold text-blue-50">
                            <!-- Info item: Phone -->
                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-white/10 rounded-lg text-teal-300 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </span>
                                <div>
                                    <span class="block text-[10px] text-blue-200 uppercase font-bold">Hotline đặt hẹn</span>
                                    <span class="text-sm font-black text-white">{{ \App\Models\Setting::site('hotline') }}</span>
                                </div>
                            </div>

                            <!-- Info item: Address -->
                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-white/10 rounded-lg text-teal-300 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>
                                <div class="space-y-0.5">
                                    <span class="block text-[10px] text-blue-200 uppercase font-bold">Địa chỉ phòng khám</span>
                                    <span class="text-xs font-bold text-white leading-normal">
                                        {{ \App\Models\Setting::site('address') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Info item: Support Hours -->
                            <div class="flex items-start gap-3">
                                <span class="p-2 bg-white/10 rounded-lg text-teal-300 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                <div>
                                    <span class="block text-[10px] text-blue-200 uppercase font-bold">Thời gian làm việc</span>
                                    <span class="text-xs font-bold text-white">Tư vấn 24/7 (Cả ngày lễ)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
