<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Customer;
use App\Models\MeasurementHistory;
use Illuminate\Database\Seeder;

class MeasurementHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memastikan data Customer & ClothingType tersedia sebelum seeding
        if (Customer::count() === 0) {
            Customer::factory()->count(10)->create();
        }

        if (ClothingType::count() === 0) {
            ClothingType::factory()->count(5)->create();
        }

        // Jalankan pembuatan data dummy
        MeasurementHistory::factory()->count(50)->create();
    }
}