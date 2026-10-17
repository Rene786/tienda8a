<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'file_path',
        'file_type',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function pdfDownloads(): HasMany
    {
        return $this->hasMany(PdfDownload::class);
    }

    public function isPdf(): bool
    {
        $path = parse_url($this->file_path, PHP_URL_PATH) ?: '';

        return strtolower((string) $this->file_type) === 'pdf'
            || strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf';
    }
}
