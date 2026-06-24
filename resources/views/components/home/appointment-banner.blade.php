<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-blue-50/60 via-white to-teal-50/40 rounded-3xl border border-slate-100/80 p-8 md:p-12 shadow-sm relative overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-clinic-teal/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-clinic-blue/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-clinic-teal/10 text-clinic-teal uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-clinic-teal animate-ping"></span>
                        Tiết kiệm thời gian
                    </span>

                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight tracking-tight">
                        Đặt lịch khám nhanh, <br class="hidden sm:inline"> chủ động thời gian của bạn
                    </h2>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto lg:mx-0 font-medium">
                        Đội ngũ tư vấn hỗ trợ sắp xếp lịch khám phù hợp, giúp bạn tiết kiệm thời gian chờ đợi khi đến phòng khám. Chỉ mất 1 phút để đăng ký.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @php
                            $zaloUrl = \App\Models\Setting::site('zalo_url') ?: 'https://zalo.me/0966332352';
                        @endphp
                        <a href="{{ $zaloUrl }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-lg shadow-clinic-blue/20 hover:shadow-xl transition-all duration-200 text-sm tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Đặt lịch ngay
                        </a>
                        <a href="tel:{{ preg_replace('/\D/', '', \App\Models\Setting::site('hotline')) }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white border border-slate-200 text-clinic-blue hover:bg-slate-50 font-extrabold rounded-xl text-sm transition-all shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Gọi {{ \App\Models\Setting::site('hotline') }}
                        </a>
                    </div>

                    <!-- Trust factors -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-y-2 gap-x-4 pt-4 border-t border-slate-100/60 text-xs font-bold text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Hỗ trợ tư vấn nhanh</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Bảo mật thông tin</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-clinic-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span>Quy trình rõ ràng</span>
                        </div>
                    </div>
                </div>

                <!-- Right Visual (Mockup Calendar) -->
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-5 w-full max-w-sm relative">
                        <!-- Card Header -->
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-blue-50 text-clinic-blue rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-extrabold text-slate-800 leading-none">Đặt lịch khám</span>
                                    <span class="text-[10px] text-slate-400 font-bold">Thời gian khả dụng</span>
                                </div>
                            </div>
                            <!-- Live status indicator -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Trực tuyến
                            </span>
                        </div>

                        <!-- Mini Calendar Row -->
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <div class="p-2.5 rounded-xl border border-slate-100 text-center bg-slate-50/50">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Th 2</span>
                                <span class="block text-sm font-black text-slate-700">08</span>
                            </div>
                            <div class="p-2.5 rounded-xl border-2 border-clinic-blue text-center bg-blue-50/30">
                                <span class="block text-[10px] font-bold text-clinic-blue uppercase">Th 3</span>
                                <span class="block text-sm font-black text-clinic-blue">H.Nay</span>
                            </div>
                            <div class="p-2.5 rounded-xl border border-slate-100 text-center bg-slate-50/50">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Th 4</span>
                                <span class="block text-sm font-black text-slate-700">10</span>
                            </div>
                            <div class="p-2.5 rounded-xl border border-slate-100 text-center bg-slate-50/50">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase">Th 5</span>
                                <span class="block text-sm font-black text-slate-700">11</span>
                            </div>
                        </div>

                        <!-- Time slots grid -->
                        <div class="space-y-2.5">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Chọn khung giờ khám:</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="px-3 py-2.5 rounded-xl text-center border border-slate-100 text-slate-400 bg-slate-50 line-through text-xs font-bold flex items-center justify-center gap-1">
                                    <span>08:00</span>
                                    <span class="text-[9px] font-medium opacity-80">(Hết)</span>
                                </div>
                                <div class="px-3 py-2.5 rounded-xl text-center border border-clinic-teal bg-clinic-teal text-white text-xs font-extrabold shadow-sm shadow-clinic-teal/20 flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span>09:30</span>
                                </div>
                                <div class="px-3 py-2.5 rounded-xl text-center border border-slate-100 hover:border-clinic-blue text-slate-600 bg-white hover:bg-slate-50 transition-all text-xs font-bold cursor-pointer">
                                    <span>14:00</span>
                                </div>
                                <div class="px-3 py-2.5 rounded-xl text-center border border-slate-100 hover:border-clinic-blue text-slate-600 bg-white hover:bg-slate-50 transition-all text-xs font-bold cursor-pointer">
                                    <span>15:30</span>
                                </div>
                            </div>
                        </div>

                        <!-- Micro trust box inside mockup -->
                        <div class="mt-4 p-3 bg-slate-50 rounded-xl flex items-start gap-2.5 border border-slate-100">
                            <span class="text-clinic-teal mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <span class="text-[11px] text-slate-500 leading-normal font-semibold">
                                Lịch khám được đồng bộ trực tuyến. Bạn sẽ được ưu tiên vào khám trước mà không cần bốc số.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
