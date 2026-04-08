<?php

namespace App\Services\Inventory;

use App\Models\Hotel;
use App\Models\Inventory;
use App\Models\RoomType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function updateInventory(int $hotelId, int $roomTypeId, array $data): Inventory
    {
        // Verify hotel ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $roomType = RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();

        $date = Carbon::parse($data['date']);

        $inventory = Inventory::firstOrNew([
            'room_type_id' => $roomType->id,
            'date'         => $date->toDateString(),
        ]);

        $totalRooms = $data['total_rooms'];
        $availableRooms = $data['available_rooms'] ?? $totalRooms;

        // Ensure available_rooms doesn't exceed total_rooms
        if ($availableRooms > $totalRooms) {
            $availableRooms = $totalRooms;
        }

        $inventory->fill([
            'total_rooms'     => $totalRooms,
            'available_rooms' => $availableRooms,
        ]);

        $inventory->save();
        $inventory->refresh();

        return $inventory;
    }

    public function bulkUpdateInventory(int $hotelId, int $roomTypeId, array $data): void
    {
        // Verify hotel ownership
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $roomType = RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $totalRooms = $data['total_rooms'];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            Inventory::updateOrCreate(
                [
                    'room_type_id' => $roomType->id,
                    'date'         => $currentDate->toDateString(),
                ],
                [
                    'total_rooms'     => $totalRooms,
                    'available_rooms' => DB::raw("CASE WHEN available_rooms IS NULL THEN {$totalRooms} ELSE LEAST(available_rooms, {$totalRooms}) END"),
                ]
            );

            $currentDate->addDay();
        }
    }

    public function getInventoryForDateRange(int $hotelId, int $roomTypeId, string $startDate, string $endDate): \Illuminate\Database\Eloquent\Collection
    {
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        RoomType::where('hotel_id', $hotelId)
            ->where('id', $roomTypeId)
            ->firstOrFail();

        return Inventory::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    public function getOwnerInventories(int $hotelId): \Illuminate\Database\Eloquent\Collection
    {
        Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return Inventory::whereHas('roomType', function ($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        })
            ->with('roomType:id,name')
            ->orderBy('date')
            ->get();
    }

    /**
     * Check availability for a room type across a date range.
     * Returns the minimum available rooms across all dates in the range.
     */
    public function checkAvailability(int $roomTypeId, string $checkInDate, string $checkOutDate): int
    {
        $startDate = Carbon::parse($checkInDate);
        $endDate = Carbon::parse($checkOutDate)->subDay(); // Check-out date is not included

        $inventories = Inventory::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // If no inventory records exist, return 0
        if ($inventories->isEmpty()) {
            return 0;
        }

        // Return the minimum available rooms across the date range
        return $inventories->min('available_rooms');
    }

    /**
     * Reduce inventory for a confirmed booking. Uses row-level locking.
     * Returns true if successful, false if insufficient inventory.
     */
    public function reduceInventory(int $roomTypeId, string $checkInDate, string $checkOutDate, int $numberOfRooms): bool
    {
        $startDate = Carbon::parse($checkInDate);
        $endDate = Carbon::parse($checkOutDate)->subDay();

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            // Lock the row for update to prevent race conditions
            $inventory = Inventory::where('room_type_id', $roomTypeId)
                ->where('date', $currentDate->toDateString())
                ->lockForUpdate()
                ->first();

            if (! $inventory || $inventory->available_rooms < $numberOfRooms) {
                return false;
            }

            $inventory->decrement('available_rooms', $numberOfRooms);
            $currentDate->addDay();
        }

        return true;
    }

    /**
     * Restore inventory for a cancelled booking.
     */
    public function restoreInventory(int $roomTypeId, string $checkInDate, string $checkOutDate, int $numberOfRooms): void
    {
        $startDate = Carbon::parse($checkInDate);
        $endDate = Carbon::parse($checkOutDate)->subDay();

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $inventory = Inventory::where('room_type_id', $roomTypeId)
                ->where('date', $currentDate->toDateString())
                ->first();

            if ($inventory) {
                $inventory->increment('available_rooms', $numberOfRooms);
            }

            $currentDate->addDay();
        }
    }
}
