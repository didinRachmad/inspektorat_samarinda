<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ========== SUPER ADMIN ==========
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // ========== APPROVER ==========
        $approver = User::firstOrCreate(
            ['email' => 'approver@gmail.com'],
            [
                'name' => 'Approver User',
                'password' => Hash::make('12345678'),
            ]
        );
        $approver->assignRole('approver');

        // ========== IRBANWIL (AUDITOR) ==========
        $irbanwils = [
            1 => 'Irbanwil I',
            2 => 'Irbanwil II',
            3 => 'Irbanwil III',
            4 => 'Irbanwil IV',
            5 => 'Irbanwil Khusus',
        ];

        foreach ($irbanwils as $id => $nama) {
            $email = 'irbanwil' . $id . '@gmail.com';
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('12345678'),
                    'irbanwil_id' => $id,
                ]
            );
            $user->assignRole('auditor');
        }

        // ========== AUDITI ==========
        $auditis = [
            1  => ['nama' => 'Dinas Kesehatan',                 'email' => 'dinkes@gmail.com'],
            2  => ['nama' => 'Dinas Pendidikan',                'email' => 'disdik@gmail.com'],
            3  => ['nama' => 'Dinas Pekerjaan Umum',            'email' => 'dpu@gmail.com'],
            4  => ['nama' => 'Dinas Perhubungan',               'email' => 'dishub@gmail.com'],
            5  => ['nama' => 'Dinas Sosial',                    'email' => 'dinsos@gmail.com'],
            6  => ['nama' => 'Dinas Komunikasi dan Informatika', 'email' => 'diskominfo@gmail.com'],
            7  => ['nama' => 'Dinas Lingkungan Hidup',          'email' => 'dlh@gmail.com'],
            8  => ['nama' => 'Dinas Pertanian',                 'email' => 'distan@gmail.com'],
            9  => ['nama' => 'Dinas Tenaga Kerja',              'email' => 'disnaker@gmail.com'],
            10 => ['nama' => 'Dinas Pariwisata',                'email' => 'dispar@gmail.com'],
        ];

        foreach ($auditis as $id => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'password' => Hash::make('12345678'),
                    'auditi_id' => $id,
                ]
            );
            $user->assignRole('auditi');
        }
    }
}
