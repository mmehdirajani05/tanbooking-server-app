<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'company_name',
        'display_name',
        'business_type',
        'registration_number',
        'tin_number',
        'license_number',
        'incorporation_date',
        'country',
        'region',
        'district',
        'ward',
        'address',
        'contact_phone',
        'contact_email',
        'website',
        'social_links',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'approved_at' => 'datetime',
            'incorporation_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CompanyModule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot('role', 'status')
            ->withTimestamps();
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function tourismPackages(): HasMany
    {
        return $this->hasMany(TourismPackage::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function hasModule(string $moduleType): bool
    {
        return $this->modules()
            ->where('module_type', $moduleType)
            ->where('status', 'approved')
            ->exists();
    }

    public function getModulesAttribute(): array
    {
        return $this->modules()->pluck('module_type')->toArray();
    }
}
