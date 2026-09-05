<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ClothingType extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'code',
        'type',
        'base_price'
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
            });
    }

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
