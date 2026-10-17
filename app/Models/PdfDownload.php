<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfDownload extends Model
{
    protected $fillable = [
        'user_id',
        'product_file_id',
        'file_name',
        'file_path',
        'downloaded_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function productFile(): BelongsTo
    {
        return $this->belongsTo(ProductFile::class)->withTrashed();
    }
}
