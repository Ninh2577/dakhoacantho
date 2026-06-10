<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlSettingHistory extends Model
{
    protected $table = 'url_setting_histories';

    protected $fillable = [
        'old_article_pattern',
        'new_article_pattern',
        'old_category_pattern',
        'new_category_pattern',
        'conflict_count',
        'redirect_count',
        'status',
        'error_message',
        'started_at',
        'finished_at',
        'total_items',
        'processed_items',
        'updated_items',
        'failed_items',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(UrlRecompileItem::class, 'history_id');
    }
}
