<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'failure_reason',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('successful', false);
    }

    public function scopeByIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip_address', $ip);
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    /**
     * Count recent failed attempts for a given IP within $minutes.
     */
    public static function countRecentFailedByIp(string $ip, int $minutes = 15): int
    {
        return static::where('ip_address', $ip)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Count recent failed attempts for a given email within $minutes.
     */
    public static function countRecentFailedByEmail(string $email, int $minutes = 15): int
    {
        return static::where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
