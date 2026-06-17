<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SecuritySetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Default values for all known settings.
     * Used as fallback when setting is not in DB.
     */
    public static array $defaults = [
        // Firewall
        'firewall_mode' => 'log_only',   // log_only | block
        'allowlisted_ips' => [],

        // Login protection
        'max_failed_logins_per_ip' => 10,
        'max_failed_logins_per_email' => 5,
        'lockout_minutes' => 15,
        'auto_block_enabled' => false,        // must be opt-in
        'permanent_block_enabled' => false,

        // Traffic logging
        'traffic_logging' => 'suspicious_only', // off | suspicious_only | all

        // File scanner
        'scan_uploads' => true,
        'scan_public' => true,
        'max_scan_file_size_mb' => 2,

        // Alerts
        'email_alerts_enabled' => false,
        'admin_security_email' => '',

        // Retention (days)
        'retention_days_info' => 30,
        'retention_days_low' => 30,
        'retention_days_medium' => 60,
        'retention_days_high' => 180,
        'retention_days_critical' => 365,
        'retention_days_login' => 60,
    ];

    private static string $cacheKey = 'security:settings:all';

    /**
     * Get a single setting value, with fallback to default.
     */
    public static function getSetting(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }
        if ($default !== null) {
            return $default;
        }

        return static::$defaults[$key] ?? null;
    }

    /**
     * Set a single setting value and flush cache.
     */
    public static function setSetting(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(static::$cacheKey);
    }

    /**
     * Set multiple settings at once and flush cache once.
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(static::$cacheKey);
    }

    /**
     * Return all settings merged with defaults (cached 10 min).
     */
    public static function allCached(): array
    {
        return Cache::remember(static::$cacheKey, 600, function () {
            $dbSettings = static::all()->pluck('value', 'key')->toArray();

            // Unwrap single-value arrays that json-cast wraps scalars in
            $flat = [];
            foreach ($dbSettings as $k => $v) {
                $flat[$k] = is_array($v) && count($v) === 1 && array_key_exists(0, $v) ? $v[0] : $v;
            }

            return array_merge(static::$defaults, $flat);
        });
    }

    /**
     * Flush settings cache (call after bulk update).
     */
    public static function flushCache(): void
    {
        Cache::forget(static::$cacheKey);
    }
}
