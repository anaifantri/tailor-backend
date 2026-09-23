<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ulid',
        'number',
        'user_id',
        'user_ulid',
        'customer_id',
        'customer_ulid',
        'order_date',
        'fitting_date',
        'due_date',
        'discount',
        'tax',
        'total',
        'notes',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'customer_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'fitting_date' => 'date',
        'due_date' => 'date',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereDoesntHave('payments')
              ->orWhere('total', '>', function ($subQuery) {
                  $subQuery->select(DB::raw('COALESCE(SUM(amount_paid), 0)'))
                           ->from('payments')
                           ->whereColumn('payments.order_id', 'orders.id');
              });
        });
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('number', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($customerQuery) use ($search) {
                  $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
              });
        });
    }

    public function scopeByMonthYear(Builder $query, mixed $month, ?int $year = null): Builder
    {
        if ($year) {
            $query->whereYear('order_date', $year);
        }

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('order_date', (int)$month);
        }

        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    public function orderCancellations()
    {
        return $this->hasMany(OrderCancellation::class, 'order_id', 'id');
    }
}