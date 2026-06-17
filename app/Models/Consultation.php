<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'department',
        'symptoms',
        'status',
        'notes',
        'assigned_to',
        'patient_id',
        'converted_to_patient_at',
    ];

    protected $casts = [
        'converted_to_patient_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Chờ xử lý',
            'contacted' => 'Đã liên hệ',
            'booked' => 'Đã đặt lịch',
            'visited' => 'Đã đến khám',
            'cancelled' => 'Đã hủy',
            'spam' => 'Spam',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'contacted' => 'info',
            'booked' => 'primary',
            'visited' => 'success',
            'cancelled' => 'danger',
            'spam' => 'gray',
            default => 'gray',
        };
    }
}
