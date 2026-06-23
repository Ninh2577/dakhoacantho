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
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->file_path);
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
