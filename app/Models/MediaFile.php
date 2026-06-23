<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Get the full URL to the media file.
     */
    public function getUrlAttribute(): string
    {
        $storageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path);

        // Swap the host/scheme/port dynamically based on the current request
        if (request() && !app()->runningInConsole()) {
            $parsedUrl = parse_url($storageUrl);
            if (isset($parsedUrl['host'])) {
                $scheme = request()->getScheme();
                $host = request()->getHost();
                $port = request()->getPort();
                $portStr = ($port && !in_array($port, [80, 443])) ? ':' . $port : '';

                $path = $parsedUrl['path'] ?? '';
                $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
                $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

                return "{$scheme}://{$host}{$portStr}{$path}{$query}{$fragment}";
            }
        }

        return $storageUrl;
    }

    /**
     * Get the file size in human-readable format.
     */
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
