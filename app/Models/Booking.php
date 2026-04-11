<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'company_id',
        'module_type',
        'bookable_id',
        'bookable_type',
        'hotel_id',
        'room_type_id',
        'booking_reference',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in_date',
        'check_out_date',
        'number_of_rooms',
        'number_of_guests',
        'total_price',
        'paid_amount',
        'refund_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'status',
        'notes',
        'confirmed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date'  => 'date',
            'check_out_date' => 'date',
            'number_of_rooms'=> 'integer',
            'number_of_guests' => 'integer',
            'total_price'    => 'decimal:2',
            'paid_amount'    => 'decimal:2',
            'refund_amount'  => 'decimal:2',
            'confirmed_at'   => 'datetime',
            'cancelled_at'   => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Polymorphic relationship to the bookable item (Hotel, TourismPackage, Event, etc.)
     */
    public function bookable()
    {
        return $this->morphTo();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public static function generateBookingReference(): string
    {
        do {
            $reference = 'TB' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
        } while (self::where('booking_reference', $reference)->exists());

        return $reference;
    }
}
