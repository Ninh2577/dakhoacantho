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

    /**
     * Get site contact settings (Single Source of Truth) with default fallback.
     */
    public static function site(?string $key = null): mixed
    {
        $defaults = [
            'clinic_name' => 'Phòng Khám Đa Khoa Gia Phước',
            'clinic_short_name' => 'Đa Khoa Gia Phước',
            'address' => 'Số 57 Hùng Vương, P. Ninh Kiều, TP. Cần Thơ',
            'hotline' => '0966.332.352',
            'email' => 'info@dakhoagiaphuoc.vn',
            'google_maps_url' => 'https://maps.app.goo.gl/DtvjNfmhPru9z1HD9',
            'google_maps_embed_url' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31429.579020087935!2d105.7704082!3d10.0418118!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a088032bc0311b%3A0x4da06f04ef4663c2!2zxJBhIEtob2EgR2lhIFBoxrDhu5tj!5e0!3m2!1sen!2s!4v1782102895910!5m2!1sen!2s',
            'latitude' => '10.043858',
            'longitude' => '105.778917',
            'facebook_url' => 'https://www.facebook.com/pkdkgiaphuoc',
            'zalo_url' => 'https://zalo.me/0966332352',
            'youtube_url' => 'https://www.youtube.com/@dakhoagiaphuoc',
            'tiktok_url' => 'https://www.tiktok.com/@dakhoagiaphuoc',
            'booking_url' => 'https://app.dakhoacantho.com/lien-he',
            'working_hours' => '07:30 - 20:00 (Tất cả các ngày trong tuần, kể cả Lễ)',
        ];

        $settings = static::get('site_settings', []);
        $settings = is_array($settings) ? $settings : [];

        $merged = array_merge($defaults, $settings);

        if ($key !== null) {
            return $merged[$key] ?? ($defaults[$key] ?? null);
        }

        return $merged;
    }
}

