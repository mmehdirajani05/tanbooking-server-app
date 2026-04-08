<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Hotel\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(private HotelService $hotelService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'city', 'search']);
        $hotels = $this->hotelService->getAllHotels($filters);

        return $this->success('Hotels retrieved.', $hotels);
    }

    public function pending(): JsonResponse
    {
        $hotels = $this->hotelService->getAllPendingHotels();

        return $this->success('Pending hotels retrieved.', $hotels);
    }

    public function show(int $id): JsonResponse
    {
        $hotel = $this->hotelService->getHotel($id);

        return $this->success('Hotel retrieved.', $hotel);
    }

    public function approve(int $id): JsonResponse
    {
        $hotel = $this->hotelService->approveHotel($id);

        return $this->success('Hotel approved successfully.', $hotel);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $reason = $request->input('reason');
        $hotel = $this->hotelService->rejectHotel($id, $reason);

        return $this->success('Hotel rejected successfully.', $hotel);
    }
}
