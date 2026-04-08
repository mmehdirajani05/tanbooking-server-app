<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Search available hotels and room types based on criteria.
     */
    public function searchHotels(array $filters): array
    {
        $checkInDate = Carbon::parse($filters['check_in_date']);
        $checkOutDate = Carbon::parse($filters['check_out_date']);
        $numberOfGuests = $filters['number_of_guests'] ?? null;

        $query = Hotel::where('status', 'approved')
            ->with(['roomTypes' => function ($q) use ($checkInDate, $checkOutDate, $numberOfGuests) {
                $q->where('is_active', true);

                if ($numberOfGuests) {
                    $q->where('max_occupancy', '>=', $numberOfGuests);
                }

                // Filter room types that have availability
                $q->with(['inventories' => function ($iq) use ($checkInDate, $checkOutDate) {
                    $iq->whereBetween('date', [$checkInDate->toDateString(), $checkOutDate->copy()->subDay()->toDateString()])
                       ->orderBy('date');
                }]);
            }]);

        if (! empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (! empty($filters['area'])) {
            $query->where('area', 'like', '%' . $filters['area'] . '%');
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        $hotels = $query->get();

        // Process and filter results to only include available room types
        $results = [];
        $nights = $checkInDate->diffInDays($checkOutDate);

        foreach ($hotels as $hotel) {
            $availableRoomTypes = [];

            foreach ($hotel->roomTypes as $roomType) {
                $inventories = $roomType->inventories;

                // Skip if no inventory records
                if ($inventories->isEmpty()) {
                    continue;
                }

                // Check if all dates in range have available rooms
                $minAvailable = $inventories->min('available_rooms');

                if ($minAvailable > 0) {
                    $availableRoomTypes[] = [
                        'id'              => $roomType->id,
                        'name'            => $roomType->name,
                        'description'     => $roomType->description,
                        'max_occupancy'   => $roomType->max_occupancy,
                        'price_per_night' => $roomType->price_per_night,
                        'number_of_beds'  => $roomType->number_of_beds,
                        'amenities'       => $roomType->amenities,
                        'images'          => $roomType->images,
                        'available_rooms' => $minAvailable,
                        'total_price'     => number_format((float) $roomType->price_per_night * $nights, 2, '.', ''),
                    ];
                }
            }

            if (! empty($availableRoomTypes)) {
                $results[] = [
                    'id'            => $hotel->id,
                    'name'          => $hotel->name,
                    'city'          => $hotel->city,
                    'area'          => $hotel->area,
                    'address'       => $hotel->address,
                    'description'   => $hotel->description,
                    'amenities'     => $hotel->amenities,
                    'images'        => $hotel->images,
                    'check_in_time' => $hotel->check_in_time,
                    'check_out_time'=> $hotel->check_out_time,
                    'room_types'    => $availableRoomTypes,
                ];
            }
        }

        return $results;
    }

    /**
     * Create a booking with transaction and inventory lock.
     */
    public function createBooking(array $data): Booking
    {
        $checkInDate = Carbon::parse($data['check_in_date']);
        $checkOutDate = Carbon::parse($data['check_out_date']);
        $numberOfRooms = $data['number_of_rooms'];
        $roomTypeId = $data['room_type_id'];
        $hotelId = $data['hotel_id'];
        
        // Determine customer_id: either from authenticated user (customer) or provided/created
        $customerId = $data['customer_id'] ?? Auth::id();

        // Validate hotel is approved
        $hotel = Hotel::where('id', $hotelId)
            ->where('status', 'approved')
            ->first();

        if (! $hotel) {
            throw ValidationException::withMessages([
                'hotel_id' => ['Hotel is not available for booking.'],
            ]);
        }

        // Validate room type belongs to the hotel
        $roomType = RoomType::where('id', $roomTypeId)
            ->where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->first();

        if (! $roomType) {
            throw ValidationException::withMessages([
                'room_type_id' => ['Room type is not available.'],
            ]);
        }

        // Validate guest count doesn't exceed occupancy
        if ($data['number_of_guests'] > $roomType->max_occupancy) {
            throw ValidationException::withMessages([
                'number_of_guests' => ['Number of guests exceeds room capacity of ' . $roomType->max_occupancy . '.'],
            ]);
        }

        // Calculate total price
        $nights = $checkInDate->diffInDays($checkOutDate);
        $totalPrice = (float) $roomType->price_per_night * $nights * $numberOfRooms;

        // Use database transaction with row-level locking
        return DB::transaction(function () use (
            $data, $roomType, $roomTypeId, $checkInDate, $checkOutDate,
            $numberOfRooms, $totalPrice, $hotelId, $nights, $customerId
        ) {
            // Check availability with row-level locking
            $currentDate = $checkInDate->copy();
            $endDate = $checkOutDate->copy()->subDay();

            while ($currentDate <= $endDate) {
                $inventory = \App\Models\Inventory::where('room_type_id', $roomTypeId)
                    ->where('date', $currentDate->toDateString())
                    ->lockForUpdate()
                    ->first();

                if (! $inventory || $inventory->available_rooms < $numberOfRooms) {
                    throw ValidationException::withMessages([
                        'number_of_rooms' => ['Only ' . ($inventory ? $inventory->available_rooms : 0) . ' rooms available for ' . $currentDate->toDateString() . '.'],
                    ]);
                }

                $currentDate->addDay();
            }

            // Reduce inventory
            $currentDate = $checkInDate->copy();
            $endDate = $checkOutDate->copy()->subDay();

            while ($currentDate <= $endDate) {
                \App\Models\Inventory::where('room_type_id', $roomTypeId)
                    ->where('date', $currentDate->toDateString())
                    ->lockForUpdate()
                    ->decrement('available_rooms', $numberOfRooms);

                $currentDate->addDay();
            }

            // Create booking
            return Booking::create([
                'customer_id'     => $customerId,
                'hotel_id'        => $hotelId,
                'room_type_id'    => $roomTypeId,
                'booking_reference'=> Booking::generateBookingReference(),
                'guest_name'      => $data['guest_name'],
                'guest_email'     => $data['guest_email'],
                'guest_phone'     => $data['guest_phone'],
                'check_in_date'   => $checkInDate->toDateString(),
                'check_out_date'  => $checkOutDate->toDateString(),
                'number_of_rooms' => $numberOfRooms,
                'number_of_guests'=> $data['number_of_guests'],
                'total_price'     => $totalPrice,
                'status'          => 'pending',
                'notes'           => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Get customer's bookings.
     */
    public function getCustomerBookings(?string $status = null): LengthAwarePaginator
    {
        $query = Booking::where('customer_id', Auth::id())
            ->with(['hotel:id,name,city,area', 'roomType:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get customer booking detail.
     */
    public function getCustomerBooking(int $bookingId): Booking
    {
        return Booking::where('customer_id', Auth::id())
            ->where('id', $bookingId)
            ->with(['hotel', 'roomType'])
            ->firstOrFail();
    }

    /**
     * Cancel a booking (customer).
     */
    public function cancelBooking(int $bookingId): Booking
    {
        $booking = Booking::where('customer_id', Auth::id())
            ->where('id', $bookingId)
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            throw ValidationException::withMessages([
                'booking' => ['Booking is already cancelled.'],
            ]);
        }

        if ($booking->status === 'confirmed') {
            // Restore inventory
            app(\App\Services\Inventory\InventoryService::class)->restoreInventory(
                $booking->room_type_id,
                $booking->check_in_date,
                $booking->check_out_date,
                $booking->number_of_rooms
            );
        }

        $booking->update([
            'status'        => 'cancelled',
            'cancelled_at'  => now(),
        ]);

        $booking->refresh();

        return $booking->load(['hotel:id,name,city', 'roomType:id,name']);
    }

    // Hotel owner methods
    /**
     * Get bookings for a hotel owner's hotels.
     */
    public function getHotelBookings(int $hotelId, ?string $status = null): LengthAwarePaginator
    {
        // Verify ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $query = Booking::where('hotel_id', $hotelId)
            ->with(['customer:id,name,email,phone', 'roomType:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Get booking detail for hotel owner.
     */
    public function getHotelBooking(int $hotelId, int $bookingId): Booking
    {
        // Verify ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return Booking::where('hotel_id', $hotelId)
            ->where('id', $bookingId)
            ->with(['customer:id,name,email,phone', 'roomType'])
            ->firstOrFail();
    }

    /**
     * Update booking status (hotel owner).
     */
    public function updateBookingStatus(int $hotelId, int $bookingId, string $status): Booking
    {
        // Verify ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $booking = Booking::where('hotel_id', $hotelId)
            ->where('id', $bookingId)
            ->firstOrFail();

        if ($booking->status === $status) {
            throw ValidationException::withMessages([
                'status' => ['Booking is already ' . $status . '.'],
            ]);
        }

        if ($booking->status === 'cancelled') {
            throw ValidationException::withMessages([
                'status' => ['Cannot update a cancelled booking.'],
            ]);
        }

        if ($status === 'confirmed') {
            $booking->update([
                'status'       => 'confirmed',
                'confirmed_at' => now(),
            ]);
        } elseif ($status === 'cancelled') {
            // Restore inventory
            app(\App\Services\Inventory\InventoryService::class)->restoreInventory(
                $booking->room_type_id,
                $booking->check_in_date,
                $booking->check_out_date,
                $booking->number_of_rooms
            );

            $booking->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        $booking->refresh();

        return $booking->load(['customer:id,name,email,phone', 'roomType:id,name']);
    }

    // Admin methods
    public function getAllBookings(array $filters): LengthAwarePaginator
    {
        $query = Booking::with(['customer:id,name,email', 'hotel:id,name,city', 'roomType:id,name']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['hotel_id'])) {
            $query->where('hotel_id', $filters['hotel_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('check_in_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('check_in_date', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(15);
    }
}
