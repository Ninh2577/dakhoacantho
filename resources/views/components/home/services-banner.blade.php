@php
    $services = [
        [
            'label' => 'Nam khoa',
            'desc' => 'Yếu sinh lý, bao quy đầu, viêm đường tiết niệu',
            'url' => route('category.show', ['category_path' => 'nam-khoa']),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
        ],
        [
            'label' => 'Phụ khoa',
            'desc' => 'Viêm nhiễm âm đạo, rối loạn kinh nguyệt, tầm soát tử cung',
            'url' => route('category.show', ['category_path' => 'phu-khoa']),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>'
        ],
        [
            'label' => 'Bệnh xã hội',
            'desc' => 'Xét nghiệm sùi mào gà, lậu, giang mai bảo mật tuyệt đối',
            'url' => route('category.show', ['category_path' => 'benh-xa-hoi']),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
        ],
        [
            'label' => 'Hậu môn - Trực tràng',
            'desc' => 'Cắt trĩ bằng PPH, HCPT không đau, hồi phục nhanh',
            'url' => route('category.show', ['category_path' => 'hau-mon-truc-trang']),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
        ],
        [
            'label' => 'Ngoại khoa',
            'desc' => 'Tiểu phẫu ngoại khoa, cắt bao quy đầu thẩm mỹ vô trùng',
            'url' => route('category.show', ['category_path' => 'ngoai-khoa']),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>'
        ],
        [
            'label' => 'Tư vấn sức khỏe',
            'desc' => 'Hỏi đáp triệu chứng, tư vấn trực tuyến miễn phí 24/7',
            'url' => route('contact'),
            'icon' => '<svg class="w-6 h-6 text-clinic-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'
        ]
    ];
@endphp

<section class="py-16 bg-slate-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-white to-slate-50 border border-slate-100 rounded-3xl p-8 md:p-12 shadow-sm relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -left-16 -top-16 w-32 h-32 bg-clinic-blue/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left column: Banner info -->
                <div class="lg:col-span-4 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-clinic-blue/5 text-clinic-blue uppercase tracking-wider">
                        Phòng khám Gia Phước
                    </span>
                    
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight tracking-tight">
                        Dịch vụ khám chữa bệnh toàn diện
                    </h2>
                    
                    <p class="text-slate-600 text-sm leading-relaxed font-medium">
                        Chúng tôi cung cấp các gói dịch vụ thăm khám chuyên khoa khép kín, ứng dụng công nghệ y tế tiên tiến đảm bảo chẩn đoán chính xác và điều trị hiệu quả nhất.
                    </p>

                    <div class="pt-2">
                        <a href="{{ route('categories.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-md shadow-clinic-blue/15 hover:shadow-lg transition-all duration-200 text-sm">
                            Xem tất cả chuyên khoa
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right column: Services Chips Grid -->
                <div class="lg:col-span-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($services as $service)
                            <a href="{{ $service['url'] }}" class="flex items-start gap-4 p-5 bg-white border border-slate-100 hover:border-clinic-teal hover:shadow-md rounded-2xl transition-all duration-300 group">
                                <div class="p-3 bg-slate-50 group-hover:bg-clinic-teal/10 rounded-xl transition-colors flex-shrink-0">
                                    {!! $service['icon'] !!}
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-sm font-extrabold text-slate-950 group-hover:text-clinic-teal transition-colors">
                                        {{ $service['label'] }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                        {{ $service['desc'] }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
