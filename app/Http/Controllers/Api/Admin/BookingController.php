<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'hotel_id', 'date_from', 'date_to']);
        $bookings = $this->bookingService->getAllBookings($filters);

        return $this->success('Bookings retrieved.', $bookings);
    }
}
