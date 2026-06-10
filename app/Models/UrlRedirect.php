<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlRedirect extends Model
{
    protected $table = 'url_redirects';

    protected $fillable = [
        'old_path',
        'new_path',
        'target_type',
        'target_id',
        'status_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
