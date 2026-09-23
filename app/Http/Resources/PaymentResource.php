<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid'           => $this->ulid,
            'user_ulid'      => $this->user_ulid,
            'order_ulid'     => $this->order_ulid,
            'payment_date'   => $this->payment_date?->format('Y-m-d'),
            'amount_paid'    => (float) $this->amount_paid,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'notes'          => $this->notes,
            'user'           => $this->whenLoaded('user', function () {
                return [
                    'ulid'  => $this->user->ulid,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'order'          => $this->whenLoaded('order', function () {
                return [
                    'ulid'   => $this->order->ulid,
                    'number' => $this->order->number ?? null,
                ];
            }),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}