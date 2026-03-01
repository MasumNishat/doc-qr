<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'verification_code',
        'original_filename',
        'crts_no',
        'file_type',
        'page_count',
    ];

    protected function casts(): array
    {
        return [
            'page_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStoragePathAttribute(): string
    {
        return storage_path("app/documents/{$this->verification_code}");
    }
}
