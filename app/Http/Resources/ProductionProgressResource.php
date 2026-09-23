<?php

namespace App\Http\Resources;

use App\Http\Resources\OrderDetailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'order_detail_ulid' => $this->order_detail_ulid,
            'status' => $this->status,
            'progress_date' => $this->progress_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'order_detail' => new OrderDetailResource($this->whenLoaded('orderDetail')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}