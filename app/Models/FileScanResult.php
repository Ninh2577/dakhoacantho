<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FileScanResult extends Model
{
    protected $fillable = [
        'scan_id',
        'path',
        'type',
        'severity',
        'message',
        'hash',
        'meta',
        'check_key',
        'check_group',
        'status',
        'target',
        'recommendation',
        'reviewed_at',
        'ignored_at',
        'ignored_reason',
    ];

    protected $casts = [
        'meta' => 'array',
        'reviewed_at' => 'datetime',
        'ignored_at' => 'datetime',
    ];

    // Type constants
    const TYPE_SUSPICIOUS = 'suspicious';

    const TYPE_MODIFIED = 'modified';

    const TYPE_NEW = 'new';

    const TYPE_DELETED = 'deleted';

    const TYPE_OK = 'ok';

    const TYPE_REVIEWED = 'reviewed';

    const TYPE_IGNORED = 'ignored';

    // Severity constants (mirrors SecurityEvent)
    const SEVERITY_INFO = 'info';

    const SEVERITY_LOW = 'low';

    const SEVERITY_MEDIUM = 'medium';

    const SEVERITY_HIGH = 'high';

    const SEVERITY_CRITICAL = 'critical';

    public function scopeByScan(Builder $query, string $scanId): Builder
    {
        return $query->where('scan_id', $scanId);
    }

    public function scopeSuspicious(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_SUSPICIOUS, self::TYPE_MODIFIED, self::TYPE_NEW])
            ->whereNull('ignored_at');
    }

    public function scopeCriticalOrHigh(Builder $query): Builder
    {
        return $query->whereIn('severity', [self::SEVERITY_CRITICAL, self::SEVERITY_HIGH]);
    }

    /**
     * Mark result as reviewed.
     */
    public function markReviewed(): void
    {
        $this->update([
            'type' => self::TYPE_REVIEWED,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Mark result as ignored.
     */
    public function markIgnored(?string $reason = null): void
    {
        $this->update([
            'type' => self::TYPE_IGNORED,
            'ignored_at' => now(),
            'ignored_reason' => $reason,
        ]);
    }
}
