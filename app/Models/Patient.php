<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email', 'gender', 'birth_date', 'age', 'address',
        'category_id', 'source', 'status', 'notes', 'internal_note',
        'last_contacted_at', 'created_by', 'consultation_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_contacted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public static function statusOptions(): array
    {
        return [
            'new' => 'Mới',
            'contacted' => 'Đã liên hệ',
            'booked' => 'Đã đặt lịch',
            'visited' => 'Đã đến khám',
            'cancelled' => 'Đã hủy',
            'archived' => 'Lưu trữ',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'new' => 'info',
            'contacted' => 'warning',
            'booked' => 'primary',
            'visited' => 'success',
            'cancelled' => 'danger',
            'archived' => 'gray',
            default => 'gray',
        };
    }

    public static function genderOptions(): array
    {
        return [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];
    }

    public static function sourceOptions(): array
    {
        return [
            'Tư vấn online' => 'Tư vấn online',
            'Giới thiệu' => 'Giới thiệu',
            'Tự đến' => 'Tự đến',
            'Mạng xã hội' => 'Mạng xã hội',
            'Google Search' => 'Google Search',
            'Khác' => 'Khác',
        ];
    }
}
