<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OrderDetailDelivery extends Model
{
    use HasFactory;

    protected $appends = ['hashed_id'];

    protected $fillable = [
        'order_detail_id',
        'user_id',
        'delivery_date',
        'quantity_delivered',
        'recipient_name',
        'recipient_relation',
        'notes',
    ];

    protected $hidden = ['id'];

    // Tambahkan casting agar delivery_date otomatis berformat Carbon
    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('delivery_date', 'like', "%{$search}%")
                ->orWhere('recipient_name', 'like', "%{$search}%") // Diubah dari where menjadi orWhere
                ->orWhere('recipient_relation', 'like', "%{$search}%")
                ->orWhereHas('order_detail', function ($orderDetailQuery) use ($search) {
                    $orderDetailQuery->whereHas('clothing_type', function ($clothingQuery) use ($search) {
                        $clothingQuery->where('type', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('number', 'like', "%{$search}%")
                            ->whereHas('customer', function ($customerQuery) use ($search) {
                                $customerQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                });
        });
    }

    public function scopeByMonthYear(Builder $query, mixed $month, int $year): Builder
    {
        // Diubah dari assignment_date menjadi delivery_date
        $query->whereYear('delivery_date', $year);

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('delivery_date', (int)$month);
        }

        return $query;
    }

    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}