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
}
