<!-- Fixed Bottom Navigation Bar for Mobile (Matches Mockups) -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-100 shadow-lg py-2">
    <div class="grid grid-cols-4 text-center">
        <a href="{{ route('contact') }}" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-[10px] font-bold">Đặt hẹn</span>
        </a>
        <a href="#" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <span class="text-[10px] font-bold">Chat</span>
        </a>
        <a href="tel:0933496986" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            <span class="text-[10px] font-bold">Gọi điện</span>
        </a>
        <a href="{{ route('contact') }}" class="flex flex-col items-center justify-center text-slate-500 hover:text-clinic-blue">
            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-[10px] font-bold">Vị trí</span>
        </a>
    </div>
</div>

<!-- Global Footer -->
<footer class="bg-[#242b35] text-slate-400 border-t border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
            
            <!-- Column 1: Identity & General Contacts -->
            <div class="space-y-4">
                <h3 class="text-white text-lg font-black tracking-tight">Da Khoa Gia Phước</h3>
                <p class="text-sm leading-relaxed">
                    Địa chỉ y tế tin cậy tại miền Tây với dịch vụ tận tâm và chuyên nghiệp nhất.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors" aria-label="Social Link">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors" aria-label="Social Link">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12.713l-11.985-9.713h23.97l-11.985 9.713zm0 2.574l12-9.725v15.438h-24v-15.438l12 9.725z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Quick Links (Chuyên Khoa) -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Về Chúng Tôi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Giới thiệu</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Đội ngũ bác sĩ</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Cơ sở vật chất</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Hợp tác quốc tế</a></li>
                </ul>
            </div>

            <!-- Column 3: Corporate Policies -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Chính Sách</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Điều khoản sử dụng</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Chính sách bảo mật</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Sơ đồ website</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Quy trình khám bệnh</a></li>
                </ul>
            </div>

            <!-- Column 4: Specific Location details -->
            <div>
                <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Liên Hệ</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-clinic-teal flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>57 Hùng Vương, P. Thới Bình, Q. Ninh Kiều, TP. Cần Thơ</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="font-extrabold text-white">0933 49 69 86</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-clinic-teal flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>07:30 - 20:00 (Hàng ngày)</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="border-t border-slate-700 mt-12 pt-8 text-center text-xs space-y-2">
            <p>Copyright &copy; {{ date('Y') }} Đa Khoa Cần Thơ. All rights reserved.</p>
        </div>
    </div>
</footer>
