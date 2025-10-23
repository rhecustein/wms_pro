<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ============================================
// MASTER DATA CONTROLLERS
// ============================================
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\WarehouseController;
use App\Http\Controllers\Master\StorageAreaController;
use App\Http\Controllers\Master\StorageBinController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Master\ProductCategoryController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\VendorController;

// ============================================
// INVENTORY CONTROLLERS
// ============================================
use App\Http\Controllers\Inventory\InventoryStockController;
use App\Http\Controllers\Inventory\PalletController;
use App\Http\Controllers\Inventory\StockMovementController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\StockOpnameController;

// ============================================
// INBOUND CONTROLLERS
// ============================================
use App\Http\Controllers\Inbound\PurchaseOrderController;
use App\Http\Controllers\Inbound\InboundShipmentController;
use App\Http\Controllers\Inbound\GoodReceivingController;
use App\Http\Controllers\Inbound\PutawayTaskController;

// ============================================
// OUTBOUND CONTROLLERS
// ============================================
use App\Http\Controllers\Outbound\SalesOrderController;
use App\Http\Controllers\Outbound\PickingOrderController;
use App\Http\Controllers\Outbound\PackingOrderController;
use App\Http\Controllers\Outbound\DeliveryOrderController;
use App\Http\Controllers\Outbound\ReturnOrderController;

// ============================================
// OPERATIONS CONTROLLERS
// ============================================
use App\Http\Controllers\Operations\ReplenishmentTaskController;
use App\Http\Controllers\Operations\TransferOrderController;
use App\Http\Controllers\Operations\CrossDockingOrderController;

// ============================================
// EQUIPMENT CONTROLLERS
// ============================================
use App\Http\Controllers\Equipment\VehicleController;
use App\Http\Controllers\Equipment\EquipmentController;

// ============================================
// REPORTING CONTROLLERS
// ============================================
use App\Http\Controllers\Reports\DashboardController;
use App\Http\Controllers\Reports\InventoryReportController;
use App\Http\Controllers\Reports\InboundReportController;
use App\Http\Controllers\Reports\OutboundReportController;
use App\Http\Controllers\Reports\OperationsReportController;
use App\Http\Controllers\Reports\KpiReportController;

// ============================================
// SYSTEM CONTROLLERS
// ============================================
use App\Http\Controllers\System\SettingController;
use App\Http\Controllers\System\ActivityLogController;
use App\Http\Controllers\System\NotificationController;

// ============================================
// MOBILE CONTROLLERS
// ============================================
use App\Http\Controllers\Mobile\GoodReceivingMobileController;
use App\Http\Controllers\Mobile\PutawayMobileController;
use App\Http\Controllers\Mobile\PickingMobileController;
use App\Http\Controllers\Mobile\StockCountMobileController;
use App\Http\Controllers\Mobile\TransferMobileController;
use App\Http\Controllers\Mobile\PackingMobileController;

