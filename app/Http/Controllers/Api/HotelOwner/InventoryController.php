<?php

namespace App\Http\Controllers\Api\HotelOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Inventory\BulkUpdateInventoryRequest;
use App\Http\Requests\Api\Inventory\UpdateInventoryRequest;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\JsonResponse;

class InventoryController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index(int $hotelId): JsonResponse
    {
        $inventories = $this->inventoryService->getOwnerInventories($hotelId);

        return $this->success('Inventory retrieved.', $inventories);
    }

    public function update(UpdateInventoryRequest $request, int $hotelId, int $roomTypeId): JsonResponse
    {
        $inventory = $this->inventoryService->updateInventory($hotelId, $roomTypeId, $request->validated());

        return $this->success('Inventory updated successfully.', $inventory);
    }

    public function bulkUpdate(BulkUpdateInventoryRequest $request, int $hotelId, int $roomTypeId): JsonResponse
    {
        $this->inventoryService->bulkUpdateInventory($hotelId, $roomTypeId, $request->validated());

        return $this->success('Inventory updated for date range successfully.');
    }

    public function show(int $hotelId, int $roomTypeId, string $startDate, string $endDate): JsonResponse
    {
        $inventories = $this->inventoryService->getInventoryForDateRange($hotelId, $roomTypeId, $startDate, $endDate);

        return $this->success('Inventory for date range retrieved.', $inventories);
    }
}
