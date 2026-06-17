<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('dakhoacantho:settings:all');
        });
        static::deleted(function () {
            Cache::forget('dakhoacantho:settings:all');
        });
    }

    /**
     * Get a setting value by key, with JSON decode and safe fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $settings = Cache::remember('dakhoacantho:settings:all', now()->addHours(24), function () {
                return static::all()->keyBy('key');
            });

            $record = $settings->get($key);
            if (! $record || $record->value === null) {
                return $default;
            }

            $decoded = json_decode($record->value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }

            return $record->value;
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Store a setting value (auto-JSON-encodes arrays/objects).
     */
    public static function set(string $key, mixed $value): void
    {
        try {
            $encoded = is_string($value)
                ? $value
                : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            static::updateOrCreate(['key' => $key], ['value' => $encoded]);
            Cache::forget('dakhoacantho:settings:all');
        } catch (\Throwable) {
            // Silently fail — never crash admin on settings write
        }
    }
}
