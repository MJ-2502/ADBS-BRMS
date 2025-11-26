<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdRecordFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'original_name',
        'storage_path',
        'disk',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $casts = [
        'version' => 'integer',
        'file_size' => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
