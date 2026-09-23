<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeasurementHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'category' => $this->category,
            'measured_by' => $this->measured_by,
            'measured_at' => $this->measured_at ? $this->measured_at->format('Y-m-d') : null,
            'measurement_details' => $this->measurement_details,
            'notes' => $this->notes,
            'customer' => [
                'ulid' => $this->customer_ulid,
                'name' => $this->whenLoaded('customer', fn() => $this->customer?->name),
            ],
            'clothing_type' => [
                'ulid' => $this->clothing_type_ulid,
                'type' => $this->whenLoaded('clothing_type', fn() => $this->clothing_type?->type),
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}