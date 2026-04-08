<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tanbooking.com'],
            [
                'name'                => 'Super Admin',
                'phone'               => '+1234567890',
                'password'            => Hash::make('admin123'),
                'global_role'         => 'admin',
                'is_active'           => true,
                'email_verified_at'   => now(),
            ]
        );

        $this->command->info('Admin user created: admin@tanbooking.com / admin123');
    }
}
