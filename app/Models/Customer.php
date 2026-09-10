<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Customer extends Model
{
    use HasFactory;
    
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function measurement_histories(){
        return $this->hasMany(MeasurementHistory::class, 'customer_id', 'id');
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function orders(){
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }
}
