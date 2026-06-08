<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(Request $request)
    {
        // Validate consultation form inputs including Vietnamese phone format
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^(03|05|07|08|09)\d{8}$/'],
            'department' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam (phải có 10 số và bắt đầu bằng 03, 05, 07, 08 hoặc 09).',
        ]);

        // Save consultation details to database
        Consultation::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'department' => $validated['department'] ?? null,
            'symptoms' => $validated['symptoms'] ?? null,
            'status' => 'pending',
        ]);

        // Redirect back with a flash success message (doctor references replaced)
        return back()->with('success', 'Đăng ký tư vấn thành công! Đội ngũ tư vấn sẽ liên hệ trực tiếp hỗ trợ bạn trong vòng 15 phút.');
    }
}
