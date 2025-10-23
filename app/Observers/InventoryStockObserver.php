<?php

namespace App\Observers;

use App\Models\InventoryStock;

class InventoryStockObserver
{
    /**
     * Handle the InventoryStock "created" event.
     */
    public function created(InventoryStock $inventoryStock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "updated" event.
     */
    public function updated(InventoryStock $inventoryStock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "deleted" event.
     */
    public function deleted(InventoryStock $inventoryStock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "restored" event.
     */
    public function restored(InventoryStock $inventoryStock): void
    {
        //
    }

    /**
     * Handle the InventoryStock "force deleted" event.
     */
    public function forceDeleted(InventoryStock $inventoryStock): void
    {
        //
    }
}
