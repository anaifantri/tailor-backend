<?php

namespace App\Services;

use App\Models\ClothingType;
use App\Models\Material;
use App\Models\MeasurementHistory;
use App\Models\Payment;
use App\Repositories\OrderRepository;
// use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAll(int $perPage = 10, mixed $month = null, ?int $year = null, ?string $search = null)
    {
        return $this->orderRepository->getAll($perPage, $month, $year, $search);
    }

    public function getUnpaid(?string $search = null)
    {
        return $this->orderRepository->getUnpaid($search);
    }

    public function getBySearch(?string $search = null)
    {
        return $this->orderRepository->getBySearch($search);
    }

    public function getByUlid(string $ulid)
    {
        return $this->orderRepository->getByUlid($ulid);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->create($data);

            if (!empty($data['order_details'])) {
                $orderDetailsData = [];

                foreach ($data['order_details'] as $item) {
                    $clothingType = ClothingType::where('ulid', $item['clothing_type_ulid'])->first();
                    $material = !empty($item['material_ulid']) 
                        ? Material::where('ulid', $item['material_ulid'])->first() 
                        : null;
                    $measurementHistory = !empty($item['measurement_history_ulid']) 
                        ? MeasurementHistory::where('ulid', $item['measurement_history_ulid'])->first() 
                        : null;

                    $orderDetailsData[] = [
                        'clothing_type_id' => $clothingType?->id,
                        'clothing_type_ulid' => $item['clothing_type_ulid'],
                        'material_id' => $material?->id,
                        'material_ulid' => $item['material_ulid'] ?? null,
                        'measurement_history_id' => $measurementHistory?->id,
                        'measurement_history_ulid' => $item['measurement_history_ulid'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'fabric_consumed_meter' => $item['fabric_consumed_meter'] ?? 0,
                        'notes' => $item['notes'] ?? null,
                        'measurements' => $item['measurements'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $order->orderDetails()->createMany($orderDetailsData);
            }

            $amountPaid = (float) ($data['amount_paid'] ?? 0);
            if ($amountPaid > 0) {
                $paymentStatus = $amountPaid >= $data['total'] ? 'full_payment' : 'down_payment';

                Payment::create([
                    'order_id' => $order->id,
                    'order_ulid' => $order->ulid,
                    'user_id' => $data['user_id'],
                    'user_ulid' => $data['user_ulid'],
                    'payment_date' => $data['payment_date'] ?? now(),
                    'amount_paid' => $amountPaid,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_status' => $paymentStatus,
                    'notes' => $data['notes'] ?? 'Pembayaran awal saat pemesanan',
                ]);
            }

            return $order->load(['orderDetails', 'payments']);
        });
    }

    public function update(string $ulid, array $data)
    {
        return DB::transaction(function () use ($ulid, $data) {
            $order = $this->orderRepository->update($ulid, $data);

            if (isset($data['order_details'])) {
                $order->orderDetails()->delete();
                $orderDetailsData = [];

                foreach ($data['order_details'] as $item) {
                    $clothingType = ClothingType::where('ulid', $item['clothing_type_ulid'])->first();
                    $material = !empty($item['material_ulid']) 
                        ? Material::where('ulid', $item['material_ulid'])->first() 
                        : null;
                    $measurementHistory = !empty($item['measurement_history_ulid']) 
                        ? MeasurementHistory::where('ulid', $item['measurement_history_ulid'])->first() 
                        : null;

                    $orderDetailsData[] = [
                        'clothing_type_id' => $clothingType?->id,
                        'clothing_type_ulid' => $item['clothing_type_ulid'],
                        'material_id' => $material?->id,
                        'material_ulid' => $item['material_ulid'] ?? null,
                        'measurement_history_id' => $measurementHistory?->id,
                        'measurement_history_ulid' => $item['measurement_history_ulid'] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $item['price'] ?? 0,
                        'fabric_consumed_meter' => $item['fabric_consumed_meter'] ?? 0,
                        'notes' => $item['notes'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $order->orderDetails()->createMany($orderDetailsData);
            }

            if (isset($data['amount_paid'])) {
                $amountPaid = (float) $data['amount_paid'];
                if ($amountPaid > 0) {
                    $paymentStatus = $amountPaid >= $data['total'] ? 'full_payment' : 'down_payment';

                    Payment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'order_ulid' => $order->ulid,
                            'user_id' => $data['user_id'],
                            'user_ulid' => $data['user_ulid'],
                            'payment_date' => $data['payment_date'] ?? now(),
                            'amount_paid' => $amountPaid,
                            'payment_method' => $data['payment_method'] ?? 'cash',
                            'payment_status' => $paymentStatus,
                            'notes' => $data['notes'] ?? 'Pembayaran pesanan',
                        ]
                    );
                } else {
                    $order->payments()->delete();
                }
            }

            return $order->load('orderDetails');
        });
    }

    public function delete(string $ulid)
    {
        return DB::transaction(function () use ($ulid) {
            return $this->orderRepository->delete($ulid);
        });
    }
}