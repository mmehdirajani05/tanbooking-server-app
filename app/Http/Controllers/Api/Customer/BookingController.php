<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Booking\SearchHotelsRequest;
use App\Http\Requests\Api\Booking\StoreBookingRequest;
use App\Http\Requests\Api\Booking\UpdateBookingStatusRequest;
use App\Services\Booking\BookingService;
use App\Services\Hotel\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private HotelService $hotelService
    ) {}

    public function search(SearchHotelsRequest $request): JsonResponse
    {
        $results = $this->bookingService->searchHotels($request->validated());

        return $this->success('Search results retrieved.', $results);
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->createBooking($request->validated());

        return $this->success('Booking created successfully.', $booking->load(['hotel:id,name,city', 'roomType:id,name']), 201);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $bookings = $this->bookingService->getCustomerBookings($status);

        return $this->success('Bookings retrieved.', $bookings);
    }

    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingService->getCustomerBooking($id);

        return $this->success('Booking retrieved.', $booking);
    }

    public function cancel(int $id): JsonResponse
    {
        $booking = $this->bookingService->cancelBooking($id);

        return $this->success('Booking cancelled successfully.', $booking);
    }
}
