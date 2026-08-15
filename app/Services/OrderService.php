<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\OrderRepository;
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

    public function getAll(array $fields)
    {
        return $this->orderRepository->getAll($fields);
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
        return DB::transaction(function () use ($data) { 
            $lastOrder = $this->orderRepository->getLatestByNumber();
            if(!$lastOrder){
                $number = 1;
            }else{
                $number = (int) $lastOrder->number + 1;
            }
            $newOrderNumber = str_pad($number, 6, '0', STR_PAD_LEFT);

            $data['number'] = $newOrderNumber;

            $order = $this->orderRepository->create($data);
            
            if (!empty($data['order_details'])) {
                $order->order_details()->createMany($data['order_details']);
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

    public function update(int $id, array $data)
    {
        $fields = ['id'];
        $order = $this->orderRepository->getById($id, $fields);

        $this->orderRepository->update($id, $data);
        
        if (!empty($data['order_details'])) {
            $order->order_details()->delete();

            $order->order_details()->createMany($data['order_details']);
         }
        return $order->load('order_details');
    }

    public function delete(string $hashedId)
    {
        $decryptedId = Crypt::decryptString($hashedId);
        return $this->orderRepository->delete((int) $decryptedId);
    }
}