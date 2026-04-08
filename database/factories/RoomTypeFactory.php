<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['Standard', 'Deluxe', 'Suite', 'Premium', 'Executive', 'Presidential'];
        $type = fake()->randomElement($roomTypes);
        
        $prices = [
            'Standard' => fake()->numberBetween(1500, 3000),
            'Deluxe' => fake()->numberBetween(3000, 5000),
            'Suite' => fake()->numberBetween(5000, 8000),
            'Premium' => fake()->numberBetween(4000, 7000),
            'Executive' => fake()->numberBetween(3500, 6000),
            'Presidential' => fake()->numberBetween(10000, 20000),
        ];

        $occupancy = [
            'Standard' => fake()->randomElement([2, 3]),
            'Deluxe' => fake()->randomElement([2, 3, 4]),
            'Suite' => fake()->randomElement([4, 5, 6]),
            'Premium' => fake()->randomElement([3, 4]),
            'Executive' => fake()->randomElement([2, 3]),
            'Presidential' => fake()->randomElement([6, 8, 10]),
        ];

        return [
            'hotel_id' => Hotel::factory(),
            'name' => $type . ' Room',
            'description' => fake()->paragraph(2),
            'max_occupancy' => $occupancy[$type],
            'price_per_night' => $prices[$type],
            'number_of_beds' => fake()->numberBetween(1, 3),
            'amenities' => fake()->randomElements(['TV', 'AC', 'Mini Bar', 'Safe', 'Balcony', 'City View'], fake()->numberBetween(2, 5)),
            'images' => [
                'https://via.placeholder.com/800x600?text=' . $type . '+Room+1',
                'https://via.placeholder.com/800x600?text=' . $type . '+Room+2',
            ],
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the room type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
