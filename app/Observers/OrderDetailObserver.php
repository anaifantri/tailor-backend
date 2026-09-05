<?php

namespace App\Observers;

use App\Models\OrderDetail;
use App\Models\ProductionProgress;

class OrderDetailObserver
{
    /**
     * Handle the OrderDetail "created" event.
     */
    public function created(OrderDetail $orderDetail): void
    {
        ProductionProgress::create([
            'order_detail_id' => $orderDetail->id,
            'tailor_id'       => null,
            'status'          => 'Antrian',
            'notes'           => 'Antrean awal diisi otomatis oleh sistem.',
        ]);
    }

    /**
     * Handle the OrderDetail "updated" event.
     */
    public function updated(OrderDetail $orderDetail): void
    {
        //
    }

    /**
     * Handle the OrderDetail "deleted" event.
     */
    public function deleted(OrderDetail $orderDetail): void
    {
        //
    }

    /**
     * Handle the OrderDetail "restored" event.
     */
    public function restored(OrderDetail $orderDetail): void
    {
        //
    }

    /**
     * Handle the OrderDetail "force deleted" event.
     */
    public function forceDeleted(OrderDetail $orderDetail): void
    {
        //
    }
}
