<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'number' => $this->number,
            'order_date' => $this->order_date?->format('Y-m-d'),
            'fitting_date' => $this->fitting_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'notes' => $this->notes,
            'customer' => [
                'ulid' => $this->customer_ulid,
                'name' => $this->whenLoaded('customer', fn() => $this->customer->name),
                'phone' => $this->whenLoaded('customer', fn() => $this->customer->phone),
            ],
            'user' => [
                'ulid' => $this->user_ulid,
                'name' => $this->whenLoaded('user', fn() => $this->user->name),
            ],
            'order_details' => $this->whenLoaded('orderDetails', fn() => $this->orderDetails->map(function ($detail) {
                return [
                    'ulid' => $detail->ulid,
                    'clothing_type_ulid' => $detail->clothing_type_ulid,
                    'clothing_type_name' => $detail->clothingType?->name,
                    'material_ulid' => $detail->material_ulid,
                    'material_name' => $detail->material?->name,
                    'quantity' => $detail->quantity,
                    'price' => (float) $detail->price,
                    'fabric_consumed_meter' => (float) $detail->fabric_consumed_meter,
                    'measurements' => $detail->measurements,
                    'notes' => $detail->notes,
                ];
            })),
            'payments' => $this->whenLoaded('payments', fn() => $this->payments->map(function ($payment) {
                return [
                    'ulid' => $payment->ulid,
                    'payment_date' => $payment->payment_date?->format('Y-m-d'),
                    'amount_paid' => (float) $payment->amount_paid,
                    'payment_method' => $payment->payment_method,
                    'payment_status' => $payment->payment_status,
                ];
            })),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}