<?php

namespace Database\Seeders;

use App\Models\GoodReceiving;
use App\Models\GoodReceivingItem;
use App\Models\InboundShipment;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GoodReceivingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : null;

        $inboundShipments = InboundShipment::with(['purchaseOrder', 'warehouse', 'supplier', 'items'])->get();
        $products = Product::all();

        if ($inboundShipments->isEmpty()) {
            $this->command->warn('Tidak ada inbound shipments. Jalankan InboundShipmentSeeder terlebih dahulu.');
            return;
        }

        // ========================================
        // GR-001 - ISH-2024-001 (COMPLETED)
        // Smartphones receiving - Perfect condition
        // ========================================
        $shipment1 = $inboundShipments->where('shipment_number', 'ISH-2024-001')->first();
        if ($shipment1) {
            $gr1 = GoodReceiving::create([
                'gr_number' => 'GR-2024-001',
                'inbound_shipment_id' => $shipment1->id,
                'purchase_order_id' => $shipment1->purchase_order_id,
                'warehouse_id' => $shipment1->warehouse_id,
                'supplier_id' => $shipment1->supplier_id,
                'receiving_date' => Carbon::now()->subMonths(3)->addDays(5)->setHour(10)->setMinute(30),
                'received_by' => $userId,
                'status' => 'completed',
                'total_items' => 2,
                'total_quantity' => 80,
                'total_pallets' => 8,
                'quality_status' => 'passed',
                'quality_checked_by' => $userId,
                'quality_checked_at' => Carbon::now()->subMonths(3)->addDays(5)->setHour(15)->setMinute(0),
                'notes' => 'All smartphone units received in perfect condition. Serial numbers verified and logged. Packaging intact with no damage.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr1, [
                [
                    'product_sku' => 'ELC-SM-001',
                    'batch_number' => 'BATCH-SM001-2024-Q1',
                    'manufacturing_date' => '2024-01-15',
                    'quantity_expected' => 50,
                    'quantity_received' => 50,
                    'quantity_accepted' => 50,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'Samsung Galaxy S24 Ultra - All 50 units tested and verified. IMEI numbers logged.',
                ],
                [
                    'product_sku' => 'ELC-SM-002',
                    'batch_number' => 'BATCH-SM002-2024-Q1',
                    'manufacturing_date' => '2024-01-20',
                    'quantity_expected' => 30,
                    'quantity_received' => 30,
                    'quantity_accepted' => 30,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'iPhone 15 Pro Max - All units sealed with Apple warranty. Serial numbers match documentation.',
                ],
            ], $shipment1, $products);
        }

        // ========================================
        // GR-002 - ISH-2024-002 (COMPLETED)
        // MacBook Pro receiving
        // ========================================
        $shipment2 = $inboundShipments->where('shipment_number', 'ISH-2024-002')->first();
        if ($shipment2) {
            $gr2 = GoodReceiving::create([
                'gr_number' => 'GR-2024-002',
                'inbound_shipment_id' => $shipment2->id,
                'purchase_order_id' => $shipment2->purchase_order_id,
                'warehouse_id' => $shipment2->warehouse_id,
                'supplier_id' => $shipment2->supplier_id,
                'receiving_date' => Carbon::now()->subMonths(2)->addDays(12)->setHour(12)->setMinute(0),
                'received_by' => $userId,
                'status' => 'completed',
                'total_items' => 1,
                'total_quantity' => 20,
                'total_pallets' => 5,
                'quality_status' => 'passed',
                'quality_checked_by' => $userId,
                'quality_checked_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(16)->setMinute(0),
                'notes' => 'MacBook Pro M3 Max units received. All factory sealed with Apple certification. Performance tests conducted.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr2, [
                [
                    'product_sku' => 'ELC-LT-001',
                    'batch_number' => 'BATCH-LT001-2024-02',
                    'manufacturing_date' => '2024-02-01',
                    'quantity_expected' => 20,
                    'quantity_received' => 20,
                    'quantity_accepted' => 20,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'MacBook Pro 16" M3 Max - All units tested, specifications verified, serial numbers logged.',
                ],
            ], $shipment2, $products);
        }

        // ========================================
        // GR-003 - ISH-2024-003 (COMPLETED)
        // Dell XPS receiving
        // ========================================
        $shipment3 = $inboundShipments->where('shipment_number', 'ISH-2024-003')->first();
        if ($shipment3) {
            $gr3 = GoodReceiving::create([
                'gr_number' => 'GR-2024-003',
                'inbound_shipment_id' => $shipment3->id,
                'purchase_order_id' => $shipment3->purchase_order_id,
                'warehouse_id' => $shipment3->warehouse_id,
                'supplier_id' => $shipment3->supplier_id,
                'receiving_date' => Carbon::now()->subMonths(2)->addDays(12)->setHour(15)->setMinute(30),
                'received_by' => $userId,
                'status' => 'completed',
                'total_items' => 1,
                'total_quantity' => 35,
                'total_pallets' => 7,
                'quality_status' => 'passed',
                'quality_checked_by' => $userId,
                'quality_checked_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(18)->setMinute(45),
                'notes' => 'Dell XPS 15 units received in excellent condition. GPU stress tests passed. All warranty cards included.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr3, [
                [
                    'product_sku' => 'ELC-LT-002',
                    'batch_number' => 'BATCH-LT002-2024-02',
                    'manufacturing_date' => '2024-02-05',
                    'quantity_expected' => 35,
                    'quantity_received' => 35,
                    'quantity_accepted' => 35,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'Dell XPS 15 i9 - GPU and CPU benchmarks passed. OLED display tested - no dead pixels.',
                ],
            ], $shipment3, $products);
        }

        // ========================================
        // GR-004 - ISH-2024-004 (PARTIAL)
        // Raw materials with rejections
        // ========================================
        $shipment4 = $inboundShipments->where('shipment_number', 'ISH-2024-004')->first();
        if ($shipment4) {
            $gr4 = GoodReceiving::create([
                'gr_number' => 'GR-2024-004',
                'inbound_shipment_id' => $shipment4->id,
                'purchase_order_id' => $shipment4->purchase_order_id,
                'warehouse_id' => $shipment4->warehouse_id,
                'supplier_id' => $shipment4->supplier_id,
                'receiving_date' => Carbon::now()->subDays(3)->setHour(10)->setMinute(0),
                'received_by' => $userId,
                'status' => 'completed',
                'total_items' => 3,
                'total_quantity' => 473, // 115 + 58 + 300
                'total_pallets' => 18,
                'quality_status' => 'partial',
                'quality_checked_by' => $userId,
                'quality_checked_at' => Carbon::now()->subDays(3)->setHour(15)->setMinute(0),
                'notes' => 'Batch 1 of raw materials received. Quality issues found on steel plates - surface defects and minor dents. Plastic pellets quality excellent. Rejection documented with photos.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr4, [
                [
                    'product_sku' => 'RAW-MT-001',
                    'batch_number' => 'BATCH-MT001-2024-03-A',
                    'manufacturing_date' => '2024-03-10',
                    'quantity_expected' => 120,
                    'quantity_received' => 120,
                    'quantity_accepted' => 115,
                    'quantity_rejected' => 5,
                    'quality_status' => 'failed', // Changed from 'partial' to 'failed' (ada rejection)
                    'rejection_reason' => '5 sheets rejected: Surface defects - scratches, minor rust spots, and mill finish quality below specification',
                    'notes' => 'Steel Plate SPCC 1.0mm - Mill certificates verified. Photos taken of rejected sheets. Supplier notified.',
                ],
                [
                    'product_sku' => 'RAW-MT-002',
                    'batch_number' => 'BATCH-MT002-2024-03-A',
                    'manufacturing_date' => '2024-03-12',
                    'quantity_expected' => 60,
                    'quantity_received' => 60,
                    'quantity_accepted' => 58,
                    'quantity_rejected' => 2,
                    'quality_status' => 'failed', // Changed from 'partial' to 'failed' (ada rejection)
                    'rejection_reason' => '2 sheets rejected: Edge dents and corner damage from transport. Not suitable for production use.',
                    'notes' => 'Stainless Steel SUS304 - Material certificates OK. Damage occurred during shipping. Claim filed.',
                ],
                [
                    'product_sku' => 'RAW-PL-001',
                    'batch_number' => 'BATCH-PL001-2024-03',
                    'manufacturing_date' => '2024-03-05',
                    'expiry_date' => '2029-03-05',
                    'quantity_expected' => 300,
                    'quantity_received' => 300,
                    'quantity_accepted' => 300,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'HDPE Plastic Pellets Natural - All bags sealed properly. COA verified. Moisture content within specification. No issues found.',
                ],
            ], $shipment4, $products);
        }

        // ========================================
        // GR-005 - Direct GR (No Shipment Reference)
        // Emergency receiving without PO
        // ========================================
        $warehouse = Warehouse::where('code', 'WH001')->first();
        $supplier = \App\Models\Supplier::where('code', 'SUP-001')->first();
        
        if ($warehouse && $supplier) {
            $gr5 = GoodReceiving::create([
                'gr_number' => 'GR-2024-005',
                'inbound_shipment_id' => null,
                'purchase_order_id' => null,
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $supplier->id,
                'receiving_date' => Carbon::now()->subDays(10)->setHour(14)->setMinute(0),
                'received_by' => $userId,
                'status' => 'completed',
                'total_items' => 2,
                'total_quantity' => 300,
                'total_pallets' => 6,
                'quality_status' => 'passed',
                'quality_checked_by' => $userId,
                'quality_checked_at' => Carbon::now()->subDays(10)->setHour(16)->setMinute(30),
                'notes' => 'Emergency stock replenishment - Direct receiving without PO. Approved by Operations Manager. Payment terms: COD.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr5, [
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_expected' => 200,
                    'quantity_received' => 200,
                    'quantity_accepted' => 200,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'Kertas A4 80gsm - Emergency restock for urgent customer order. Quality checked - within specification.',
                ],
                [
                    'product_sku' => 'SPR-BR-001',
                    'batch_number' => 'BATCH-BR001-2024-03',
                    'quantity_expected' => 100,
                    'quantity_received' => 100,
                    'quantity_accepted' => 100,
                    'quantity_rejected' => 0,
                    'quality_status' => 'passed',
                    'notes' => 'Deep Groove Ball Bearing 6205 ZZ - Emergency order. Original SKF packaging. Batch verified.',
                ],
            ], null, $products);
        }

        // ========================================
        // GR-006 - IN PROGRESS (Quality Check)
        // Currently under inspection
        // ========================================
        $po6 = PurchaseOrder::where('po_number', 'PO-2024-006')->first();
        if ($po6) {
            $gr6 = GoodReceiving::create([
                'gr_number' => 'GR-2024-006',
                'inbound_shipment_id' => null,
                'purchase_order_id' => $po6->id,
                'warehouse_id' => $po6->warehouse_id,
                'supplier_id' => $po6->supplier_id,
                'receiving_date' => Carbon::now()->setHour(9)->setMinute(0),
                'received_by' => $userId,
                'status' => 'quality_check',
                'total_items' => 2,
                'total_quantity' => 230,
                'total_pallets' => 12,
                'quality_status' => 'pending',
                'quality_checked_by' => null,
                'quality_checked_at' => null,
                'notes' => 'Chemicals shipment currently under quality inspection. MSDS certificates being verified. Expected completion in 2 hours.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createGRItems($gr6, [
                [
                    'product_sku' => 'CHM-LB-001',
                    'batch_number' => 'BATCH-LB001-2024-04',
                    'manufacturing_date' => '2024-03-25',
                    'expiry_date' => '2027-03-25',
                    'quantity_expected' => 150,
                    'quantity_received' => 150,
                    'quantity_accepted' => 0,
                    'quantity_rejected' => 0,
                    'quality_status' => 'pending',
                    'notes' => 'Shell Helix Ultra 5W-40 - Under quality inspection. Viscosity test in progress.',
                ],
                [
                    'product_sku' => 'CHM-CL-001',
                    'batch_number' => 'BATCH-CL001-2024-04',
                    'manufacturing_date' => '2024-03-20',
                    'expiry_date' => '2026-03-20',
                    'quantity_expected' => 80,
                    'quantity_received' => 80,
                    'quantity_accepted' => 0,
                    'quantity_rejected' => 0,
                    'quality_status' => 'pending',
                    'notes' => 'Detergen Industrial - Awaiting pH level test results and MSDS verification.',
                ],
            ], null, $products);
        }

        // ========================================
        // GR-007 - DRAFT
        // Started but not completed
        // ========================================
        $po7 = PurchaseOrder::where('po_number', 'PO-2024-007')->first();
        if ($po7) {
            $gr7 = GoodReceiving::create([
                'gr_number' => 'GR-2024-007',
                'inbound_shipment_id' => null,
                'purchase_order_id' => $po7->id,
                'warehouse_id' => $po7->warehouse_id,
                'supplier_id' => $po7->supplier_id,
                'receiving_date' => Carbon::now()->addDays(5)->setHour(10)->setMinute(0),
                'received_by' => null,
                'status' => 'draft',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_pallets' => 0,
                'quality_status' => 'pending',
                'quality_checked_by' => null,
                'quality_checked_at' => null,
                'notes' => 'Draft GR created for upcoming office supplies delivery. Scheduled for next week.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        }

        $this->command->info('✓ Created 7 good receiving records successfully!');
        $this->command->info('  - 4 Completed GR with items');
        $this->command->info('  - 1 Quality check in progress');
        $this->command->info('  - 1 Direct GR without PO');
        $this->command->info('  - 1 Draft GR');
    }

    /**
     * Create GR items
     */
    private function createGRItems($goodReceiving, $items, $shipment, $products): void
    {
        foreach ($items as $item) {
            $product = $products->firstWhere('sku', $item['product_sku']);
            
            if (!$product) {
                $this->command->warn("Product {$item['product_sku']} not found, skipping...");
                continue;
            }

            // Find PO item if shipment exists
            $poItemId = null;
            if ($shipment && $shipment->purchaseOrder) {
                $poItem = $shipment->purchaseOrder->items()
                    ->where('product_id', $product->id)
                    ->first();
                $poItemId = $poItem ? $poItem->id : null;
            }

            GoodReceivingItem::create([
                'good_receiving_id' => $goodReceiving->id,
                'purchase_order_item_id' => $poItemId,
                'product_id' => $product->id,
                'batch_number' => $item['batch_number'] ?? null,
                'serial_number' => $item['serial_number'] ?? null,
                'manufacturing_date' => $item['manufacturing_date'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'quantity_expected' => $item['quantity_expected'],
                'quantity_received' => $item['quantity_received'],
                'quantity_accepted' => $item['quantity_accepted'],
                'quantity_rejected' => $item['quantity_rejected'],
                'unit_of_measure' => $product->unit->abbreviation ?? 'PCS',
                'pallet_id' => null,
                'quality_status' => $item['quality_status'],
                'rejection_reason' => $item['rejection_reason'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }
}