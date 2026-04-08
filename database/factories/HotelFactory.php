<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Kolkata'];
        $city = fake()->randomElement($cities);
        
        $areas = [
            'Mumbai' => ['Andheri', 'Bandra', 'Juhu', 'Powai', 'Colaba'],
            'Delhi' => ['Connaught Place', 'Karol Bagh', 'Hauz Khas', 'Dwarka', 'Rohini'],
            'Bangalore' => ['Koramangala', 'Indiranagar', 'Whitefield', 'Electronic City', 'MG Road'],
            'Chennai' => ['T Nagar', 'Anna Nagar', 'Adyar', 'Velachery', 'Mylapore'],
            'Kolkata' => ['Park Street', 'Salt Lake', 'New Town', 'Ballygunge', 'Howrah'],
        ];

        return [
            'owner_id' => User::factory(),
            'name' => fake()->company() . ' Hotel',
            'description' => fake()->paragraph(3),
            'city' => $city,
            'area' => fake()->randomElement($areas[$city]),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'amenities' => fake()->randomElements(['WiFi', 'Pool', 'Gym', 'Spa', 'Restaurant', 'Parking', 'Room Service', 'Laundry'], fake()->numberBetween(2, 6)),
            'images' => [
                'https://via.placeholder.com/800x600?text=Hotel+Image+1',
                'https://via.placeholder.com/800x600?text=Hotel+Image+2',
                'https://via.placeholder.com/800x600?text=Hotel+Image+3',
            ],
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'status' => fake()->randomElement(['pending', 'approved', 'approved', 'approved']),
            'approved_at' => fake()->dateTimeThisMonth(),
            'approved_by' => null,
        ];
    }

    /**
     * Indicate that the hotel is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_at' => null,
        ]);
    }

    /**
     * Indicate that the hotel is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the hotel is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_at' => null,
            'rejection_reason' => 'Incomplete information or does not meet standards.',
        ]);
    }
}
