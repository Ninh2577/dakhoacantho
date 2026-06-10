<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlRecompileItem extends Model
{
    protected $table = 'url_recompile_items';

    protected $fillable = [
        'history_id',
        'target_type',
        'target_id',
        'old_path',
        'new_path',
        'status',
        'error_message',
    ];

    public function history()
    {
        return $this->belongsTo(UrlSettingHistory::class, 'history_id');
    }
}
