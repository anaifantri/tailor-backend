<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MeasurementHistory extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'customer_id',
        'clothing_type_id',
        'measured_by',
        'measured_at',
        'measurement_details',
        'notes',
    ];
    
    protected $hidden = ['id'];

    public function clothing_type(){
        return $this->belongsTo(ClothingType::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }
}
