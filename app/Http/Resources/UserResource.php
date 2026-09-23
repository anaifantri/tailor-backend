<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ulid'              => $this->ulid,
            'name'              => $this->name,
            'username'          => $this->username,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'photo'             => $this->photo,
            'is_active'         => $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}