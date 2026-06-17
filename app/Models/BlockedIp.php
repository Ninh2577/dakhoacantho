<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
        'is_permanent',
        'created_by',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'is_permanent' => 'boolean',
    ];

    /**
     * Check if this block is still active (not expired).
     */
    public function isActive(): bool
    {
        if ($this->is_permanent) {
            return true;
        }

        if ($this->blocked_until === null) {
            return true;
        }

        return $this->blocked_until->isFuture();
    }

    /**
     * Check if the given IP is currently blocked (uses cache for performance).
     */
    public static function isIpBlocked(string $ip): bool
    {
        // Cache for 5 minutes to avoid hammering DB on every request
        return Cache::remember("security:blocked_ip:{$ip}", 300, function () use ($ip) {
            $block = static::where('ip_address', $ip)->first();
            if (! $block) {
                return false;
            }

            return $block->isActive();
        });
    }

    /**
     * Unblock an IP and clear its cache.
     */
    public static function unblockIp(string $ip): void
    {
        static::where('ip_address', $ip)->delete();
        Cache::forget("security:blocked_ip:{$ip}");
    }

    /**
     * Block an IP and clear its cache.
     */
    public static function blockIp(
        string $ip,
        string $reason = '',
        ?int $minutes = null,
        bool $permanent = false,
        ?int $createdBy = null
    ): self {
        $blockedUntil = ($permanent || $minutes === null) ? null : now()->addMinutes($minutes);

        $block = static::updateOrCreate(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_until' => $blockedUntil,
                'is_permanent' => $permanent,
                'created_by' => $createdBy,
            ]
        );

        // Invalidate cache so next request picks up the new block
        Cache::forget("security:blocked_ip:{$ip}");

        return $block;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_permanent', true)
                ->orWhereNull('blocked_until')
                ->orWhere('blocked_until', '>', now());
        });
    }
}
