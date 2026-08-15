<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Order extends Model
{
    protected $appends = ['hashed_id'];
    protected $fillable = [
        'number',
        'user_id',
        'client_id',
        'order_date',
        'fitting_date',
        'due_date',
        'tax',
        'total',
    ];
    
    protected $hidden = ['id'];
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function order_details(){
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }
}
