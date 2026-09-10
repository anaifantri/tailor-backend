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
        // 1. Membuat 1 User khusus untuk login uji coba Anda sendiri
        User::create([
            'name' => 'Admin Utama',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'password' => Hash::make('rahasia123'),
            'is_active' => true,
            'photo' => 'default.jpg',
            'remember_token' => null,
            'email_verified_at' => now(),
        ]);

        // 2. Membuat 20 User dummy acak menggunakan Factory yang sudah dibuat
        User::factory()->count(10)->create();
    }
}
