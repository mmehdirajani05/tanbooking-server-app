<?php

namespace App\Http\Controllers\Api\HotelOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoomType\StoreRoomTypeRequest;
use App\Http\Requests\Api\RoomType\UpdateRoomTypeRequest;
use App\Services\RoomType\RoomTypeService;
use Illuminate\Http\JsonResponse;

class RoomTypeController extends Controller
{
    public function __construct(private RoomTypeService $roomTypeService) {}

    public function store(StoreRoomTypeRequest $request, int $hotelId): JsonResponse
    {
        $roomType = $this->roomTypeService->createRoomType($hotelId, $request->validated());

        return $this->success('Room type created successfully.', $roomType, 201);
    }

    public function index(int $hotelId): JsonResponse
    {
        $roomTypes = $this->roomTypeService->getHotelRoomTypes($hotelId);

        return $this->success('Room types retrieved.', $roomTypes);
    }

    public function show(int $hotelId, int $id): JsonResponse
    {
        $roomType = $this->roomTypeService->getRoomType($hotelId, $id);

        return $this->success('Room type retrieved.', $roomType);
    }

    public function update(UpdateRoomTypeRequest $request, int $hotelId, int $id): JsonResponse
    {
        $roomType = $this->roomTypeService->updateRoomType($hotelId, $id, $request->validated());

        return $this->success('Room type updated successfully.', $roomType);
    }

    public function destroy(int $hotelId, int $id): JsonResponse
    {
        $this->roomTypeService->deleteRoomType($hotelId, $id);

        return $this->success('Room type deleted successfully.');
    }
}
