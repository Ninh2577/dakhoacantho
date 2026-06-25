@extends('layouts.app')

@section('title', 'Chính Sách Bảo Mật | Phòng Khám Đa Khoa Cần Thơ')

@section('meta')
    <x-seo
        page-type="webpage"
        title="Chính Sách Bảo Mật | Phòng Khám Đa Khoa Cần Thơ"
        description="Chính sách bảo mật thông tin cá nhân và bệnh án của bệnh nhân tại Phòng Khám Đa Khoa Cần Thơ."
        :canonical="route('privacy.policy')"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Chính Sách Bảo Mật', 'url' => route('privacy.policy')]
        ]"
    />
@endsection

@section('content')
<section class="bg-slate-50 py-10 md:py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8 md:p-10">
            <span class="inline-flex rounded-full bg-clinic-sky/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em] text-clinic-blue">
                Chính sách
            </span>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900">Chính sách bảo mật thông tin</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                Phòng Khám Đa Khoa Cần Thơ cam kết bảo mật thông tin người dùng khi gửi yêu cầu tư vấn, đặt lịch và liên hệ qua website.
            </p>

            <div class="mt-8 space-y-6 text-sm leading-7 text-slate-700">
                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">1. Phạm vi thông tin tiếp nhận</h2>
                    <p class="mt-2">
                        Website có thể tiếp nhận các thông tin cần thiết như họ tên, số điện thoại, nhu cầu tư vấn và nội dung người dùng chủ động cung cấp.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">2. Mục đích sử dụng</h2>
                    <p class="mt-2">
                        Thông tin được sử dụng để hỗ trợ tư vấn, xác nhận nhu cầu liên hệ, hướng dẫn đặt lịch và cải thiện trải nghiệm sử dụng website.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">3. Cam kết bảo mật</h2>
                    <p class="mt-2">
                        Chúng tôi ưu tiên quy trình rõ ràng, giới hạn truy cập nội bộ phù hợp và không chia sẻ thông tin cá nhân trái mục đích tiếp nhận ban đầu.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">4. Lưu ý nội dung</h2>
                    <p class="mt-2">
                        Thông tin trên website chỉ mang tính tham khảo, không thay thế cho việc thăm khám và đánh giá trực tiếp khi cần thiết.
                    </p>
                </section>
            </div>
        </div>
    </div>
</section>
@endsection
