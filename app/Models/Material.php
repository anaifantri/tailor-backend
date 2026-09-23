<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Material extends Model
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
        'name',
        'description',
        'unit',
        'initial_stock',
        'stock',
        'photo',
    ];

    protected $hidden = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'initial_stock' => 'decimal:2',
            'stock' => 'decimal:2',
        ];
    }

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

    public function getPhotoAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        return url(Storage::url($value));
    }

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'material_id', 'id');
    }
}