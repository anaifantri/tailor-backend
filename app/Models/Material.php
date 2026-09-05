<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'code',
        'name',
        'description',
        'unit',
        'photo',
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
            });
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function getPhotoAttribute($value){
        if(!$value){
            return null;
        }
        return url(Storage::url($value));
    }

    public function order_details(){
        return $this->hasMany(OrderDetail::class, 'material_id', 'id');
    }
}
