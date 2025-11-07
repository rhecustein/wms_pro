<?php

namespace Database\Seeders;

use App\Models\InboundShipment;
use App\Models\InboundShipmentItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InboundShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : null;

        $purchaseOrders = PurchaseOrder::with(['items', 'warehouse', 'supplier'])->get();
        $products = Product::all();
        $units = Unit::all();
        $locations = WarehouseLocation::all();

        if ($purchaseOrders->isEmpty()) {
            $this->command->warn('Tidak ada purchase orders. Jalankan PurchaseOrderSeeder terlebih dahulu.');
            return;
        }

        // ========================================
        // SHIPMENT 1 - PO-2024-001 (COMPLETED)
        // ========================================
        $po1 = $purchaseOrders->where('po_number', 'PO-2024-001')->first();
        if ($po1) {
            $shipment1 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-001',
                'purchase_order_id' => $po1->id,
                'warehouse_id' => $po1->warehouse_id,
                'supplier_id' => $po1->supplier_id,
                'scheduled_date' => Carbon::now()->subMonths(3)->addDays(3),
                'shipment_date' => Carbon::now()->subMonths(3)->addDays(4),
                'arrival_date' => Carbon::now()->subMonths(3)->addDays(5)->setHour(9)->setMinute(30),
                'unloading_start' => Carbon::now()->subMonths(3)->addDays(5)->setHour(10)->setMinute(0),
                'unloading_end' => Carbon::now()->subMonths(3)->addDays(5)->setHour(14)->setMinute(30),
                'completed_at' => Carbon::now()->subMonths(3)->addDays(5)->setHour(16)->setMinute(0),
                'expected_pallets' => 8,
                'received_pallets' => 8,
                'expected_boxes' => 80,
                'received_boxes' => 80,
                'expected_weight' => 450.00,
                'actual_weight' => 448.50,
                'vehicle_type' => 'Box Truck',
                'vehicle_number' => 'B 1234 XYZ',
                'driver_name' => 'Budi Santoso',
                'driver_phone' => '0812-3456-7890',
                'driver_id_number' => '3201012345670001',
                'seal_number' => 'SEAL-2024-001',
                'status' => 'completed',
                'dock_number' => 'DOCK-A1',
                'received_by' => $userId,
                'inspected_by' => $userId,
                'bill_of_lading' => 'BOL-SUP001-2024-001',
                'packing_list' => 'PKL-SUP001-2024-001',
                'attachments' => json_encode([
                    'delivery_note' => 'DN-2024-001.pdf',
                    'quality_cert' => 'QC-2024-001.pdf'
                ]),
                'inspection_result' => 'passed',
                'inspection_notes' => 'All items received in excellent condition. Serial numbers verified. Packaging intact.',
                'has_damages' => false,
                'notes' => 'Smooth receiving process. Driver very professional. All documentation complete.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Create shipment items for PO-2024-001
            $this->createShipmentItems($shipment1, $po1, [
                [
                    'product_sku' => 'ELC-SM-001',
                    'quantity_expected' => 50.00,
                    'quantity_received' => 50.00,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-SM001-2024-Q1',
                    'manufacturing_date' => '2024-01-15',
                    'quality_status' => 'passed',
                    'qc_notes' => 'All 50 units tested - IMEI verified, screen quality excellent, no dead pixels',
                ],
                [
                    'product_sku' => 'ELC-SM-002',
                    'quantity_expected' => 30.00,
                    'quantity_received' => 30.00,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-SM002-2024-Q1',
                    'manufacturing_date' => '2024-01-20',
                    'quality_status' => 'passed',
                    'qc_notes' => 'All units sealed with original Apple packaging, serial numbers match documentation',
                ],
            ], $products, $units, $locations);
        }

        // ========================================
        // SHIPMENT 2 & 3 - PO-2024-002 (RECEIVED - 2 Shipments)
        // ========================================
        $po2 = $purchaseOrders->where('po_number', 'PO-2024-002')->first();
        if ($po2) {
            // First shipment - MacBook Pro
            $shipment2 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-002',
                'purchase_order_id' => $po2->id,
                'warehouse_id' => $po2->warehouse_id,
                'supplier_id' => $po2->supplier_id,
                'scheduled_date' => Carbon::now()->subMonths(2)->addDays(8),
                'shipment_date' => Carbon::now()->subMonths(2)->addDays(9),
                'arrival_date' => Carbon::now()->subMonths(2)->addDays(12)->setHour(11)->setMinute(15),
                'unloading_start' => Carbon::now()->subMonths(2)->addDays(12)->setHour(11)->setMinute(45),
                'unloading_end' => Carbon::now()->subMonths(2)->addDays(12)->setHour(15)->setMinute(0),
                'completed_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(16)->setMinute(30),
                'expected_pallets' => 5,
                'received_pallets' => 5,
                'expected_boxes' => 20,
                'received_boxes' => 20,
                'expected_weight' => 850.00,
                'actual_weight' => 853.20,
                'vehicle_type' => 'Container 20ft',
                'vehicle_number' => 'L 5678 ABC',
                'container_number' => 'CONT-20240212-001',
                'driver_name' => 'Ahmad Fauzi',
                'driver_phone' => '0813-9876-5432',
                'driver_id_number' => '3578012345670002',
                'seal_number' => 'SEAL-2024-002',
                'status' => 'completed',
                'dock_number' => 'DOCK-B2',
                'received_by' => $userId,
                'inspected_by' => $userId,
                'bill_of_lading' => 'BOL-SUP002-2024-078-A',
                'packing_list' => 'PKL-SUP002-2024-078-A',
                'inspection_result' => 'passed',
                'inspection_notes' => 'MacBook Pro units - all sealed, serial numbers logged, excellent packaging',
                'has_damages' => false,
                'notes' => 'First shipment of PO-2024-002. Container in perfect condition.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createShipmentItems($shipment2, $po2, [
                [
                    'product_sku' => 'ELC-LT-001',
                    'quantity_expected' => 20.00,
                    'quantity_received' => 20.00,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-LT001-2024-02',
                    'manufacturing_date' => '2024-02-01',
                    'quality_status' => 'passed',
                    'qc_notes' => 'All MacBook Pro M3 Max units verified - performance test passed',
                ],
            ], $products, $units, $locations);

            // Second shipment - Dell XPS
            $shipment3 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-003',
                'purchase_order_id' => $po2->id,
                'warehouse_id' => $po2->warehouse_id,
                'supplier_id' => $po2->supplier_id,
                'scheduled_date' => Carbon::now()->subMonths(2)->addDays(9),
                'shipment_date' => Carbon::now()->subMonths(2)->addDays(10),
                'arrival_date' => Carbon::now()->subMonths(2)->addDays(12)->setHour(14)->setMinute(30),
                'unloading_start' => Carbon::now()->subMonths(2)->addDays(12)->setHour(15)->setMinute(0),
                'unloading_end' => Carbon::now()->subMonths(2)->addDays(12)->setHour(18)->setMinute(15),
                'completed_at' => Carbon::now()->subMonths(2)->addDays(12)->setHour(19)->setMinute(0),
                'expected_pallets' => 7,
                'received_pallets' => 7,
                'expected_boxes' => 35,
                'received_boxes' => 35,
                'expected_weight' => 1250.00,
                'actual_weight' => 1247.80,
                'vehicle_type' => 'Container 20ft',
                'vehicle_number' => 'L 5678 ABC',
                'container_number' => 'CONT-20240212-002',
                'driver_name' => 'Ahmad Fauzi',
                'driver_phone' => '0813-9876-5432',
                'driver_id_number' => '3578012345670002',
                'seal_number' => 'SEAL-2024-003',
                'status' => 'completed',
                'dock_number' => 'DOCK-B3',
                'received_by' => $userId,
                'inspected_by' => $userId,
                'bill_of_lading' => 'BOL-SUP002-2024-078-B',
                'packing_list' => 'PKL-SUP002-2024-078-B',
                'inspection_result' => 'passed',
                'inspection_notes' => 'Dell XPS units - all factory sealed with warranty cards',
                'has_damages' => false,
                'notes' => 'Second shipment of PO-2024-002 completed successfully.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createShipmentItems($shipment3, $po2, [
                [
                    'product_sku' => 'ELC-LT-002',
                    'quantity_expected' => 35.00,
                    'quantity_received' => 35.00,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-LT002-2024-02',
                    'manufacturing_date' => '2024-02-05',
                    'quality_status' => 'passed',
                    'qc_notes' => 'Dell XPS i9 units - GPU stress test completed, all passed',
                ],
            ], $products, $units, $locations);
        }

        // ========================================
        // SHIPMENT 4 - PO-2024-003 (PARTIAL RECEIVED - 1st Batch)
        // ========================================
        $po3 = $purchaseOrders->where('po_number', 'PO-2024-003')->first();
        if ($po3) {
            $shipment4 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-004',
                'purchase_order_id' => $po3->id,
                'warehouse_id' => $po3->warehouse_id,
                'supplier_id' => $po3->supplier_id,
                'scheduled_date' => Carbon::now()->subDays(5),
                'shipment_date' => Carbon::now()->subDays(4),
                'arrival_date' => Carbon::now()->subDays(3)->setHour(8)->setMinute(45),
                'unloading_start' => Carbon::now()->subDays(3)->setHour(9)->setMinute(30),
                'unloading_end' => Carbon::now()->subDays(3)->setHour(13)->setMinute(45),
                'completed_at' => Carbon::now()->subDays(3)->setHour(15)->setMinute(30),
                'expected_pallets' => 18,
                'received_pallets' => 18,
                'expected_boxes' => 180,
                'received_boxes' => 177,
                'expected_weight' => 3500.00,
                'actual_weight' => 3425.50,
                'vehicle_type' => 'Flatbed Truck',
                'vehicle_number' => 'B 9012 DEF',
                'driver_name' => 'Suhardi',
                'driver_phone' => '0821-5555-6666',
                'driver_id_number' => '3201012345670003',
                'seal_number' => 'SEAL-2024-004',
                'status' => 'completed',
                'dock_number' => 'DOCK-C1',
                'received_by' => $userId,
                'inspected_by' => $userId,
                'bill_of_lading' => 'BOL-SUP003-2024-103-A',
                'packing_list' => 'PKL-SUP003-2024-103-A',
                'inspection_result' => 'partial',
                'inspection_notes' => 'Batch 1 received. Some steel plates have minor surface defects. Plastic pellets quality excellent.',
                'has_damages' => true,
                'damage_description' => '5 sheets of SPCC steel plate with surface scratches. 2 sheets of SUS304 with minor dents. Photos documented.',
                'notes' => 'First batch of raw materials. Second batch expected in 2 weeks. Quality issues documented with photos.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createShipmentItems($shipment4, $po3, [
                [
                    'product_sku' => 'RAW-MT-001',
                    'quantity_expected' => 120.00,
                    'quantity_received' => 120.00,
                    'quantity_rejected' => 5.00,
                    'batch_number' => 'BATCH-MT001-2024-03-A',
                    'manufacturing_date' => '2024-03-10',
                    'quality_status' => 'passed',
                    'rejection_reason' => 'Surface defects - scratches and minor rust spots on 5 sheets',
                    'qc_notes' => 'Mill certificates verified. 5 sheets rejected due to surface quality issues.',
                ],
                [
                    'product_sku' => 'RAW-MT-002',
                    'quantity_expected' => 60.00,
                    'quantity_received' => 60.00,
                    'quantity_rejected' => 2.00,
                    'batch_number' => 'BATCH-MT002-2024-03-A',
                    'manufacturing_date' => '2024-03-12',
                    'quality_status' => 'passed',
                    'rejection_reason' => '2 sheets with minor edge dents during transport',
                    'qc_notes' => 'Material certificates OK. 2 sheets with cosmetic damage on edges.',
                ],
                [
                    'product_sku' => 'RAW-PL-001',
                    'quantity_expected' => 300.00,
                    'quantity_received' => 300.00,
                    'quantity_rejected' => 0.00,
                    'batch_number' => 'BATCH-PL001-2024-03',
                    'manufacturing_date' => '2024-03-05',
                    'expiry_date' => '2029-03-05',
                    'quality_status' => 'passed',
                    'qc_notes' => 'All bags sealed properly. COA verified. Moisture content within spec.',
                ],
            ], $products, $units, $locations);
        }

        // ========================================
        // SHIPMENT 5 - PO-2024-004 (IN TRANSIT)
        // ========================================
        $po4 = $purchaseOrders->where('po_number', 'PO-2024-004')->first();
        if ($po4) {
            $shipment5 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-005',
                'purchase_order_id' => $po4->id,
                'warehouse_id' => $po4->warehouse_id,
                'supplier_id' => $po4->supplier_id,
                'scheduled_date' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0),
                'shipment_date' => Carbon::now()->subDays(1)->setHour(14)->setMinute(30),
                'arrival_date' => null,
                'unloading_start' => null,
                'unloading_end' => null,
                'completed_at' => null,
                'expected_pallets' => 12,
                'received_pallets' => 0,
                'expected_boxes' => 45,
                'received_boxes' => 0,
                'expected_weight' => 850.00,
                'actual_weight' => null,
                'vehicle_type' => 'Box Truck',
                'vehicle_number' => 'D 3456 GHI',
                'driver_name' => 'Dedi Kurniawan',
                'driver_phone' => '0822-7777-8888',
                'driver_id_number' => '3273012345670004',
                'seal_number' => 'SEAL-2024-005',
                'status' => 'in_transit',
                'dock_number' => null,
                'received_by' => null,
                'inspected_by' => null,
                'bill_of_lading' => 'BOL-SUP001-2024-145',
                'packing_list' => 'PKL-SUP001-2024-145',
                'inspection_result' => null,
                'inspection_notes' => null,
                'has_damages' => false,
                'notes' => 'Office furniture shipment in transit from Jakarta to Bandung. ETA 2 days.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createShipmentItems($shipment5, $po4, [
                [
                    'product_sku' => 'OFC-FR-001',
                    'quantity_expected' => 25.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'quality_status' => 'pending',
                    'notes' => 'Kursi Kantor Ergonomis - In transit',
                ],
                [
                    'product_sku' => 'OFC-FR-002',
                    'quantity_expected' => 20.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'quality_status' => 'pending',
                    'notes' => 'Meja Kerja - In transit',
                ],
            ], $products, $units, $locations);
        }

        // ========================================
        // SHIPMENT 6 - PO-2024-005 (SCHEDULED)
        // ========================================
        $po5 = $purchaseOrders->where('po_number', 'PO-2024-005')->first();
        if ($po5) {
            $shipment6 = InboundShipment::create([
                'shipment_number' => 'ISH-2024-006',
                'purchase_order_id' => $po5->id,
                'warehouse_id' => $po5->warehouse_id,
                'supplier_id' => $po5->supplier_id,
                'scheduled_date' => Carbon::now()->addDays(14)->setHour(9)->setMinute(0),
                'shipment_date' => null,
                'arrival_date' => null,
                'unloading_start' => null,
                'unloading_end' => null,
                'completed_at' => null,
                'expected_pallets' => 35,
                'received_pallets' => 0,
                'expected_boxes' => 1800,
                'received_boxes' => 0,
                'expected_weight' => 4500.00,
                'actual_weight' => null,
                'vehicle_type' => 'Container 40ft',
                'vehicle_number' => 'TBD',
                'driver_name' => null,
                'driver_phone' => null,
                'seal_number' => null,
                'status' => 'scheduled',
                'dock_number' => null,
                'received_by' => null,
                'inspected_by' => null,
                'bill_of_lading' => null,
                'packing_list' => null,
                'inspection_result' => null,
                'inspection_notes' => null,
                'has_damages' => false,
                'notes' => 'Large shipment of consumables and safety equipment to Medan warehouse. Scheduled for next 2 weeks.',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->createShipmentItems($shipment6, $po5, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_expected' => 500.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'expiry_date' => Carbon::now()->addYear()->format('Y-m-d'),
                    'quality_status' => 'pending',
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_expected' => 800.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'),
                    'quality_status' => 'pending',
                ],
                [
                    'product_sku' => 'SAF-EQ-001',
                    'quantity_expected' => 200.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'quality_status' => 'pending',
                ],
                [
                    'product_sku' => 'SAF-EQ-002',
                    'quantity_expected' => 300.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'quality_status' => 'pending',
                ],
            ], $products, $units, $locations);
        }

        $this->command->info('✓ Created 6 inbound shipments with items successfully!');
        $this->command->info('  - 3 Completed shipments');
        $this->command->info('  - 1 Partial received shipment');
        $this->command->info('  - 1 In-transit shipment');
        $this->command->info('  - 1 Scheduled shipment');
    }

    /**
     * Create shipment items
     */
    private function createShipmentItems($shipment, $purchaseOrder, $items, $products, $units, $locations): void
    {
        foreach ($items as $item) {
            $product = $products->firstWhere('sku', $item['product_sku']);
            
            if (!$product) {
                $this->command->warn("Product {$item['product_sku']} not found, skipping...");
                continue;
            }

            // Find the corresponding PO item
            $poItem = $purchaseOrder->items()->where('product_id', $product->id)->first();
            
            if (!$poItem) {
                $this->command->warn("PO Item for product {$item['product_sku']} not found, skipping...");
                continue;
            }

            // Get a random location if available
            $locationId = $locations->isNotEmpty() ? $locations->random()->id : null;

            InboundShipmentItem::create([
                'inbound_shipment_id' => $shipment->id,
                'purchase_order_item_id' => $poItem->id,
                'product_id' => $product->id,
                'quantity_expected' => $item['quantity_expected'],
                'quantity_received' => $item['quantity_received'],
                'quantity_rejected' => $item['quantity_rejected'] ?? 0.00,
                'unit_id' => $product->unit_id,
                'batch_number' => $item['batch_number'] ?? null,
                'manufacturing_date' => $item['manufacturing_date'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'serial_numbers' => isset($item['serial_numbers']) ? json_encode($item['serial_numbers']) : null,
                'location_id' => $locationId,
                'quality_status' => $item['quality_status'] ?? 'pending',
                'rejection_reason' => $item['rejection_reason'] ?? null,
                'qc_notes' => $item['qc_notes'] ?? null,
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }
}