<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::firstOrCreate(
            ['email' => 'admin@ktmwdc.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'phone' => '9800000000',
                'is_admin' => true,
                'role' => 'admin',
                'user_type' => 'admin',
            ]
        );

        // Client Users
        User::firstOrCreate(
            ['email' => 'client@ktmwdc.com'],
            [
                'name' => 'Client User',
                'password' => Hash::make('password123'),
                'phone' => '9800000001',
                'is_client' => true,
                'role' => 'client',
                'user_type' => 'client',
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'phone' => '9800000002',
                'is_client' => true,
                'role' => 'client',
                'user_type' => 'client',
            ]
        );

        // Driver Users
        User::firstOrCreate(
            ['email' => 'driver@ktmwdc.com'],
            [
                'name' => 'Driver User',
                'password' => Hash::make('password123'),
                'phone' => '9800000003',
                'is_driver' => true,
                'role' => 'driver',
                'user_type' => 'driver',
            ]
        );

        User::firstOrCreate(
            ['email' => 'madan@driver.com'],
            [
                'name' => 'Madan Driver',
                'password' => Hash::make('password123'),
                'phone' => '9800000004',
                'is_driver' => true,
                'role' => 'driver',
                'user_type' => 'driver',
            ]
        );

        // Equipment Owner
        User::firstOrCreate(
            ['email' => 'equipment@equipment.com'],
            [
                'name' => 'Equipment Owner',
                'password' => Hash::make('password123'),
                'phone' => '9800000005',
                'is_equipment_owner' => true,
                'role' => 'equipment_owner',
                'user_type' => 'equipment_owner',
            ]
        );

        // Property Owner
        User::firstOrCreate(
            ['email' => 'warehouse@warehouse.com'],
            [
                'name' => 'Warehouse Owner',
                'password' => Hash::make('password123'),
                'phone' => '9800000006',
                'is_property_owner' => true,
                'role' => 'property_owner',
                'user_type' => 'property_owner',
            ]
        );

        $this->command->info('Users seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@ktmwdc.com / password123');
        $this->command->info('Client: client@ktmwdc.com / password123');
        $this->command->info('Driver: driver@ktmwdc.com / password123');
        $this->command->info('Equipment: equipment@equipment.com / password123');
        $this->command->info('Property: warehouse@warehouse.com / password123');
    }
}