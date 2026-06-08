<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleComment extends Model
{
    protected $fillable = [
        'article_id',
        'name',
        'phone',
        'content',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public static function statusOptions(): array
    {
        return [
            'pending'  => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'spam'     => 'Spam',
        ];
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'spam'     => 'gray',
            default    => 'gray',
        };
    }
}
