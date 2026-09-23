<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ulid',
        'user_id',
        'user_ulid',
        'order_id',
        'order_ulid',
        'payment_date',
        'amount_paid',
        'payment_method',
        'payment_status',
        'notes',
    ];
    
    protected $hidden = [
        'id',
        'user_id',
        'order_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    /* -------------------------------------------------------------------------- */
    /*                                   SCOPES                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('payment_date', 'like', "%{$search}%")
              ->orWhere('payment_method', 'like', "%{$search}%")
              ->orWhere('payment_status', 'like', "%{$search}%")
              ->orWhereHas('order', function ($orderQuery) use ($search) {
                  $orderQuery->where('number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($customerQuery) use ($search) {
                          $customerQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                      });
              });
        });
    }

    public function scopeByMonthYear(Builder $query, mixed $month = null, mixed $year = null): Builder
    {
        if (!empty($year)) {
            $query->whereYear('payment_date', (int) $year);
        }

        if (!empty($month) && $month !== 'all' && (int) $month > 0) {
            $query->whereMonth('payment_date', (int) $month);
        }

        return $query;
    }

    /* -------------------------------------------------------------------------- */
    /*                                RELATIONSHIPS                               */
    /* -------------------------------------------------------------------------- */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}