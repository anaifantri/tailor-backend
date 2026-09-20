<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $appends = ['hashed_id'];
    protected $fillable = [
        'number',
        'user_id',
        'customer_id',
        'order_date',
        'fitting_date',
        'due_date',
        'discount',
        'tax',
        'total',
        'notes',
    ];
    
    protected $hidden = ['id'];

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

    public function scopeByMonthYear(Builder $query, mixed $month, int $year): Builder
    {
        $query->whereYear('order_date', $year);

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('order_date', (int)$month);
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

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function order_details(){
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }
}
