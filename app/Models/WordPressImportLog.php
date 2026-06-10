<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordPressImportLog extends Model
{
    protected $table = 'wordpress_import_logs';

    protected $fillable = [
        'batch_id',
        'source_post_id',
        'source_post_type',
        'source_slug',
        'source_title',
        'target_type',
        'target_id',
        'action',
        'status',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(WordPressImportBatch::class, 'batch_id');
    }
}
