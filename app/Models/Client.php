<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Client extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
    ];
    
    protected $hidden = ['id'];

    public function measurement_histories(){
        return $this->hasMany(MeasurementHistory::class, 'client_id', 'id');
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function orders(){
        return $this->hasMany(Order::class, 'client_id', 'id');
    }
}
