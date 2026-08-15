<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ClothingType extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'type',
        'base_price'
    ];
    
    protected $hidden = ['id'];

    public function measurements(){
        return $this->hasMany(MeasurementDetail::class, 'clothing_type_id', 'id');
    }

    public function measurement_histories(){
        return $this->hasMany(MeasurementHistory::class, 'clothing_type_id', 'id');
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function order_details(){
        return $this->hasMany(OrderDetail::class, 'clothing_type_id', 'id');
    }
}
