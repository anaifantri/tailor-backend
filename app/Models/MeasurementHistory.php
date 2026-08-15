<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class MeasurementHistory extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'client_id',
        'clothing_type_id',
        'tailor_id',
        'measure_at',
        'measurement_details',
        'notes',
    ];
    
    protected $hidden = ['id'];

    public function clothing_type(){
        return $this->belongsTo(ClothingType::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }
}
