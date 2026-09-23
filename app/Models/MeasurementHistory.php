<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementHistory extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'ulid',
        'customer_id',
        'customer_ulid',
        'clothing_type_id',
        'clothing_type_ulid',
        'category',
        'measured_by',
        'measured_at',
        'measurement_details',
        'notes',
    ];

    protected $hidden = [
        'id',
        'customer_id',
        'clothing_type_id',
    ];

    protected $casts = [
        'measurement_details' => 'array',
        'measured_at' => 'date',
    ];

    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function customer()
    {
        // Parameter 2: foreign_key di measurement_histories
        // Parameter 3: owner_key di tabel customers
        return $this->belongsTo(Customer::class, 'customer_ulid', 'ulid');
    }

    public function clothing_type()
    {
        // Parameter 2: foreign_key di measurement_histories
        // Parameter 3: owner_key di tabel clothing_types
        return $this->belongsTo(ClothingType::class, 'clothing_type_ulid', 'ulid');
    }
}