<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SecurityEvent extends Model
{
    protected $fillable = [
        'type',
        'severity',
        'ip_address',
        'user_id',
        'user_agent',
        'url',
        'method',
        'message',
        'context',
    ];

    protected $casts = [
        'context'    => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Severity constants
    const SEVERITY_INFO     = 'info';
    const SEVERITY_LOW      = 'low';
    const SEVERITY_MEDIUM   = 'medium';
    const SEVERITY_HIGH     = 'high';
    const SEVERITY_CRITICAL = 'critical';

    // Type constants
    const TYPE_FAILED_LOGIN      = 'failed_login';
    const TYPE_SUCCESSFUL_LOGIN  = 'successful_login';
    const TYPE_BRUTE_FORCE       = 'brute_force';
    const TYPE_IP_BLOCKED        = 'ip_blocked';
    const TYPE_FIREWALL_BLOCK    = 'firewall_block';
    const TYPE_SUSPICIOUS_REQUEST = 'suspicious_request';
    const TYPE_MALICIOUS_PATH    = 'malicious_path';
    const TYPE_SCAN_CRITICAL     = 'scan_critical';
    const TYPE_FILE_INTEGRITY    = 'file_integrity';

    // Scopes
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeBySeverity(Builder $query, string $severity): Builder
    {
        return $query->where('severity', $severity);
    }

    public function scopeCriticalOrHigh(Builder $query): Builder
    {
        return $query->whereIn('severity', [self::SEVERITY_CRITICAL, self::SEVERITY_HIGH]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
