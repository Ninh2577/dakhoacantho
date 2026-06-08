<!-- Mobile Sticky CTA (Only visible on Mobile/Tablet) -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-3 shadow-2xl flex gap-3">
    <!-- Call Button -->
    <a href="tel:0966332352" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-white border border-clinic-teal text-clinic-teal font-extrabold rounded-xl text-sm transition-all shadow-sm active:scale-95 duration-150">
        <svg class="w-4.5 h-4.5 animate-bounce" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
        </svg>
        Gọi tư vấn
    </a>
    <!-- Booking Button -->
    <a href="{{ route('contact') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-clinic-blue text-white font-extrabold rounded-xl text-sm transition-all shadow-md active:scale-95 duration-150">
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Đặt lịch khám
    </a>
</div>

<!-- Global simplified Footer -->
<footer class="bg-[#242b35] text-slate-400 border-t border-slate-700 py-12 pb-24 md:pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        
        <!-- Clinic Name -->
        <h3 class="text-white text-xl md:text-2xl font-black tracking-tight uppercase">
            Phòng Khám Đa Khoa Gia Phước
        </h3>

        <!-- Clinic Details List -->
        <div class="flex flex-col md:flex-row items-center justify-center gap-6 text-sm font-semibold text-slate-350">
            <!-- Phone -->
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <a href="tel:0966332352" class="text-white hover:text-clinic-teal transition-colors text-base font-bold">0966.332.352</a>
            </div>

            <!-- Separator (hidden on mobile) -->
            <span class="hidden md:inline text-slate-600">|</span>

            <!-- Address -->
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-slate-200">Số 57 Hùng Vương, P.Ninh Kiều, TP.Cần Thơ</span>
            </div>
        </div>

        <!-- Copyright Info -->
        <div class="border-t border-slate-700/60 pt-6 text-xs text-slate-500 font-medium">
            <p>Copyright &copy; {{ date('Y') }} Phòng Khám Đa Khoa Gia Phước. All rights reserved.</p>
        </div>
    </div>
</footer>
