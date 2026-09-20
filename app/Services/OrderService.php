<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\OrderRepository;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAll(int $perPage = 10, int $month, int $year, ?string $search = null, array $fields)
    {
        return $this->orderRepository->getAll($perPage, $month, $year, $search, $fields);
    }

    public function getUnpaid(?string $search = null, array $fields)
    {
        return $this->orderRepository->getUnpaid($search, $fields);
    }

    public function getBySearch(?string $search = null, array $fields)
    {
        return $this->orderRepository->getBySearch($search, $fields);
    }

    public function getByMonthYearAndSearch(int $month, int $year, ?string $search = null, array $fields)
    {
        return $this->orderRepository->getOrdersByMonthYearAndSearch($month, $year, $search, $fields);
    }

    public function getByHashedId(string $hashedId, array $fields)
    {
        try {
            $decryptedId = Crypt::decryptString($hashedId);
            return $this->orderRepository->getById((int) $decryptedId, $fields ?? ['*']);
        } catch (DecryptException $e) {
            throw new \InvalidArgumentException("ID tidak valid.");
        }
    }

    public function create(array $data)
    {
        try {
            $customerId = (int) Crypt::decryptString($data['customer_id']);
            $userId   = (int) Crypt::decryptString($data['user_id']);
        } catch (Exception $e) {
            throw new Exception("Data identitas Pelanggan atau Pengguna tidak valid.");
        }

        return DB::transaction(function () use ($data, $customerId, $userId) { 
            //$lastOrder = $this->orderRepository->getLatestByNumber();
            //if(!$lastOrder){
              //  $number = 1;
            //}else{
              //  $number = (int) $lastOrder->number + 1;
            //}
            //$newOrderNumber = str_pad($number, 7, '0', STR_PAD_LEFT);

            //$data['number'] = $newOrderNumber;
            $data['customer_id'] = $customerId;
            $data['user_id'] = $userId;

            $order = $this->orderRepository->create($data);
            
            if (!empty($data['order_details'])) {
                $orderDetailsData = [];

                foreach ($data['order_details'] as $item) {
                    try {
                        $clothingTypeId = (int) Crypt::decryptString($item['clothing_type_id']);
                        $materialId = !empty($item['material_id']) 
                            ? (int) Crypt::decryptString($item['material_id']) 
                            : null;

                    } catch (Exception $e) {
                        throw new Exception("Data Jenis Layanan, Ukuran atau Bahan Kain tidak valid.");
                    }

                    $orderDetailsData[] = [
                        'clothing_type_id'            => $clothingTypeId,
                        'material_id'           => $materialId,
                        'quantity'              => $item['quantity'] ?? 1,
                        'price'                 => $item['price'] ?? 0,
                        'fabric_consumed_meter' => $item['fabric_consumed_meter'] ?? 0,
                        'notes'                 => $item['notes'] ?? null,
                        'measurements'          => $item['measurements'],
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                }
                $order->order_details()->createMany($orderDetailsData);
            }

            $amountPaid = (float) $data['amount_paid'] ?? 0;
            if ($amountPaid > 0) {
                $paymentStatus = 'down_payment';
                if ($amountPaid >= $data['total']) {
                    $paymentStatus = 'full_payment';
                }

                Payment::create([
                    'order_id'       => $order->id,
                    'user_id'       => $data['user_id'],
                    'payment_date'   => $data['payment_date'], 
                    'amount_paid'    => $amountPaid,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'payment_status'   => $paymentStatus,
                    'notes'          => $data['notes'] ?? 'Pembayaran awal saat pemesanan',
                ]);
            }
            return $order->load(['order_details', 'payments']);
        });
    }

    public function update(string $hashedId, array $data)
    {
        try {
            $orderId = (int) Crypt::decryptString($hashedId);
            $userId     = (int) Crypt::decryptString($data['user_id']);
        } catch (Exception $e) {
            throw new Exception("Data pesanan tidak valid.");
        }
        return DB::transaction(function () use ($orderId, $data, $userId) {
            $data['user_id'] = $userId;

            $order = $this->orderRepository->update($orderId, $data);
            
            if (!empty($data['order_details'])) {
                $order->order_details()->delete();
                $orderDetailsData = [];

                foreach ($data['order_details'] as $item) {
					$clothingTypeId = $item['clothing_type_id'];
					$measurementHistoryId = $item['measurement_history_id'];
					$materialId = $item['material_id'];
					
					if (!is_numeric($clothingTypeId)) {
						try {
								$clothingTypeId = (int) Crypt::decryptString($item['clothing_type_id']);
						} catch (Exception $e) {
							throw new Exception("Data Jenis Layanan tidak valid.");
						}
					}
					if (!is_numeric($measurementHistoryId)) {
						try {
							$measurementHistoryId = (int) Crypt::decryptString($item['measurement_history_id']);
						} catch (Exception $e) {
							throw new Exception("Data Ukuran tidak valid.");
						}
					}
					if (!is_numeric($materialId)) {
						try {
							$materialId = !empty($item['material_id']) 
								? (int) Crypt::decryptString($item['material_id']) 
								: null;
						} catch (Exception $e) {
							throw new Exception("Data Bahan Kain tidak valid.");
						}
					}

                    $orderDetailsData[] = [
                        'clothing_type_id'            => $clothingTypeId,
                        'material_id'           => $materialId,
                        'quantity'              => $item['qty'] ?? 1,
                        'price'                 => $item['price'] ?? 0,
                        'fabric_consumed_meter' => $item['fabric_consumed_meter'] ?? 0,
                        'measurement_history_id' => $measurementHistoryId,
                        'notes'                 => $item['notes'] ?? null,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ];
                }
                $order->order_details()->createMany($orderDetailsData);
            }

            $amountPaid = (float) $data['amount_paid'] ?? 0;
            if ($amountPaid > 0) {
                $paymentStatus = 'down_payment';
                if ($amountPaid >= $data['total']) {
                    $paymentStatus = 'full_payment';
                }

                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id'       => $data['user_id'],
                        'payment_date'   => now(), 
                        'amount_paid'    => $amountPaid,
                        'payment_method' => $data['payment_method'] ?? 'cash',
                        'payment_status'   => $paymentStatus,
                        'notes'          => $data['notes'] ?? 'Pembayaran awal saat pemesanan',
                    ]);
            } else {
                $order->payments()->delete();
            }
                
            return $order->load('order_details');
        });
    }

    public function delete(string $hashedId)
    {
        try {
            $orderId = (int) Crypt::decryptString($hashedId);
        } catch (Exception $e) {
            throw new Exception("Data pesanan tidak valid.");
        }
        return DB::transaction(function () use ($orderId) {
            return $this->orderRepository->delete($orderId);
        });
    }
}