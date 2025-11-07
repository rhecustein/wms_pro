<?php

use App\Http\Controllers\Installer\PurchaseCodeController;
use Illuminate\Support\Facades\Route;

Route::middleware('notinstalled')->group(function () {
    Route::get('/install/purchase', [PurchaseCodeController::class, 'show'])
        ->name('installer.purchase');
    Route::post('/install/purchase', [PurchaseCodeController::class, 'verify'])
        ->name('installer.purchase.verify');
});