<?php

namespace App\Http\Controllers\Api\HotelOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\UpdateBookingStatusRequest;
use App\Models\User;
use App\Services\Booking\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(int $hotelId, Request $request): JsonResponse
    {
        $status = $request->query('status');
        $bookings = $this->bookingService->getHotelBookings($hotelId, $status);

        return $this->success('Bookings retrieved.', $bookings);
    }

    public function show(int $hotelId, int $id): JsonResponse
    {
        $booking = $this->bookingService->getHotelBooking($hotelId, $id);

        return $this->success('Booking retrieved.', $booking);
    }

    public function updateStatus(UpdateBookingStatusRequest $request, int $hotelId, int $id): JsonResponse
    {
        $booking = $this->bookingService->updateBookingStatus($hotelId, $id, $request->status);

        return $this->success('Booking status updated successfully.', $booking);
    }

    /**
     * List customers for dropdown selection (hotel owner only sees their hotel's customers)
     */
    public function customers(int $hotelId, Request $request): JsonResponse
    {
        // Verify hotel ownership
        $hotel = \App\Models\Hotel::where('owner_id', auth()->id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $query = User::where('global_role', 'customer')
            ->whereHas('bookings', function ($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            });

        // Search by name, email, or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->select('id', 'name', 'email', 'phone')
            ->withCount('bookings')
            ->orderBy('name')
            ->paginate(50);

        return $this->success('Customers retrieved.', $customers);
    }

    /**
     * Create a walk-in customer (for hotel owner to create bookings without registration)
     */
    public function createWalkInCustomer(int $hotelId, Request $request): JsonResponse
    {
        // Verify hotel ownership
        $hotel = \App\Models\Hotel::where('owner_id', auth()->id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        // Check if customer already exists
        $customer = User::where('email', $validated['email'])->first();

        if ($customer) {
            return $this->success('Customer already exists.', $customer);
        }

        // Create walk-in customer
        $customer = User::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'] ?? 'walkin_' . uniqid() . '@tanbooking.com',
            'phone'               => $validated['phone'],
            'password'            => Hash::make(uniqid()), // Random password - they won't login
            'global_role'         => 'customer',
            'registration_source' => 'walk_in',
            'is_active'           => true,
            'email_verified_at'   => now(),
        ]);

        return $this->success('Walk-in customer created successfully.', $customer, 201);
    }
}
