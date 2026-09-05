<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OrderDetail extends Model
{
    protected $appends = ['hashed_id'];
    protected $fillable = [
        'order_id',
        'clothing_type_id',
        'material_id',
        'quantity',
        'price',
        'fabric_consumed_meter',
        'notes',
    ];
    
    protected $hidden = ['id'];
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function clothing_type(){
        return $this->belongsTo(ClothingType::class);
    }

    public function material(){
        return $this->belongsTo(Material::class);
    }

    public function production_progress(){
        return $this->hasMany(ProductionProgress::class, 'order_detail_id', 'id');
    }
}
