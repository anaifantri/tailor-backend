<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'Administrator',
            'email' => 'admin@exampel.com',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'is_active' => true,
            'photo' => null,
            'remember_token' => null,
            'email_verified_at' => now(),
        ]);

        User::factory()->count(5)->create();
    }
}
