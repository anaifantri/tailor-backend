<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionProgress extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'production_progress';

    protected $fillable = [
        'ulid',
        'order_detail_id',
        'order_detail_ulid',
        'progress_date',
        'status',
        'notes',
    ];

    protected $hidden = [
        'id',
        'order_detail_id',
    ];

    protected $casts = [
        'progress_date' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }
}