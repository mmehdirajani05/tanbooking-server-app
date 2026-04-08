<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Inventory;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🌱 Starting Demo Data Seeding...\n\n";

        // 1. Create Admin User
        echo "📝 Creating Admin User...\n";
        $admin = User::updateOrCreate(
            ['email' => 'admin@tanbooking.com'],
            [
                'name'                => 'Super Admin',
                'phone'               => '+91 9876543210',
                'password'            => Hash::make('admin123'),
                'global_role'         => 'admin',
                'is_active'           => true,
                'email_verified_at'   => now(),
            ]
        );
        echo "✅ Admin: admin@tanbooking.com / admin123\n\n";

        // 2. Create Hotel Owners (Partners)
        echo "📝 Creating Hotel Owners...\n";
        $owner1 = User::factory()->partner()->create([
            'name' => 'Rajesh Kumar',
            'email' => 'rajesh@hotels.com',
            'phone' => '+91 9876543211',
            'password' => Hash::make('partner123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Owner 1: rajesh@hotels.com / partner123\n";

        $owner2 = User::factory()->partner()->create([
            'name' => 'Priya Sharma',
            'email' => 'priya@luxuryhotels.com',
            'phone' => '+91 9876543212',
            'password' => Hash::make('partner123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Owner 2: priya@luxuryhotels.com / partner123\n\n";

        // 3. Create Customers
        echo "📝 Creating Customers...\n";
        $customer1 = User::factory()->customer()->create([
            'name' => 'Amit Patel',
            'email' => 'amit@example.com',
            'phone' => '+91 9876543213',
            'password' => Hash::make('customer123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Customer 1: amit@example.com / customer123\n";

        $customer2 = User::factory()->customer()->create([
            'name' => 'Sneha Reddy',
            'email' => 'sneha@example.com',
            'phone' => '+91 9876543214',
            'password' => Hash::make('customer123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Customer 2: sneha@example.com / customer123\n";

        $customer3 = User::factory()->customer()->create([
            'name' => 'Vikram Singh',
            'email' => 'vikram@example.com',
            'phone' => '+91 9876543215',
            'password' => Hash::make('customer123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Customer 3: vikram@example.com / customer123\n";

        $customer4 = User::factory()->customer()->create([
            'name' => 'Ananya Iyer',
            'email' => 'ananya@example.com',
            'phone' => '+91 9876543216',
            'password' => Hash::make('customer123'),
            'email_verified_at' => now(),
        ]);
        echo "✅ Customer 4: ananya@example.com / customer123\n\n";

        // 4. Create Hotels
        echo "📝 Creating Hotels...\n";
        
        // Owner 1 Hotels
        $hotel1 = Hotel::create([
            'owner_id' => $owner1->id,
            'name' => 'Grand Mumbai Palace',
            'description' => 'Luxurious 5-star hotel in the heart of Mumbai with stunning sea views. Featuring world-class amenities, multiple restaurants, and exceptional service.',
            'city' => 'Mumbai',
            'area' => 'Bandra',
            'address' => '123 Marine Drive, Bandra West, Mumbai - 400050',
            'phone' => '+91 22 6789 1234',
            'email' => 'info@grandmumbaipalace.com',
            'amenities' => ['WiFi', 'Pool', 'Gym', 'Spa', 'Restaurant', 'Parking', 'Room Service', 'Laundry', 'Business Center'],
            'images' => [
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800',
                'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800',
            ],
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
        echo "✅ Hotel 1: Grand Mumbai Palace (Owner: Rajesh Kumar)\n";

        $hotel2 = Hotel::create([
            'owner_id' => $owner1->id,
            'name' => 'Delhi Heritage Hotel',
            'description' => 'Experience the rich cultural heritage of Delhi in this beautifully restored haveli with modern amenities.',
            'city' => 'Delhi',
            'area' => 'Connaught Place',
            'address' => '45 Connaught Circus, New Delhi - 110001',
            'phone' => '+91 11 4567 8901',
            'email' => 'reservations@delhiheritage.com',
            'amenities' => ['WiFi', 'Restaurant', 'Parking', 'Room Service', 'Heritage Tours'],
            'images' => [
                'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=800',
                'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800',
            ],
            'check_in_time' => '14:00:00',
            'check_out_time' => '11:00:00',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
        echo "✅ Hotel 2: Delhi Heritage Hotel (Owner: Rajesh Kumar)\n";

        // Owner 2 Hotels
        $hotel3 = Hotel::create([
            'owner_id' => $owner2->id,
            'name' => 'Bangalore Tech Suites',
            'description' => 'Modern business hotel in Bangalore\'s tech hub with state-of-the-art facilities and conference rooms.',
            'city' => 'Bangalore',
            'area' => 'Whitefield',
            'address' => '78 ITPL Main Road, Whitefield, Bangalore - 560066',
            'phone' => '+91 80 2345 6789',
            'email' => 'info@bangaloretechsuites.com',
            'amenities' => ['WiFi', 'Gym', 'Restaurant', 'Business Center', 'Conference Rooms', 'Parking'],
            'images' => [
                'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800',
                'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=800',
            ],
            'check_in_time' => '15:00:00',
            'check_out_time' => '12:00:00',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
        echo "✅ Hotel 3: Bangalore Tech Suites (Owner: Priya Sharma)\n";

        $hotel4 = Hotel::create([
            'owner_id' => $owner2->id,
            'name' => 'Chennai Beach Resort',
            'description' => 'Beachfront resort offering breathtaking views of the Bay of Bengal with traditional Tamil hospitality.',
            'city' => 'Chennai',
            'area' => 'Marina Beach',
            'address' => '12 Marina Beach Road, Chennai - 600001',
            'phone' => '+91 44 2345 6789',
            'email' => 'bookings@chennaibeachresort.com',
            'amenities' => ['WiFi', 'Pool', 'Beach Access', 'Restaurant', 'Spa', 'Water Sports'],
            'images' => [
                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800',
                'https://images.unsplash.com/photo-1587213811864-46e59f6873b1?w=800',
            ],
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
        ]);
        echo "✅ Hotel 4: Chennai Beach Resort (Owner: Priya Sharma) - PENDING APPROVAL\n\n";

        // 5. Create Room Types
        echo "📝 Creating Room Types...\n";
        
        // Hotel 1 Room Types
        $room1 = RoomType::create([
            'hotel_id' => $hotel1->id,
            'name' => 'Deluxe Sea View',
            'description' => 'Spacious room with breathtaking views of the Arabian Sea',
            'max_occupancy' => 3,
            'price_per_night' => 5500.00,
            'number_of_beds' => 2,
            'amenities' => ['TV', 'AC', 'Mini Bar', 'Safe', 'Balcony', 'Sea View'],
            'images' => ['https://images.unsplash.com/photo-1611892440504-42a792e24d6f?w=800'],
            'is_active' => true,
        ]);

        $room2 = RoomType::create([
            'hotel_id' => $hotel1->id,
            'name' => 'Presidential Suite',
            'description' => 'Luxurious suite with separate living area and premium amenities',
            'max_occupancy' => 6,
            'price_per_night' => 15000.00,
            'number_of_beds' => 3,
            'amenities' => ['TV', 'AC', 'Mini Bar', 'Safe', 'Balcony', 'Sea View', 'Jacuzzi', 'Butler Service'],
            'images' => ['https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800'],
            'is_active' => true,
        ]);

        // Hotel 2 Room Types
        $room3 = RoomType::create([
            'hotel_id' => $hotel2->id,
            'name' => 'Heritage Deluxe',
            'description' => 'Beautifully decorated room with traditional Rajasthani artwork',
            'max_occupancy' => 2,
            'price_per_night' => 3500.00,
            'number_of_beds' => 1,
            'amenities' => ['TV', 'AC', 'WiFi', 'Heritage Decor'],
            'images' => ['https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=800'],
            'is_active' => true,
        ]);

        // Hotel 3 Room Types
        $room4 = RoomType::create([
            'hotel_id' => $hotel3->id,
            'name' => 'Business Executive',
            'description' => 'Modern room designed for business travelers with work desk',
            'max_occupancy' => 2,
            'price_per_night' => 4000.00,
            'number_of_beds' => 1,
            'amenities' => ['TV', 'AC', 'WiFi', 'Work Desk', 'Coffee Maker'],
            'images' => ['https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800'],
            'is_active' => true,
        ]);
        echo "✅ Created 4 Room Types\n\n";

        // 6. Create Inventory
        echo "📝 Creating Inventory for next 90 days...\n";
        $inventories = [];
        foreach ([$room1, $room2, $room3, $room4] as $room) {
            $totalRooms = $room->name === 'Presidential Suite' ? 5 : 15;
            for ($i = 0; $i < 90; $i++) {
                $inventories[] = [
                    'room_type_id' => $room->id,
                    'date' => Carbon::today()->addDays($i),
                    'total_rooms' => $totalRooms,
                    'available_rooms' => fake()->numberBetween($totalRooms - 5, $totalRooms),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert in chunks to avoid memory issues
        collect($inventories)->chunk(500)->each(function ($chunk) {
            Inventory::insert($chunk->toArray());
        });
        echo "✅ Created " . count($inventories) . " inventory records\n\n";

        // 7. Create Bookings
        echo "📝 Creating Bookings...\n";
        
        $booking1 = \App\Models\Booking::create([
            'customer_id' => $customer1->id,
            'hotel_id' => $hotel1->id,
            'room_type_id' => $room1->id,
            'booking_reference' => \App\Models\Booking::generateBookingReference(),
            'guest_name' => $customer1->name,
            'guest_email' => $customer1->email,
            'guest_phone' => $customer1->phone,
            'check_in_date' => Carbon::today()->addDays(10),
            'check_out_date' => Carbon::today()->addDays(13),
            'number_of_rooms' => 2,
            'number_of_guests' => 3,
            'total_price' => 5500.00 * 3 * 2,
            'status' => 'confirmed',
            'notes' => 'Anniversary celebration - please arrange cake',
            'confirmed_at' => now(),
        ]);
        echo "✅ Booking 1: {$booking1->booking_reference} (Confirmed)\n";

        $booking2 = \App\Models\Booking::create([
            'customer_id' => $customer2->id,
            'hotel_id' => $hotel2->id,
            'room_type_id' => $room3->id,
            'booking_reference' => \App\Models\Booking::generateBookingReference(),
            'guest_name' => $customer2->name,
            'guest_email' => $customer2->email,
            'guest_phone' => $customer2->phone,
            'check_in_date' => Carbon::today()->addDays(20),
            'check_out_date' => Carbon::today()->addDays(22),
            'number_of_rooms' => 1,
            'number_of_guests' => 2,
            'total_price' => 3500.00 * 2,
            'status' => 'pending',
            'notes' => null,
            'confirmed_at' => null,
        ]);
        echo "✅ Booking 2: {$booking2->booking_reference} (Pending)\n";

        $booking3 = \App\Models\Booking::create([
            'customer_id' => $customer1->id,
            'hotel_id' => $hotel3->id,
            'room_type_id' => $room4->id,
            'booking_reference' => \App\Models\Booking::generateBookingReference(),
            'guest_name' => $customer1->name,
            'guest_email' => $customer1->email,
            'guest_phone' => $customer1->phone,
            'check_in_date' => Carbon::today()->addDays(30),
            'check_out_date' => Carbon::today()->addDays(32),
            'number_of_rooms' => 1,
            'number_of_guests' => 1,
            'total_price' => 4000.00 * 2,
            'status' => 'cancelled',
            'notes' => 'Business trip postponed',
            'confirmed_at' => null,
            'cancelled_at' => now(),
        ]);
        echo "✅ Booking 3: {$booking3->booking_reference} (Cancelled)\n";

        $booking4 = \App\Models\Booking::create([
            'customer_id' => $customer3->id,
            'hotel_id' => $hotel1->id,
            'room_type_id' => $room2->id,
            'booking_reference' => \App\Models\Booking::generateBookingReference(),
            'guest_name' => $customer3->name,
            'guest_email' => $customer3->email,
            'guest_phone' => $customer3->phone,
            'check_in_date' => Carbon::today()->addDays(15),
            'check_out_date' => Carbon::today()->addDays(18),
            'number_of_rooms' => 1,
            'number_of_guests' => 4,
            'total_price' => 15000.00 * 3,
            'status' => 'pending',
            'notes' => 'Honeymoon package requested',
            'confirmed_at' => null,
        ]);
        echo "✅ Booking 4: {$booking4->booking_reference} (Pending)\n";

        $booking5 = \App\Models\Booking::create([
            'customer_id' => $customer4->id,
            'hotel_id' => $hotel2->id,
            'room_type_id' => $room3->id,
            'booking_reference' => \App\Models\Booking::generateBookingReference(),
            'guest_name' => $customer4->name,
            'guest_email' => $customer4->email,
            'guest_phone' => $customer4->phone,
            'check_in_date' => Carbon::today()->addDays(25),
            'check_out_date' => Carbon::today()->addDays(27),
            'number_of_rooms' => 2,
            'number_of_guests' => 2,
            'total_price' => 3500.00 * 2 * 2,
            'status' => 'confirmed',
            'notes' => null,
            'confirmed_at' => now(),
        ]);
        echo "✅ Booking 5: {$booking5->booking_reference} (Confirmed)\n\n";

        echo "🎉 Demo Data Seeding Complete!\n\n";
        echo "📊 Summary:\n";
        echo "   - 1 Admin User\n";
        echo "   - 2 Hotel Owners\n";
        echo "   - 4 Customers\n";
        echo "   - 4 Hotels (3 approved, 1 pending)\n";
        echo "   - 4 Room Types\n";
        echo "   - " . count($inventories) . " Inventory Records\n";
        echo "   - 5 Bookings (2 confirmed, 2 pending, 1 cancelled)\n\n";

        echo "🔐 Login Credentials:\n";
        echo "   Admin:    admin@tanbooking.com / admin123\n";
        echo "   Owner:    rajesh@hotels.com / partner123\n";
        echo "             priya@luxuryhotels.com / partner123\n";
        echo "   Customer: amit@example.com / customer123\n";
        echo "             sneha@example.com / customer123\n";
        echo "             vikram@example.com / customer123\n";
        echo "             ananya@example.com / customer123\n";
    }
}
