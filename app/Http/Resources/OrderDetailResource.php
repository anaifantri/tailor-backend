<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'order_ulid' => $this->order_ulid,
            'clothing_type_ulid' => $this->clothing_type_ulid,
            'clothing_type_name' => $this->whenLoaded('clothingType', fn() => $this->clothingType?->name),
            'material_ulid' => $this->material_ulid,
            'material_name' => $this->whenLoaded('material', fn() => $this->material?->name),
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'fabric_consumed_meter' => (float) $this->fabric_consumed_meter,
            'measurements' => $this->measurements,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}