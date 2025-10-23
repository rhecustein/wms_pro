<?php

namespace App\Listeners\Inbound;

use App\Events\GoodReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GeneratePutawayTask
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
    public function handle(GoodReceived $event): void
    {
        //
    }
}
