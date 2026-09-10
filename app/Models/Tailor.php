<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Tailor extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'code',
        'specialty',
        'name',
        'address',
        'phone',
        'email',
        'photo',
        'is_active',
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereJsonContains('specialty', 'like', "%{$search}%");
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

    public function tailor_assignments(){
        return $this->hasMany(TailorAssignment::class, 'tailor_id', 'id');
    }
}
