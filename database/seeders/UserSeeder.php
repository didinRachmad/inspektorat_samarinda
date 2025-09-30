<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Auditor
        $auditor = User::firstOrCreate(
            ['email' => 'auditor@gmail.com'],
            [
                'name' => 'Auditor User',
                'password' => Hash::make('12345678'),
            ]
        );
        $auditor->assignRole('auditor');

        // Auditi
        $auditi = User::firstOrCreate(
            ['email' => 'auditi@gmail.com'],
            [
                'name' => 'Auditi User',
                'password' => Hash::make('12345678'),
            ]
        );
        $auditi->assignRole('auditi');
    }
}
