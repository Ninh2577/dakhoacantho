<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function store(Request $request)
    {
        // Validate consultation form inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'department' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
        ]);

        // Save consultation details to database
        Consultation::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'department' => $validated['department'] ?? null,
            'symptoms' => $validated['symptoms'] ?? null,
            'status' => 'pending',
        ]);

        // Redirect back with a flash success message
        return back()->with('success', 'Đăng ký tư vấn thành công! Bác sĩ chuyên khoa sẽ liên hệ trực tiếp với bạn trong vòng 15 phút.');
    }
}
