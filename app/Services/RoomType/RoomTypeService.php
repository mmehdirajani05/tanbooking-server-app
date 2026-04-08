<?php

namespace App\Services\RoomType;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RoomTypeService
{
    public function createRoomType(int $hotelId, array $data): RoomType
    {
        $hotel = Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return RoomType::create([
            'hotel_id'        => $hotel->id,
            'name'            => $data['name'],
            'description'     => $data['description'] ?? null,
            'max_occupancy'   => $data['max_occupancy'],
            'price_per_night' => $data['price_per_night'],
            'number_of_beds'  => $data['number_of_beds'],
            'amenities'       => $data['amenities'] ?? null,
            'images'          => $data['images'] ?? null,
            'is_active'       => $data['is_active'] ?? true,
        ]);
    }

    public function getHotelRoomTypes(int $hotelId): LengthAwarePaginator
    {
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return RoomType::where('hotel_id', $hotelId)
            ->latest()
            ->paginate(15);
    }

    public function getRoomType(int $hotelId, int $roomTypeId): RoomType
    {
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();
    }

    public function updateRoomType(int $hotelId, int $roomTypeId, array $data): RoomType
    {
        $roomType = RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();

        // Verify hotel ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $updatable = [];
        foreach ($data as $key => $value) {
            if ($roomType->isFillable($key) && $key !== 'hotel_id') {
                $updatable[$key] = $value;
            }
        }

        $roomType->update($updatable);
        $roomType->refresh();

        return $roomType;
    }

    public function deleteRoomType(int $hotelId, int $roomTypeId): bool
    {
        $roomType = RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();

        // Verify hotel ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return $roomType->delete();
    }

    // Public method for customers
    public function getAvailableRoomTypes(int $hotelId): \Illuminate\Database\Eloquent\Collection
    {
        return RoomType::where('hotel_id', $hotelId)
            ->where('is_active', true)
            ->get();
    }
}
