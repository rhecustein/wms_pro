<?php

namespace App\Listeners\Operations;

use App\Events\ReplenishmentNeeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateReplenishmentTask
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
    public function handle(ReplenishmentNeeded $event): void
    {
        //
    }
}
