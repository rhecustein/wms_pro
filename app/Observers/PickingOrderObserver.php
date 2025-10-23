<?php

namespace App\Observers;

use App\Models\PickingOrder;

class PickingOrderObserver
{
    /**
     * Handle the PickingOrder "created" event.
     */
    public function created(PickingOrder $pickingOrder): void
    {
        //
    }

    /**
     * Handle the PickingOrder "updated" event.
     */
    public function updated(PickingOrder $pickingOrder): void
    {
        //
    }

    /**
     * Handle the PickingOrder "deleted" event.
     */
    public function deleted(PickingOrder $pickingOrder): void
    {
        //
    }

    /**
     * Handle the PickingOrder "restored" event.
     */
    public function restored(PickingOrder $pickingOrder): void
    {
        //
    }

    /**
     * Handle the PickingOrder "force deleted" event.
     */
    public function forceDeleted(PickingOrder $pickingOrder): void
    {
        //
    }
}
