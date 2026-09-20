<?php

namespace Database\Seeders;

use App\Models\Tailor;
use Illuminate\Database\Seeder;

class TailorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tailor::factory()->count(10)->create();
    }
}
