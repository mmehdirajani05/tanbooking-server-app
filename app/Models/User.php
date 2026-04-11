<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'global_role',
        'registration_source',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'phone_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'is_active'          => 'boolean',
            'password'           => 'hashed',
        ];
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function accesses()
    {
        return $this->hasMany(UserAccess::class);
    }

    public function ownedHotels()
    {
        return $this->hasMany(Hotel::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    public function assignedConversations()
    {
        return $this->hasMany(Conversation::class, 'assigned_to');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function approvedHotels()
    {
        return $this->hasMany(Hotel::class, 'approved_by');
    }

    /**
     * Companies this user owns (as the primary owner)
     */
    public function ownedCompanies()
    {
        return $this->hasMany(Company::class, 'owner_id');
    }

    /**
     * Companies this user belongs to (as staff/admin)
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    /**
     * Get user's companies with approved status
     * Uses direct query to avoid pivot table ambiguity
     */
    public function approvedCompanies()
    {
        return Company::whereHas('users', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->where('status', 'approved');
    }

    /**
     * Check if user has an approved company with specific module
     */
    public function hasApprovedCompanyWithModule(string $moduleType): bool
    {
        return Company::whereHas('users', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->where('status', 'approved')
            ->whereHas('modules', function ($query) use ($moduleType) {
                $query->where('module_type', $moduleType)
                      ->where('status', 'approved');
            })
            ->exists();
    }

    /**
     * Get user's primary approved company (if any)
     */
    public function primaryCompany()
    {
        return Company::whereHas('users', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->where('status', 'approved')
            ->first();
    }
}