// ============================================
// PUBLIC ROUTES
// ============================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // ============================================
    // DASHBOARD
    // ============================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ============================================
    // PROFILE MANAGEMENT
    // ============================================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // MASTER DATA ROUTES
    // ============================================
    Route::prefix('master')->name('master.')->group(function () {
        
        // Users Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Roles Management
        Route::resource('roles', RoleController::class);
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
        
        // Warehouses Management
        Route::resource('warehouses', WarehouseController::class);
        Route::post('warehouses/{warehouse}/activate', [WarehouseController::class, 'activate'])->name('warehouses.activate');
        Route::post('warehouses/{warehouse}/deactivate', [WarehouseController::class, 'deactivate'])->name('warehouses.deactivate');
        Route::get('warehouses/{warehouse}/layout', [WarehouseController::class, 'layout'])->name('warehouses.layout');
        
        // Storage Areas Management
        Route::resource('storage-areas', StorageAreaController::class);
        Route::get('warehouses/{warehouse}/storage-areas', [StorageAreaController::class, 'byWarehouse'])->name('storage-areas.by-warehouse');
        
        // Storage Bins Management
        Route::resource('storage-bins', StorageBinController::class);
        Route::get('storage-areas/{storageArea}/bins', [StorageBinController::class, 'byStorageArea'])->name('storage-bins.by-area');
        Route::post('storage-bins/generate', [StorageBinController::class, 'generate'])->name('storage-bins.generate');
        Route::post('storage-bins/{storageBin}/activate', [StorageBinController::class, 'activate'])->name('storage-bins.activate');
        Route::post('storage-bins/{storageBin}/deactivate', [StorageBinController::class, 'deactivate'])->name('storage-bins.deactivate');
        Route::get('storage-bins/{storageBin}/current-stock', [StorageBinController::class, 'currentStock'])->name('storage-bins.current-stock');
        
        // Product Categories Management
        Route::resource('product-categories', ProductCategoryController::class);
        Route::get('product-categories/tree', [ProductCategoryController::class, 'tree'])->name('product-categories.tree');
        
        // Products Management
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/activate', [ProductController::class, 'activate'])->name('products.activate');
        Route::post('products/{product}/deactivate', [ProductController::class, 'deactivate'])->name('products.deactivate');
        Route::get('products/{product}/stock-summary', [ProductController::class, 'stockSummary'])->name('products.stock-summary');
        Route::get('products/{product}/movements', [ProductController::class, 'movements'])->name('products.movements');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
        
        // Customers Management
        Route::resource('customers', CustomerController::class);
        Route::post('customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');
        Route::post('customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])->name('customers.deactivate');
        Route::get('customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
        Route::get('customers/{customer}/stock', [CustomerController::class, 'stock'])->name('customers.stock');
        
        // Vendors Management
        Route::resource('vendors', VendorController::class);
        Route::post('vendors/{vendor}/activate', [VendorController::class, 'activate'])->name('vendors.activate');
        Route::post('vendors/{vendor}/deactivate', [VendorController::class, 'deactivate'])->name('vendors.deactivate');
        Route::get('vendors/{vendor}/purchase-orders', [VendorController::class, 'purchaseOrders'])->name('vendors.purchase-orders');
    });

    // ============================================
    // INVENTORY ROUTES
    // ============================================
    Route::prefix('inventory')->name('inventory.')->group(function () {
        
        // Inventory Stocks
        Route::prefix('stocks')->name('stocks.')->group(function () {
            // Routes dengan keyword harus di atas route dengan parameter
            Route::get('/', [InventoryStockController::class, 'index'])->name('index');
            Route::get('expiring', [InventoryStockController::class, 'expiring'])->name('expiring');
            Route::get('expired', [InventoryStockController::class, 'expired'])->name('expired');
            Route::get('low-stock', [InventoryStockController::class, 'lowStock'])->name('low-stock');
            
            // Routes dengan parameter product, warehouse, bin
            Route::get('product/{product}', [InventoryStockController::class, 'byProduct'])->name('by-product');
            Route::get('product/{product}/card', [InventoryStockController::class, 'stockCard'])->name('card');
            Route::get('warehouse/{warehouse}', [InventoryStockController::class, 'byWarehouse'])->name('by-warehouse');
            Route::get('bin/{storageBin}', [InventoryStockController::class, 'byBin'])->name('by-bin');
            
            // Route detail stock - harus paling bawah
            Route::get('{inventoryStock}', [InventoryStockController::class, 'show'])->name('show');
        });
        // Pallets Management
        Route::resource('pallets', PalletController::class);
        Route::post('pallets/{pallet}/activate', [PalletController::class, 'activate'])->name('pallets.activate');
        Route::post('pallets/{pallet}/deactivate', [PalletController::class, 'deactivate'])->name('pallets.deactivate');
        Route::get('pallets/{pallet}/history', [PalletController::class, 'history'])->name('pallets.history');
        Route::post('pallets/generate', [PalletController::class, 'generate'])->name('pallets.generate');
        
        // Stock Movements
        Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
        Route::get('movements/{stockMovement}', [StockMovementController::class, 'show'])->name('movements.show');
        Route::get('movements/product/{product}', [StockMovementController::class, 'byProduct'])->name('movements.by-product');
        Route::get('movements/warehouse/{warehouse}', [StockMovementController::class, 'byWarehouse'])->name('movements.by-warehouse');
        Route::get('movements/export', [StockMovementController::class, 'export'])->name('movements.export');
        
        // Stock Adjustments
        Route::resource('adjustments', StockAdjustmentController::class);
        Route::post('adjustments/{stockAdjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');
        Route::post('adjustments/{stockAdjustment}/post', [StockAdjustmentController::class, 'post'])->name('adjustments.post');
        Route::post('adjustments/{stockAdjustment}/cancel', [StockAdjustmentController::class, 'cancel'])->name('adjustments.cancel');
        
        // Stock Opnames
        Route::resource('opnames', StockOpnameController::class);
        Route::post('opnames/{opname}/start', [StockOpnameController::class, 'start'])->name('opnames.start');
        Route::post('opnames/{opname}/complete', [StockOpnameController::class, 'complete'])->name('opnames.complete');
        Route::post('opnames/{opname}/cancel', [StockOpnameController::class, 'cancel'])->name('opnames.cancel');
        Route::get('opnames/{opname}/count', [StockOpnameController::class, 'count'])->name('opnames.count');
        Route::post('opnames/{opname}/items/{item}/update-count', [StockOpnameController::class, 'updateCount'])->name('opnames.update-count');
        Route::get('warehouses/{warehouse}/storage-areas', [StockOpnameController::class, 'getStorageAreas'])->name('warehouses.storage-areas');
        Route::get('warehouses/{warehouse}/storage-bins', [StockOpnameController::class, 'getStorageBins'])->name('warehouses.storage-bins');
    });

    // ============================================
    // INBOUND ROUTES
    // ============================================
    Route::prefix('inbound')->name('inbound.')->group(function () {
        
        // Purchase Orders
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('purchase-orders/{purchaseOrder}/submit', [PurchaseOrderController::class, 'submit'])->name('purchase-orders.submit');
        Route::post('purchase-orders/{purchaseOrder}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');
        Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
        Route::get('purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
        
        // Inbound Shipments
        Route::resource('shipments', InboundShipmentController::class);
        Route::post('shipments/{inboundShipment}/mark-arrived', [InboundShipmentController::class, 'markArrived'])->name('shipments.mark-arrived');
        Route::post('shipments/{inboundShipment}/start-unloading', [InboundShipmentController::class, 'startUnloading'])->name('shipments.start-unloading');
        Route::post('shipments/{inboundShipment}/complete', [InboundShipmentController::class, 'complete'])->name('shipments.complete');
        
        // Good Receiving
        Route::resource('good-receivings', GoodReceivingController::class);
        Route::post('good-receivings/{goodReceiving}/start', [GoodReceivingController::class, 'start'])->name('good-receivings.start');
        Route::post('good-receivings/{goodReceiving}/quality-check', [GoodReceivingController::class, 'qualityCheck'])->name('good-receivings.quality-check');
        Route::post('good-receivings/{goodReceiving}/complete', [GoodReceivingController::class, 'complete'])->name('good-receivings.complete');
        Route::post('good-receivings/{goodReceiving}/cancel', [GoodReceivingController::class, 'cancel'])->name('good-receivings.cancel');
        Route::get('good-receivings/{goodReceiving}/print', [GoodReceivingController::class, 'print'])->name('good-receivings.print');
        
        Route::get('putaway-tasks/pending', [PutawayTaskController::class, 'pending'])->name('putaway-tasks.pending');
        Route::get('putaway-tasks/{putawayTask}/execute', [PutawayTaskController::class, 'execute'])->name('putaway-tasks.execute');
        Route::post('putaway-tasks/{putawayTask}/assign', [PutawayTaskController::class, 'assign'])->name('putaway-tasks.assign');
        Route::post('putaway-tasks/{putawayTask}/start', [PutawayTaskController::class, 'start'])->name('putaway-tasks.start');
        Route::post('putaway-tasks/{putawayTask}/complete', [PutawayTaskController::class, 'complete'])->name('putaway-tasks.complete');
        Route::post('putaway-tasks/{putawayTask}/cancel', [PutawayTaskController::class, 'cancel'])->name('putaway-tasks.cancel');
        
        // Resource routes
        Route::resource('putaway-tasks', PutawayTaskController::class);
    });

    // ============================================
    // OUTBOUND ROUTES
    // ============================================
    Route::prefix('outbound')->name('outbound.')->group(function () {
        
        // Sales Orders
        Route::resource('sales-orders', SalesOrderController::class);
        Route::post('sales-orders/{salesOrder}/confirm', [SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');
        Route::post('sales-orders/{salesOrder}/cancel', [SalesOrderController::class, 'cancel'])->name('sales-orders.cancel');
        Route::post('sales-orders/{salesOrder}/generate-picking', [SalesOrderController::class, 'generatePicking'])->name('sales-orders.generate-picking');
        Route::get('sales-orders/{salesOrder}/print', [SalesOrderController::class, 'print'])->name('sales-orders.print');

        // Wave Picking - Harus di atas resource route
        Route::get('picking-orders/wave', [PickingOrderController::class, 'wave'])->name('picking-orders.wave');
        Route::post('picking-orders/batch-generate', [PickingOrderController::class, 'batchGenerate'])->name('picking-orders.batch-generate');
        
        // Other routes
        Route::get('picking-orders/pending', [PickingOrderController::class, 'pending'])->name('picking-orders.pending');
        Route::get('picking-orders/{pickingOrder}/execute', [PickingOrderController::class, 'execute'])->name('picking-orders.execute');
        Route::get('picking-orders/{pickingOrder}/print', [PickingOrderController::class, 'print'])->name('picking-orders.print');
        Route::post('picking-orders/{pickingOrder}/assign', [PickingOrderController::class, 'assign'])->name('picking-orders.assign');
        Route::post('picking-orders/{pickingOrder}/start', [PickingOrderController::class, 'start'])->name('picking-orders.start');
        Route::post('picking-orders/{pickingOrder}/complete', [PickingOrderController::class, 'complete'])->name('picking-orders.complete');
        Route::post('picking-orders/{pickingOrder}/cancel', [PickingOrderController::class, 'cancel'])->name('picking-orders.cancel');
        
        // Resource route terakhir
        Route::resource('picking-orders', PickingOrderController::class);
        
        // Packing Orders
        Route::resource('packing-orders', PackingOrderController::class);
        Route::get('packing-orders/pending', [PackingOrderController::class, 'pending'])->name('packing-orders.pending');
        Route::post('packing-orders/{packingOrder}/assign', [PackingOrderController::class, 'assign'])->name('packing-orders.assign');
        Route::post('packing-orders/{packingOrder}/start', [PackingOrderController::class, 'start'])->name('packing-orders.start');
        Route::post('packing-orders/{packingOrder}/complete', [PackingOrderController::class, 'complete'])->name('packing-orders.complete');
        Route::get('packing-orders/{packingOrder}/execute', [PackingOrderController::class, 'execute'])->name('packing-orders.execute');
        Route::get('packing-orders/{packingOrder}/print-label', [PackingOrderController::class, 'printLabel'])->name('packing-orders.print-label');
        
        // Delivery Orders
        Route::resource('delivery-orders', DeliveryOrderController::class);
        Route::post('delivery-orders/{deliveryOrder}/load', [DeliveryOrderController::class, 'load'])->name('delivery-orders.load');
        Route::post('delivery-orders/{deliveryOrder}/dispatch', [DeliveryOrderController::class, 'dispatch'])->name('delivery-orders.dispatch');
        Route::post('delivery-orders/{deliveryOrder}/in-transit', [DeliveryOrderController::class, 'inTransit'])->name('delivery-orders.in-transit');
        Route::post('delivery-orders/{deliveryOrder}/deliver', [DeliveryOrderController::class, 'deliver'])->name('delivery-orders.deliver');
        Route::post('delivery-orders/{deliveryOrder}/cancel', [DeliveryOrderController::class, 'cancel'])->name('delivery-orders.cancel');
        Route::get('delivery-orders/{deliveryOrder}/proof', [DeliveryOrderController::class, 'proof'])->name('delivery-orders.proof');
        Route::post('delivery-orders/{deliveryOrder}/upload-proof', [DeliveryOrderController::class, 'uploadProof'])->name('delivery-orders.upload-proof');
        Route::get('delivery-orders/{deliveryOrder}/print', [DeliveryOrderController::class, 'print'])->name('delivery-orders.print');
        Route::get('delivery-orders/{deliveryOrder}/tracking', [DeliveryOrderController::class, 'tracking'])->name('delivery-orders.tracking');
        
        // Return Orders
        Route::resource('returns', ReturnOrderController::class);
        Route::post('returns/{returnOrder}/receive', [ReturnOrderController::class, 'receive'])->name('returns.receive');
        Route::post('returns/{returnOrder}/inspect', [ReturnOrderController::class, 'inspect'])->name('returns.inspect');
        Route::post('returns/{returnOrder}/restock', [ReturnOrderController::class, 'restock'])->name('returns.restock');
        Route::post('returns/{returnOrder}/cancel', [ReturnOrderController::class, 'cancel'])->name('returns.cancel');
        Route::get('returns/{returnOrder}/print', [ReturnOrderController::class, 'print'])->name('returns.print');
    });

    // ============================================
    // OPERATIONS ROUTES
    // ============================================
    Route::prefix('operations')->name('operations.')->group(function () {
        
        // Replenishment Tasks
        Route::resource('replenishments', ReplenishmentTaskController::class);
        Route::get('replenishments/suggestions', [ReplenishmentTaskController::class, 'suggestions'])->name('replenishments.suggestions');
        Route::post('replenishments/generate-suggestions', [ReplenishmentTaskController::class, 'generateSuggestions'])->name('replenishments.generate-suggestions');
        Route::post('replenishments/{replenishmentTask}/assign', [ReplenishmentTaskController::class, 'assign'])->name('replenishments.assign');
        Route::post('replenishments/{replenishmentTask}/start', [ReplenishmentTaskController::class, 'start'])->name('replenishments.start');
        Route::post('replenishments/{replenishmentTask}/complete', [ReplenishmentTaskController::class, 'complete'])->name('replenishments.complete');
        Route::post('replenishments/{replenishmentTask}/cancel', [ReplenishmentTaskController::class, 'cancel'])->name('replenishments.cancel');
        Route::get('replenishments/{replenishmentTask}/execute', [ReplenishmentTaskController::class, 'execute'])->name('replenishments.execute');
        
        // Transfer Orders
        Route::resource('transfers', TransferOrderController::class);
        Route::post('transfers/{transferOrder}/approve', [TransferOrderController::class, 'approve'])->name('transfers.approve');
        Route::post('transfers/{transferOrder}/ship', [TransferOrderController::class, 'ship'])->name('transfers.ship');
        Route::post('transfers/{transferOrder}/receive', [TransferOrderController::class, 'receive'])->name('transfers.receive');
        Route::post('transfers/{transferOrder}/complete', [TransferOrderController::class, 'complete'])->name('transfers.complete');
        Route::post('transfers/{transferOrder}/cancel', [TransferOrderController::class, 'cancel'])->name('transfers.cancel');
        Route::get('transfers/{transferOrder}/print', [TransferOrderController::class, 'print'])->name('transfers.print');
        
        // Cross Docking
        Route::resource('cross-docking', CrossDockingOrderController::class);
        Route::post('cross-docking/{crossDockingOrder}/start-receiving', [CrossDockingOrderController::class, 'startReceiving'])->name('cross-docking.start-receiving');
        Route::post('cross-docking/{crossDockingOrder}/start-sorting', [CrossDockingOrderController::class, 'startSorting'])->name('cross-docking.start-sorting');
        Route::post('cross-docking/{crossDockingOrder}/start-loading', [CrossDockingOrderController::class, 'startLoading'])->name('cross-docking.start-loading');
        Route::post('cross-docking/{crossDockingOrder}/complete', [CrossDockingOrderController::class, 'complete'])->name('cross-docking.complete');
        Route::post('cross-docking/{crossDockingOrder}/cancel', [CrossDockingOrderController::class, 'cancel'])->name('cross-docking.cancel');
    });

    // ============================================
    // EQUIPMENT ROUTES
    // ============================================
    Route::prefix('equipment')->name('equipment.')->group(function () {
        
        // Vehicles Management
        Route::resource('vehicles', VehicleController::class);
        Route::post('vehicles/{vehicle}/activate', [VehicleController::class, 'activate'])->name('vehicles.activate');
        Route::post('vehicles/{vehicle}/deactivate', [VehicleController::class, 'deactivate'])->name('vehicles.deactivate');
        Route::post('vehicles/{vehicle}/maintenance', [VehicleController::class, 'maintenance'])->name('vehicles.maintenance');
        Route::get('vehicles/{vehicle}/history', [VehicleController::class, 'history'])->name('vehicles.history');
        
        // Equipment Management
        Route::resource('equipments', EquipmentController::class);
        Route::post('equipments/{equipment}/activate', [EquipmentController::class, 'activate'])->name('equipments.activate');
        Route::post('equipments/{equipment}/deactivate', [EquipmentController::class, 'deactivate'])->name('equipments.deactivate');
        Route::post('equipments/{equipment}/maintenance', [EquipmentController::class, 'maintenance'])->name('equipments.maintenance');
        Route::get('equipments/{equipment}/history', [EquipmentController::class, 'history'])->name('equipments.history');
    });

    // ============================================
    // REPORTS ROUTES
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        
        // Inventory Reports
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('stock-summary', [InventoryReportController::class, 'stockSummary'])->name('stock-summary');
            Route::get('stock-movements', [InventoryReportController::class, 'stockMovements'])->name('stock-movements');
            Route::get('aging-report', [InventoryReportController::class, 'agingReport'])->name('aging-report');
            Route::get('expiry-report', [InventoryReportController::class, 'expiryReport'])->name('expiry-report');
            Route::get('low-stock-report', [InventoryReportController::class, 'lowStockReport'])->name('low-stock-report');
            Route::get('dead-stock-report', [InventoryReportController::class, 'deadStockReport'])->name('dead-stock-report');
            Route::get('stock-valuation', [InventoryReportController::class, 'stockValuation'])->name('stock-valuation');
        });
        
        // Inbound Reports
        Route::prefix('inbound')->name('inbound.')->group(function () {
            Route::get('receiving-report', [InboundReportController::class, 'receivingReport'])->name('receiving-report');
            Route::get('putaway-report', [InboundReportController::class, 'putawayReport'])->name('putaway-report');
            Route::get('vendor-performance', [InboundReportController::class, 'vendorPerformance'])->name('vendor-performance');
            Route::get('receiving-accuracy', [InboundReportController::class, 'receivingAccuracy'])->name('receiving-accuracy');
        });
        
        // Outbound Reports
        Route::prefix('outbound')->name('outbound.')->group(function () {
            Route::get('picking-report', [OutboundReportController::class, 'pickingReport'])->name('picking-report');
            Route::get('shipping-report', [OutboundReportController::class, 'shippingReport'])->name('shipping-report');
            Route::get('customer-orders', [OutboundReportController::class, 'customerOrders'])->name('customer-orders');
            Route::get('picking-accuracy', [OutboundReportController::class, 'pickingAccuracy'])->name('picking-accuracy');
            Route::get('on-time-delivery', [OutboundReportController::class, 'onTimeDelivery'])->name('on-time-delivery');
        });
        
        // Operations Reports
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::get('daily-summary', [OperationsReportController::class, 'dailySummary'])->name('daily-summary');
            Route::get('warehouse-utilization', [OperationsReportController::class, 'warehouseUtilization'])->name('warehouse-utilization');
            Route::get('labor-productivity', [OperationsReportController::class, 'laborProductivity'])->name('labor-productivity');
            Route::get('equipment-utilization', [OperationsReportController::class, 'equipmentUtilization'])->name('equipment-utilization');
            Route::get('space-utilization', [OperationsReportController::class, 'spaceUtilization'])->name('space-utilization');
        });
        
        // KPI Reports
        Route::prefix('kpi')->name('kpi.')->group(function () {
            Route::get('dashboard', [KpiReportController::class, 'dashboard'])->name('dashboard');
            Route::get('accuracy', [KpiReportController::class, 'accuracy'])->name('accuracy');
            Route::get('efficiency', [KpiReportController::class, 'efficiency'])->name('efficiency');
            Route::get('order-fulfillment', [KpiReportController::class, 'orderFulfillment'])->name('order-fulfillment');
            Route::get('inventory-turnover', [KpiReportController::class, 'inventoryTurnover'])->name('inventory-turnover');
        });
    });

    // ============================================
    // SYSTEM ROUTES
    // ============================================
    Route::prefix('system')->name('system.')->group(function () {
        
        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('settings/edit', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/reset', [SettingController::class, 'reset'])->name('settings.reset');
        
        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::delete('activity-logs/clear', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
        
        // Notifications
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // ============================================
    // MOBILE / PWA ROUTES
    // ============================================
    Route::prefix('mobile')->name('mobile.')->group(function () {
        
        // Good Receiving Mobile
        Route::get('good-receiving', [GoodReceivingMobileController::class, 'index'])->name('good-receiving.index');
        Route::get('good-receiving/{goodReceiving}', [GoodReceivingMobileController::class, 'show'])->name('good-receiving.show');
        Route::post('good-receiving/{goodReceiving}/scan', [GoodReceivingMobileController::class, 'scan'])->name('good-receiving.scan');
        Route::post('good-receiving/{goodReceiving}/confirm', [GoodReceivingMobileController::class, 'confirm'])->name('good-receiving.confirm');
        
        // Putaway Mobile
        Route::get('putaway', [PutawayMobileController::class, 'index'])->name('putaway.index');
        Route::get('putaway/{putawayTask}', [PutawayMobileController::class, 'show'])->name('putaway.show');
        Route::post('putaway/{putawayTask}/scan-product', [PutawayMobileController::class, 'scanProduct'])->name('putaway.scan-product');
        Route::post('putaway/{putawayTask}/scan-location', [PutawayMobileController::class, 'scanLocation'])->name('putaway.scan-location');
        Route::post('putaway/{putawayTask}/confirm', [PutawayMobileController::class, 'confirm'])->name('putaway.confirm');
        
        // Picking Mobile
        Route::get('picking', [PickingMobileController::class, 'index'])->name('picking.index');
        Route::get('picking/{pickingOrder}', [PickingMobileController::class, 'show'])->name('picking.show');
        Route::post('picking/{pickingOrder}/scan', [PickingMobileController::class, 'scan'])->name('picking.scan');
        Route::post('picking/{pickingOrder}/confirm', [PickingMobileController::class, 'confirm'])->name('picking.confirm');
        
        // Stock Count Mobile
        Route::get('stock-count', [StockCountMobileController::class, 'index'])->name('stock-count.index');
        Route::get('stock-count/{stockOpname}', [StockCountMobileController::class, 'show'])->name('stock-count.show');
        Route::post('stock-count/{stockOpname}/scan', [StockCountMobileController::class, 'scan'])->name('stock-count.scan');
        Route::post('stock-count/{stockOpname}/update-count', [StockCountMobileController::class, 'updateCount'])->name('stock-count.update-count');
        Route::post('stock-count/{stockOpname}/complete', [StockCountMobileController::class, 'complete'])->name('stock-count.complete');
        
        // Transfer Mobile
        Route::get('transfer', [TransferMobileController::class, 'index'])->name('transfer.index');
        Route::get('transfer/{transferOrder}', [TransferMobileController::class, 'show'])->name('transfer.show');
        Route::post('transfer/{transferOrder}/scan-from', [TransferMobileController::class, 'scanFrom'])->name('transfer.scan-from');
        Route::post('transfer/{transferOrder}/scan-to', [TransferMobileController::class, 'scanTo'])->name('transfer.scan-to');
        Route::post('transfer/{transferOrder}/confirm', [TransferMobileController::class, 'confirm'])->name('transfer.confirm');
        
        // Packing Mobile
        Route::get('packing', [PackingMobileController::class, 'index'])->name('packing.index');
        Route::get('packing/{packingOrder}', [PackingMobileController::class, 'show'])->name('packing.show');
        Route::post('packing/{packingOrder}/scan', [PackingMobileController::class, 'scan'])->name('packing.scan');
        Route::post('packing/{packingOrder}/confirm', [PackingMobileController::class, 'confirm'])->name('packing.confirm');
    });

    // ============================================
    // UTILITY ROUTES
    // ============================================
    Route::prefix('utility')->name('utility.')->group(function () {
        Route::get('barcode-generator', function () {
            return view('utility.barcode-generator');
        })->name('barcode-generator');
        
        Route::get('qr-generator', function () {
            return view('utility.qr-generator');
        })->name('qr-generator');
        
        Route::get('label-printer', function () {
            return view('utility.label-printer');
        })->name('label-printer');
    });
});

// ============================================
// AUTH ROUTES
// ============================================
require __DIR__.'/auth.php';