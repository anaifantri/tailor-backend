<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TailorAssignment extends Model
{
    protected $appends = ['hashed_id'];
    
    protected $fillable = [
        'order_detail_id',
        'tailor_id',
        'assignment_date',
        'quantity_assigned',
        'labor_cost',
        'total_labor_cost',
        'status',
        'notes'
    ];
    
    protected $hidden = ['id'];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
                $q->where('assignment_date', 'like', "%{$search}%")
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
        $query->whereYear('assignment_date', $year);

        if (!empty($month) && $month !== 'all' && (int)$month > 0) {
            $query->whereMonth('assignment_date', (int)$month);
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

    public function tailor(){
        return $this->belongsTo(Tailor::class);
    }
}
