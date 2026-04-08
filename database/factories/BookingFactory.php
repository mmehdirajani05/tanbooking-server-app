<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = Carbon::today()->addDays(fake()->numberBetween(1, 60));
        $nights = fake()->numberBetween(1, 7);
        $checkOutDate = $checkInDate->copy()->addDays($nights);
        $numberOfRooms = fake()->numberBetween(1, 4);
        $pricePerNight = fake()->numberBetween(2000, 8000);
        
        return [
            'customer_id' => User::factory()->customer(),
            'hotel_id' => Hotel::factory()->approved(),
            'room_type_id' => RoomType::factory(),
            'booking_reference' => Booking::generateBookingReference(),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'check_in_date' => $checkInDate,
            'check_out_date' => $checkOutDate,
            'number_of_rooms' => $numberOfRooms,
            'number_of_guests' => fake()->numberBetween(1, 4),
            'total_price' => $pricePerNight * $nights * $numberOfRooms,
            'status' => fake()->randomElement(['pending', 'confirmed', 'confirmed', 'cancelled']),
            'notes' => fake()->optional(0.3)->sentence(),
            'confirmed_at' => fake()->optional(0.6)->dateTimeThisMonth(),
            'cancelled_at' => null,
        ];
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'confirmed_at' => null,
        ]);
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}
