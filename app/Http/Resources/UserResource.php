<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// use Illuminate\Support\Facades\Crypt;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
        // return [
        //     'id' => Crypt::encryptString($this->id), 
        //     'name' => $this->name,
        //     'username' => $this->username,
        //     'email' => $this->email,
        //     'email_verified_at' => $this->email_verified_at,
        //     'phone' => $this->phone,
        //     'photo' => $this->photo,
        // ];
    }
}
