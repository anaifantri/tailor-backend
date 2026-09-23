<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ulid',
        'order_id',
        'order_ulid',
        'clothing_type_id',
        'clothing_type_ulid',
        'material_id',
        'material_ulid',
        'quantity',
        'price',
        'fabric_consumed_meter',
        'measurements',
        'notes',
    ];
    
    protected $hidden = [
        'id',
        'order_id',
        'clothing_type_id',
        'material_id',
    ];

    protected $casts = [
        'measurements' => 'array',
        'price' => 'decimal:2',
        'fabric_consumed_meter' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /* -------------------------------------------------------------------------- */
    /*                                RELATIONSHIPS                               */
    /* -------------------------------------------------------------------------- */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function clothingType(): BelongsTo
    {
        return $this->belongsTo(ClothingType::class, 'clothing_type_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function productionProgress(): HasMany
    {
        return $this->hasMany(ProductionProgress::class, 'order_detail_id', 'id');
    }

    public function tailorAssignments(): HasMany
    {
        return $this->hasMany(TailorAssignment::class, 'order_detail_id', 'id');
    }

    public function orderDetailDeliveries(): HasMany
    {
        return $this->hasMany(OrderDetailDelivery::class, 'order_detail_id', 'id');
    }

    public function orderCancellations(): HasMany
    {
        return $this->hasMany(OrderCancellation::class, 'order_detail_id', 'id');
    }
}