<?php

namespace App\Services\Hotel;

use App\Models\Hotel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HotelService
{
    public function createHotel(array $data): Hotel
    {
        $images = $data['images'] ?? null;
        
        // Handle image uploads if provided
        if ($images && is_array($images) && count($images) > 0) {
            $imagePaths = [];
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('hotels', 'public');
                    $imagePaths[] = Storage::url($path);
                }
            }
            $images = !empty($imagePaths) ? $imagePaths : null;
        }

        return Hotel::create([
            'owner_id'       => Auth::id(),
            'name'           => $data['name'],
            'description'    => $data['description'] ?? null,
            'city'           => $data['city'],
            'area'           => $data['area'],
            'address'        => $data['address'],
            'phone'          => $data['phone'] ?? null,
            'email'          => $data['email'] ?? null,
            'amenities'      => $data['amenities'] ?? null,
            'images'         => $images,
            'check_in_time'  => $data['check_in_time'] ?? '14:00:00',
            'check_out_time' => $data['check_out_time'] ?? '12:00:00',
            'status'         => 'pending',
        ]);
    }

    public function getOwnerHotels(): LengthAwarePaginator
    {
        return Hotel::where('owner_id', Auth::id())
            ->withCount('roomTypes')
            ->withCount('bookings')
            ->latest()
            ->paginate(15);
    }

    public function getOwnerHotel(int $hotelId): Hotel
    {
        return Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->withCount('roomTypes')
            ->withCount('bookings')
            ->firstOrFail();
    }

    public function updateHotel(int $hotelId, array $data): Hotel
    {
        $hotel = Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $updateable = [];

        // Handle image uploads if provided
        if (isset($data['images']) && is_array($data['images']) && count($data['images']) > 0) {
            $imagePaths = $hotel->images ?? [];
            if (!is_array($imagePaths)) {
                $imagePaths = [];
            }
            
            foreach ($data['images'] as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('hotels', 'public');
                    $imagePaths[] = Storage::url($path);
                }
            }
            $updateable['images'] = $imagePaths;
        }

        foreach ($data as $key => $value) {
            if ($key === 'images') {
                continue; // Already handled above
            }
            if ($hotel->isFillable($key) && ! in_array($key, ['owner_id', 'status', 'approved_at', 'approved_by', 'rejection_reason'])) {
                $updateable[$key] = $value;
            }
        }

        // If hotel was rejected and owner updates it, reset to pending
        if ($hotel->status === 'rejected' && ! empty($updateable)) {
            $updateable['status'] = 'pending';
            $updateable['rejection_reason'] = null;
        }

        $hotel->update($updateable);
        $hotel->refresh();

        return $hotel;
    }

    public function deleteHotel(int $hotelId): bool
    {
        $hotel = Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return $hotel->delete();
    }

    // Admin methods
    public function getAllPendingHotels(): LengthAwarePaginator
    {
        return Hotel::where('status', 'pending')
            ->with('owner:id,name,email,phone')
            ->latest()
            ->paginate(15);
    }

    public function getAllHotels(array $filters): LengthAwarePaginator
    {
        $query = Hotel::with('owner:id,name,email,phone')
            ->withCount('roomTypes')
            ->withCount('bookings');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }

        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('city', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('area', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate(15);
    }

    public function getHotel(int $hotelId): Hotel
    {
        return Hotel::with('owner:id,name,email,phone')
            ->withCount('roomTypes')
            ->withCount('bookings')
            ->findOrFail($hotelId);
    }

    public function approveHotel(int $hotelId): Hotel
    {
        $hotel = Hotel::findOrFail($hotelId);

        $hotel->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        $hotel->refresh();

        return $hotel;
    }

    public function rejectHotel(int $hotelId, ?string $reason = null): Hotel
    {
        $hotel = Hotel::findOrFail($hotelId);

        $hotel->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $hotel->refresh();

        return $hotel;
    }

    // Public search (for customers)
    public function searchPublicHotels(array $filters): LengthAwarePaginator
    {
        $query = Hotel::where('status', 'approved')
            ->with(['roomTypes' => function ($q) {
                $q->where('is_active', true);
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

        return $query->latest()->paginate(15);
    }
}
