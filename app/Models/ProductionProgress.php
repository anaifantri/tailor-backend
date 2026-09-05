<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ProductionProgress extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'order_detail_id',
        'tailor_id',
        'status',
        'notes',
    ];
    
    protected $hidden = ['id'];
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function order_detail(){
        return $this->belongsTo(OrderDetail::class);
    }

    public function tailor(){
        return $this->belongsTo(Tailor::class);
    }
}
