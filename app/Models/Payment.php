<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Payment extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'user_id',
        'order_id',
        'payment_date',
        'amount_paid',
        'payment_method',
        'payment_status',
        'notes',
    ];
    
    protected $hidden = ['id'];

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

    public function scopeByMonthYear(Builder $query, mixed $month, int $year): Builder
    {
        $query->whereYear('payment_date', $year);

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('payment_date', (int)$month);
        }

        return $query;
    }
    
    protected function hashedId(): Attribute
    {
        return Attribute::make(
            get: fn () => Crypt::encryptString($this->attributes['id'] ?? ''),
        );
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
