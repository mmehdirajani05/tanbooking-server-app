<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourItinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'day_number',
        'time_slot',
        'activity_title',
        'description',
        'location',
        'duration_hours',
        'included_meals',
        'transport_details',
    ];

    protected function casts(): array
    {
        return [
            'included_meals' => 'array',
            'transport_details' => 'array',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourismPackage::class, 'package_id');
    }
}