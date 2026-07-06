<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@siwatu.com'],
            [
                'name' => 'Admin Siwatu',
                'password' => Hash::make('siwatu123'),
                'role' => 'admin',
            ]
        );
    }
}