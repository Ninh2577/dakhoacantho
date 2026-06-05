<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with JSON decode and safe fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $record = static::where('key', $key)->first();
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
        } catch (\Throwable) {
            // Silently fail — never crash admin on settings write
        }
    }
}
