<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quantity_total',
        'quantity_available',
        'description',
        'perks',
        'sale_start_date',
        'sale_end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'perks' => 'array',
            'sale_start_date' => 'datetime',
            'sale_end_date' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}