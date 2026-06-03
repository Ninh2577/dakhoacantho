@extends('layouts.app')

@section('title', 'Liên Hệ & Tư Vấn Miễn Phí | Đa Khoa Gia Phước')

@section('content')
<div class="py-8 md:py-16 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-6 text-xs md:text-sm text-slate-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-clinic-blue inline-flex items-center gap-1 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 012 0v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Trang chủ
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-slate-400 font-medium">Liên hệ</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left Info Area (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-10">
                
                <!-- Map / Smartphone mockup container -->
                <div class="bg-gradient-to-br from-blue-50 to-teal-50/50 rounded-3xl p-6 md:p-8 border border-slate-100 flex flex-col md:flex-row items-center gap-8 shadow-sm">
                    <div class="relative w-36 h-64 bg-slate-900 border-[6px] border-slate-800 rounded-[32px] overflow-hidden shadow-inner flex-shrink-0">
                        <!-- Simulated Map -->
                        <div class="absolute inset-0 bg-slate-100 flex items-center justify-center">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Bản đồ Cần Thơ</span>
                            <!-- Map Pin Icon -->
                            <span class="absolute w-5 h-5 bg-clinic-blue rounded-full border-2 border-white animate-bounce flex items-center justify-center shadow-md">
                                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-4 text-center md:text-left flex-grow">
                        <div class="flex items-center gap-2 justify-center md:justify-start">
                            <span class="p-2 bg-clinic-blue text-white rounded-xl shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </span>
                            <span class="text-base font-extrabold text-slate-900">Cơ sở chính</span>
                        </div>
                        
                        <p class="text-sm font-bold text-slate-700 leading-normal max-w-sm">
                            57 Hùng Vương, P. Thới Bình, Q. Ninh Kiều, TP. Cần Thơ.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <a href="https://maps.google.com/?q=57+Hùng+Vương,+Cần+Thơ" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl text-xs shadow-md transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                Chỉ đường
                            </a>
                            <a href="tel:0933496986" class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-clinic-blue font-extrabold rounded-xl text-xs shadow-sm transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Gọi điện thoại
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Consultation Contacts info block -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-clinic-sky/10 text-clinic-blue uppercase tracking-wider">
                            Thông tin liên hệ
                        </span>
                        <h2 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">
                            Chúng tôi luôn sẵn sàng hỗ trợ bạn
                        </h2>
                        <p class="text-sm text-slate-600 leading-relaxed max-w-xl">
                            Phòng Khám Đa Khoa Gia Phước tự hào là cơ sở y tế uy tín hàng đầu tại Đồng bằng sông Cửu Long, nơi quy tụ đội ngũ y bác sĩ chuyên môn cao và trang thiết bị hiện đại nhất.
                        </p>
                    </div>

                    <!-- Details items list -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <!-- Contact Item 1 -->
                        <div class="flex items-start gap-3">
                            <span class="p-3 bg-clinic-sky/10 text-clinic-blue rounded-xl flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tư vấn sức khỏe</span>
                                <span class="block text-sm font-extrabold text-slate-900">0966.332.352</span>
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">(Zalo, Viber, Line)</span>
                            </div>
                        </div>

                        <!-- Contact Item 2 -->
                        <div class="flex items-start gap-3">
                            <span class="p-3 bg-clinic-sky/10 text-clinic-blue rounded-xl flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </span>
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Giờ làm việc</span>
                                <span class="block text-sm font-extrabold text-slate-900">7h30 - 20h00</span>
                                <span class="block text-[10px] text-slate-500 font-bold uppercase">(Tất cả các ngày trong tuần, kể cả Lễ)</span>
                            </div>
                        </div>

                        <!-- Contact Item 3 -->
                        <div class="flex items-start gap-3">
                            <span class="p-3 bg-clinic-sky/10 text-clinic-blue rounded-xl flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L22 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </span>
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Email liên hệ</span>
                                <span class="block text-sm font-extrabold text-slate-900">info@dakhoagiaphuoc.vn</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Area (Consultation Form) (lg:col-span-5) -->
            <div class="lg:col-span-5">
                <x-consultation-form />
            </div>

        </div>

    </div>
</div>
@endsection
