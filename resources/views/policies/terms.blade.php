@extends('layouts.app')

@section('title', 'Điều Khoản Sử Dụng | Phòng Khám Đa Khoa Gia Phước')

@section('meta')
    <x-seo
        page-type="webpage"
        title="Điều Khoản Sử Dụng | Phòng Khám Đa Khoa Gia Phước"
        description="Điều khoản sử dụng dịch vụ và thông tin trên website của Phòng Khám Đa Khoa Gia Phước Cần Thơ."
        :canonical="route('terms.policy')"
        :breadcrumbs="[
            ['name' => 'Trang chủ', 'url' => route('home')],
            ['name' => 'Điều Khoản Sử Dụng', 'url' => route('terms.policy')]
        ]"
    />
@endsection

@section('content')
<section class="bg-slate-50 py-10 md:py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-sm sm:p-8 md:p-10">
            <span class="inline-flex rounded-full bg-clinic-sky/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.24em] text-clinic-blue">
                Điều khoản
            </span>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900">Điều khoản sử dụng website</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                Khi truy cập website Phòng Khám Đa Khoa Gia Phước, người dùng đồng ý sử dụng nội dung theo đúng mục đích tham khảo và liên hệ tư vấn phù hợp.
            </p>

            <div class="mt-8 space-y-6 text-sm leading-7 text-slate-700">
                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">1. Mục đích sử dụng</h2>
                    <p class="mt-2">
                        Website cung cấp thông tin giới thiệu, liên hệ, chuyên khoa và hướng dẫn hỗ trợ tư vấn cho người dùng quan tâm.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">2. Trách nhiệm người dùng</h2>
                    <p class="mt-2">
                        Người dùng cần cung cấp thông tin liên hệ chính xác khi gửi biểu mẫu và không đăng tải nội dung sai lệch, gây nhiễu hoặc vi phạm pháp luật.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">3. Giới hạn thông tin</h2>
                    <p class="mt-2">
                        Các nội dung trên website không thay thế cho chẩn đoán trực tiếp. Trường hợp cần hỗ trợ cụ thể, vui lòng liên hệ để được hướng dẫn phù hợp.
                    </p>
                </section>

                <section>
                    <h2 class="text-lg font-extrabold text-slate-900">4. Điều chỉnh nội dung</h2>
                    <p class="mt-2">
                        Website có thể cập nhật nội dung, cấu trúc và thông tin liên hệ nhằm phục vụ tốt hơn cho trải nghiệm người dùng và nhu cầu vận hành thực tế.
                    </p>
                </section>
            </div>
        </div>
    </div>
</section>
@endsection
