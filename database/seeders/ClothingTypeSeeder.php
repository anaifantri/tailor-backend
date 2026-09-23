<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use Illuminate\Database\Seeder;

class ClothingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClothingType::factory()->count(8)->create();
    }
}