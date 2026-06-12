<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // Type constants
    const TYPE_SUSPICIOUS = 'suspicious';
    const TYPE_MODIFIED   = 'modified';
    const TYPE_NEW        = 'new';
    const TYPE_DELETED    = 'deleted';
    const TYPE_OK         = 'ok';
    const TYPE_REVIEWED   = 'reviewed';

    // Severity constants (mirrors SecurityEvent)
    const SEVERITY_INFO     = 'info';
    const SEVERITY_LOW      = 'low';
    const SEVERITY_MEDIUM   = 'medium';
    const SEVERITY_HIGH     = 'high';
    const SEVERITY_CRITICAL = 'critical';

    public function scopeByScan(Builder $query, string $scanId): Builder
    {
        return $query->where('scan_id', $scanId);
    }

    public function scopeSuspicious(Builder $query): Builder
    {
        return $query->whereIn('type', [self::TYPE_SUSPICIOUS, self::TYPE_MODIFIED, self::TYPE_NEW]);
    }

    public function scopeCriticalOrHigh(Builder $query): Builder
    {
        return $query->whereIn('severity', [self::SEVERITY_CRITICAL, self::SEVERITY_HIGH]);
    }

    /**
     * Mark result as reviewed (non-destructive, just changes type label).
     */
    public function markReviewed(): void
    {
        $this->update(['type' => self::TYPE_REVIEWED]);
    }
}
