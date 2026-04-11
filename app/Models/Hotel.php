<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'company_id',
        'name',
        'description',
        'city',
        'area',
        'address',
        'phone',
        'email',
        'amenities',
        'images',
        'check_in_time',
        'check_out_time',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'retail_price',
        'contract_price',
        'search_tags',
        'discount_percentage',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'amenities'     => 'array',
            'images'        => 'array',
            'search_tags'   => 'array',
            'check_in_time' => 'datetime:H:i:s',
            'check_out_time'=> 'datetime:H:i:s',
            'approved_at'   => 'datetime',
            'retail_price'  => 'decimal:2',
            'contract_price'=> 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
