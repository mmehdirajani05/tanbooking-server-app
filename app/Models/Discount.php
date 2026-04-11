<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'module_type',
        'module_id',
        'type',
        'value',
        'code',
        'start_date',
        'end_date',
        'min_booking_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_booking_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->start_date, $this->end_date)
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}