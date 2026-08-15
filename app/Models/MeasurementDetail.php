<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MeasurementDetail extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'clothing_type_id',
        'measurement',
    ];
    
    protected $hidden = ['id'];

    public function clothing_type(){
        return $this->belongsTo(ClothingType::class);
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }
}
