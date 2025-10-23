<?php

namespace App\Listeners\Inventory;

use App\Events\StockUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStockAvailability
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StockUpdated $event): void
    {
        //
    }
}
