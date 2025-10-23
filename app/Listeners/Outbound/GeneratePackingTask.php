<?php

namespace App\Listeners\Outbound;

use App\Events\OrderPicked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GeneratePackingTask
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
    public function handle(OrderPicked $event): void
    {
        //
    }
}
