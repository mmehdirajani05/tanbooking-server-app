<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourismPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'title',
        'category',
        'thumbnail',
        'gallery',
        'tour_type',
        'country',
        'region',
        'locations',
        'short_description',
        'full_description',
        'highlights',
        'unique_selling_points',
        'duration_days',
        'price_adult',
        'price_child',
        'price_infant',
        'group_price',
        'discount_percentage',
        'inclusions',
        'exclusions',
        'cancellation_policy',
        'refund_rules',
        'child_policy',
        'safety_guidelines',
        'requirements',
        'search_keywords',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => 'array',
            'gallery' => 'array',
            'locations' => 'array',
            'highlights' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'safety_guidelines' => 'array',
            'requirements' => 'array',
            'search_keywords' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class, 'package_id');
    }
}