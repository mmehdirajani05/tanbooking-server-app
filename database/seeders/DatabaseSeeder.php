<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DemoDataSeeder::class,
        ]);

        // Create additional random users
        User::factory()->customer()->count(5)->create();
        User::factory()->partner()->count(3)->create();
    }
}
