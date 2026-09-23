<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClothingType extends Model
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
        'type',
        'category',
        'base_price',
    ];

    protected $hidden = [
        'id',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
        ];
    }

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

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'clothing_type_id', 'id');
    }

    public function measurement_histories(): HasMany
    {
        return $this->hasMany(MeasurementHistory::class, 'clothing_type_ulid', 'ulid');
    }
}