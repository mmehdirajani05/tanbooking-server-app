<?php

namespace App\Http\Controllers\Api\HotelOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Hotel\StoreHotelRequest;
use App\Http\Requests\Api\Hotel\UpdateHotelRequest;
use App\Models\Hotel;
use App\Services\Hotel\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct(private HotelService $hotelService) {}

    public function store(StoreHotelRequest $request): JsonResponse
    {
        $hotel = $this->hotelService->createHotel($request->validated());

        return $this->success('Hotel created successfully and is pending approval.', $hotel->load('owner:id,name,email'), 201);
    }

    public function index(): JsonResponse
    {
        $hotels = $this->hotelService->getOwnerHotels();

        return $this->success('Hotels retrieved.', $hotels);
    }

    public function show(int $id): JsonResponse
    {
        $hotel = $this->hotelService->getOwnerHotel($id);

        return $this->success('Hotel retrieved.', $hotel);
    }

    public function update(UpdateHotelRequest $request, int $id): JsonResponse
    {
        $hotel = $this->hotelService->updateHotel($id, $request->validated());

        return $this->success('Hotel updated successfully.', $hotel);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->hotelService->deleteHotel($id);

        return $this->success('Hotel deleted successfully.');
    }
}
