<?php

namespace Database\Seeders;

use App\Models\MeasurementHistory;
use Illuminate\Database\Seeder;

class MeasurementHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        MeasurementHistory::factory()->count(50)->create();
    }
}
