<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WordPressImportBatch extends Model
{
    protected $table = 'wordpress_import_batches';

    protected $fillable = [
        'file_path',
        'original_file_name',
        'old_domain',
        'media_mode',
        'local_media_base_path',
        'import_post_types',
        'import_statuses',
        'duplicate_mode',
        'dry_run',
        'limit',
        'status',
        'total_items',
        'processed_items',
        'imported_items',
        'updated_items',
        'skipped_items',
        'failed_items',
        'missing_media_items',
        'started_at',
        'finished_at',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'import_post_types' => 'array',
        'import_statuses' => 'array',
        'dry_run' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(WordPressImportLog::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
