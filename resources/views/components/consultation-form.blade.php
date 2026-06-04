<div class="bg-white rounded-3xl border border-slate-100 shadow-lg p-6 md:p-8 space-y-6">
    <div class="text-center md:text-left space-y-2">
        <h3 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Hỏi đáp triệu chứng bệnh</h3>
        <p class="text-xs md:text-sm text-slate-500 leading-normal">
            Vui lòng điền thông tin, bác sĩ sẽ phản hồi ngay trong vòng <span class="text-clinic-teal font-extrabold">15 phút</span>.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-teal-50 border border-teal-200 text-teal-800 rounded-2xl text-sm font-semibold text-center">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('consultation.store') }}" method="POST" class="space-y-4">
        @csrf
        <!-- Name -->
        <div class="space-y-1.5">
            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Họ và tên *</label>
            <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Nguyễn Văn A" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
            @error('name')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="space-y-1.5">
            <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Số điện thoại *</label>
            <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" placeholder="090 123 4587" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
            @error('phone')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Specialty Dropdown -->
        <div class="space-y-1.5">
            <label for="department" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Chuyên khoa cần tư vấn</label>
            <select id="department" name="department" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all">
                <option value="">Chọn chuyên khoa</option>
                <option value="Nam Khoa" {{ old('department') == 'Nam Khoa' ? 'selected' : '' }}>Nam Khoa</option>
                <option value="Phụ Khoa" {{ old('department') == 'Phụ Khoa' ? 'selected' : '' }}>Phụ Khoa</option>
                <option value="Bệnh Xã Hội" {{ old('department') == 'Bệnh Xã Hội' ? 'selected' : '' }}>Bệnh Xã Hội</option>
                <option value="Hậu Môn - Trực Tràng" {{ old('department') == 'Hậu Môn - Trực Tràng' ? 'selected' : '' }}>Hậu Môn - Trực Tràng</option>
            </select>
            @error('department')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Symptoms -->
        <div class="space-y-1.5">
            <label for="symptoms" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Triệu chứng của bạn</label>
            <textarea id="symptoms" name="symptoms" rows="3" placeholder="Mô tả sơ lược tình trạng sức khỏe của bạn..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-clinic-blue focus:bg-white rounded-xl text-sm font-semibold text-slate-800 outline-none transition-all resize-none">{{ old('symptoms') }}</textarea>
            @error('symptoms')
                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-clinic-blue hover:bg-opacity-95 text-white font-extrabold rounded-xl shadow-md shadow-clinic-blue/20 hover:shadow-lg transition-all duration-200 text-sm">
            <span>Gửi thông tin tư vấn</span>
            <svg class="w-4 h-4 transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </button>
    </form>

    <p class="text-[10px] text-slate-400 text-center leading-relaxed">
        * Thông tin của bạn được cam kết bảo mật tuyệt đối theo chính sách bảo mật của chúng tôi.
    </p>
</div>
