<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailDeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->hashed_id,
            'order_detail_id'    => $this->order_detail?->hashed_id ?? $this->order_detail_id,
            'user_id'            => $this->user?->hashed_id ?? $this->user_id,
            'quantity_delivered' => $this->quantity_delivered,
            'delivery_date'      => $this->delivery_date?->toISOString(),
            'recipient_name'     => $this->recipient_name,
            'recipient_relation' => $this->recipient_relation,
            'notes'              => $this->notes,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),

            // Data Relasi (Eager Loaded)
            'order_detail' => $this->whenLoaded('order_detail'),
            'user'         => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->hashed_id ?? $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}