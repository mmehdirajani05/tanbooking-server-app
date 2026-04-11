<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'module_type',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_at',
        'verified_at',
        'verified_by',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
