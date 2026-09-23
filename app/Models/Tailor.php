<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Tailor extends Model
{
    use HasFactory, HasUlids;

    /**
     * Tentukan kolom ULID jika primary key di DB menggunakan id auto-increment.
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    protected $fillable = [
        'ulid',
        'code',
        'specialty',
        'name',
        'address',
        'phone',
        'email',
        'photo',
        'is_active',
    ];

    protected $hidden = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'specialty' => 'array',
            'is_active' => 'boolean',
        ];
    }

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
                ->orWhereJsonContains('specialty', $search);
        });
    }

    public function getPhotoAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        return url(Storage::url($value));
    }

    public function tailor_assignments(): HasMany
    {
        return $this->hasMany(TailorAssignment::class, 'tailor_id', 'id');
    }
}