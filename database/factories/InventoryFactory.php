<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalRooms = fake()->numberBetween(5, 20);
        
        return [
            'room_type_id' => RoomType::factory(),
            'date' => Carbon::today()->addDays(fake()->numberBetween(0, 90)),
            'total_rooms' => $totalRooms,
            'available_rooms' => fake()->numberBetween(0, $totalRooms),
        ];
    }

    /**
     * Create inventory for a date range.
     */
    public function forDateRange(Carbon $startDate, Carbon $endDate): static
    {
        return $this->state(function (array $attributes) use ($startDate, $endDate) {
            $totalRooms = fake()->numberBetween(5, 20);
            return [
                'total_rooms' => $totalRooms,
                'available_rooms' => fake()->numberBetween(0, $totalRooms),
            ];
        });
    }
}
