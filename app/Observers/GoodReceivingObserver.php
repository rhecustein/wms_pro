<?php

namespace App\Observers;

use App\Models\GoodReceiving;

class GoodReceivingObserver
{
    /**
     * Handle the GoodReceiving "created" event.
     */
    public function created(GoodReceiving $goodReceiving): void
    {
        //
    }

    /**
     * Handle the GoodReceiving "updated" event.
     */
    public function updated(GoodReceiving $goodReceiving): void
    {
        //
    }

    /**
     * Handle the GoodReceiving "deleted" event.
     */
    public function deleted(GoodReceiving $goodReceiving): void
    {
        //
    }

    /**
     * Handle the GoodReceiving "restored" event.
     */
    public function restored(GoodReceiving $goodReceiving): void
    {
        //
    }

    /**
     * Handle the GoodReceiving "force deleted" event.
     */
    public function forceDeleted(GoodReceiving $goodReceiving): void
    {
        //
    }
}
