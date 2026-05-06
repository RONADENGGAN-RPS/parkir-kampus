<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::firstOrCreate(
            ['email' => 'superadmin@kampus.ac.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => 1,       // role Super Admin (id=1)
                'active' => true,
            ]
        );

        // Admin Kampus
        User::firstOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Admin Kampus',
                'password' => Hash::make('password'),
                'role_id' => 2,       // role Admin (id=2)
                'active' => true,
            ]
        );

        // Petugas Parkir Kampus
        User::firstOrCreate(
            ['email' => 'pertugasparkir@kampus.ac.id'],
            [
                'name' => 'Petugas Parkir Kampus',
                'password' => Hash::make('password'),
                'role_id' => 3,       // role Petugas (id=3)
                'active' => true,
            ]
        );
    }
}