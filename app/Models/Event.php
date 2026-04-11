<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'category',
        'subcategory',
        'status',
        'event_date',
        'start_datetime',
        'end_datetime',
        'venue_name',
        'venue_type',
        'city',
        'region',
        'address',
        'banner_image',
        'gallery',
        'video_url',
        'terms_conditions',
        'itinerary_document',
        'search_keywords',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'search_keywords' => 'array',
            'event_date' => 'date',
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class);
    }
}