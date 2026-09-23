<?php

namespace Database\Seeders;

use App\Models\ClothingType;
use App\Models\Customer;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data referensi yang ada
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory(3)->create();
        }

        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = Customer::factory(10)->create();
        }

        $clothingTypes = ClothingType::all();
        $materials = Material::all();

        // Pemetaan skema pengukuran berdasarkan kategori
        $measurementSchema = [
            'rok' => [
                'Panjang Rok',
                'Lingkar Pinggang',
                'Lingkar Pinggul',
            ],
            'celana' => [
                'Panjang Celana',
                'Lingkar Pinggang',
                'Lingkar Pinggul',
                'Pesak',
                'Paha',
                'Lutut',
                'Kaki',
            ],
            'baju' => [
                'Panjang Badan',
                'Lebar Bahu',
                'Panjang Tangan',
                'Lingkar Lengan',
                'Manset',
                'Lingkar Badan',
                'Lingkar Perut',
                'Lingkar Pinggul',
                'Lebar Dada',
                'Lebar Punggung',
                'Lingkar Leher',
            ],
        ];

        // Buat 20 order dummy
        Order::factory(20)->make()->each(function ($order) use ($users, $customers, $clothingTypes, $materials, $measurementSchema) {
            $order->user_id = $users->random()->id;
            $order->customer_id = $customers->random()->id;
            $order->save();

            $subtotalSum = 0;
            $itemCount = rand(1, 3);

            // 1. Buat OrderDetail
            for ($i = 0; $i < $itemCount; $i++) {
                $clothingType = $clothingTypes->isNotEmpty() ? $clothingTypes->random() : null;
                $material = $materials->isNotEmpty() ? $materials->random() : null;

                $category = strtolower($clothingType?->category ?? 'baju');
                $fields = $measurementSchema[$category] ?? $measurementSchema['baju'];

                $measurementsData = [];
                foreach ($fields as $field) {
                    $measurementsData[] = [
                        'name' => $field,
                        'value' => rand(30, 110),
                    ];
                }

                $price = $clothingType?->base_price ?? rand(150000, 500000);
                $quantity = rand(1, 2);
                $subtotalSum += ($price * $quantity);

                OrderDetail::create([
                    'order_id' => $order->id,
                    'clothing_type_id' => $clothingType?->id,
                    'material_id' => $material?->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'fabric_consumed_meter' => rand(1, 3) + (rand(0, 9) / 10),
                    'measurements' => json_encode($measurementsData),
                    'notes' => 'Catatan jahit khusus item ' . ($i + 1),
                ]);
            }

            // 2. Hitung & simpan total order
            $grandTotal = max(0, $subtotalSum - $order->discount + $order->tax);
            $order->update(['total' => $grandTotal]);

            // 3. Buat Down Payment (DP) untuk 70% dari total order (sebagian order)
            $hasDownPayment = rand(1, 100) <= 70; // 70% probabilitas mendapat DP

            if ($hasDownPayment && $grandTotal > 0) {
                // DP sebesar 30% - 50% dari total harga
                $dpPercentage = rand(30, 50) / 100;
                $amountPaid = round($grandTotal * $dpPercentage);

                Payment::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'payment_date' => $order->order_date, // DP dilakukan di tanggal pemesanan
                    'amount_paid' => $amountPaid,
                    'payment_method' => fake()->randomElement(['cash', 'transfer', 'qris']),
                    'payment_status' => 'down_payment', // atau 'dp' / 'partial' sesuai enum/string pilihanmu
                    'notes' => 'Uang muka (DP) saat pemesanan',
                ]);
            }
        });
    }
}