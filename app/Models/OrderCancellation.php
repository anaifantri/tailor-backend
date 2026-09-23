<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class OrderCancellation extends Model
{
    use HasFactory;

    protected $appends = ['hashed_id'];
    protected $fillable = [
        'order_id',
        'order_detail_id',
        'user_id',
        'cancellation_type',
        'refund_amount',
        'reason',
        'cancelled_at',
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('cancelled_at', 'like', "%{$search}%")
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
        $query->whereYear('cancelled_at', $year);

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('cancelled_at', (int)$month);
        }

        return $query;
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function order_detail(){
        return $this->belongsTo(OrderDetail::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
