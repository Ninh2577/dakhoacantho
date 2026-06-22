<div x-data="{ submitting: false, name: '{{ old('name') }}', phone: '{{ old('phone') }}', isValidPhone() { return /^(03|05|07|08|09)\d{8}$/.test(this.phone); } }" 
     class="bg-white rounded-3xl border border-slate-100 shadow-lg p-6 md:p-8 space-y-6">
    <div class="text-center md:text-left space-y-2">
        <h3 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Hỏi đáp triệu chứng bệnh</h3>
        <p class="text-xs md:text-sm text-slate-500 leading-normal font-semibold">
            Vui lòng điền thông tin, đội ngũ tư vấn sẽ liên hệ hỗ trợ ngay trong vòng <span class="text-clinic-teal font-extrabold">15 phút</span>.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-teal-50 border border-teal-200 text-teal-800 rounded-2xl text-sm font-bold text-center">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('consultation.store') }}" method="POST" class="space-y-4" @submit="if(name && isValidPhone()) { submitting = true; } else { $event.preventDefault(); }">
        @csrf
        
        <!-- Hidden page source info -->
        <input type="hidden" name="source_url" value="{{ request()->fullUrl() }}">
        <input type="hidden" name="form_type" value="sidebar_consultation">

        <!-- Name -->
        <div class="space-y-1.5">
            <label for="form-name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Họ và tên *</label>
            <input type="text" id="form-name" name="name" required x-model="name" placeholder="Nguyễn Văn A" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
            @error('name')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="space-y-1.5">
            <label for="form-phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Số điện thoại *</label>
            <input type="tel" id="form-phone" name="phone" required x-model="phone" placeholder="{{ preg_replace('/\D/', '', \App\Models\Setting::site('hotline')) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
            <p x-show="phone.length > 0 && !isValidPhone()" class="text-xs font-semibold text-red-500 mt-1" x-cloak>
                Số điện thoại hợp lệ gồm 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).
            </p>
            @error('phone')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Specialty Dropdown -->
        <div class="space-y-1.5">
            <label for="form-department" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Chuyên khoa cần tư vấn</label>
            <select id="form-department" name="department" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
                <option value="">Chọn chuyên khoa</option>
                <option value="Nam Khoa" {{ old('department') == 'Nam Khoa' ? 'selected' : '' }}>Nam Khoa</option>
                <option value="Phụ Khoa" {{ old('department') == 'Phụ Khoa' ? 'selected' : '' }}>Phụ Khoa</option>
                <option value="Bệnh Xã Hội" {{ old('department') == 'Bệnh Xã Hội' ? 'selected' : '' }}>Bệnh Xã Hội</option>
                <option value="Hậu Môn - Trực Tràng" {{ old('department') == 'Hậu Môn - Trực Tràng' ? 'selected' : '' }}>Hậu Môn - Trực Tràng</option>
                <option value="Ngoại Khoa" {{ old('department') == 'Ngoại Khoa' ? 'selected' : '' }}>Ngoại Khoa</option>
            </select>
            @error('department')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Symptoms -->
        <div class="space-y-1.5">
            <label for="form-symptoms" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Triệu chứng của bạn</label>
            <textarea id="form-symptoms" name="symptoms" rows="3" placeholder="Mô tả sơ lược tình trạng sức khỏe hoặc câu hỏi cần giải đáp..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all resize-none">{{ old('symptoms') }}</textarea>
            @error('symptoms')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Privacy Policy Checkbox -->
        <div class="flex items-start gap-2.5 pt-1">
            <input type="checkbox" id="form-privacy-agree" required checked class="mt-1 w-4 h-4 text-clinic-blue border-slate-300 rounded focus:ring-clinic-blue">
            <label for="form-privacy-agree" class="text-xs text-slate-500 leading-normal select-none font-semibold">
                Tôi đồng ý với chính sách bảo mật thông tin và quy trình tư vấn riêng tư của phòng khám.
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                :disabled="submitting || !name || !isValidPhone()"
                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-clinic-blue disabled:bg-slate-350 disabled:cursor-not-allowed hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-md shadow-clinic-blue/20 hover:shadow-lg transition-all duration-200 text-sm">
            <span x-show="!submitting">Gửi thông tin tư vấn</span>
            <span x-show="submitting" x-cloak class="flex items-center gap-2">
                <!-- Simple SVG Spinner -->
                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Đang gửi thông tin...
            </span>
        </button>
    </form>

    <p class="text-[10px] text-slate-400 text-center leading-relaxed font-semibold">
        * Mọi thông tin trao đổi được cam kết bảo mật nghiêm ngặt theo quy trình riêng tư nội bộ của phòng khám.
    </p>
</div>
