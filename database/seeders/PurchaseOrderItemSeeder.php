<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $purchaseOrders = PurchaseOrder::all();
        $products = Product::all();
        $units = Unit::all();

        if ($purchaseOrders->isEmpty()) {
            $this->command->warn('Tidak ada purchase orders. Jalankan PurchaseOrderSeeder terlebih dahulu.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada products. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        // ========================================
        // PO-2024-001 (COMPLETED) - Smartphones
        // ========================================
        $po1 = $purchaseOrders->where('po_number', 'PO-2024-001')->first();
        if ($po1) {
            $this->createPOItems($po1, [
                [
                    'product_sku' => 'ELC-SM-001',
                    'quantity_ordered' => 50.00,
                    'quantity_received' => 50.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 16500000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => 'BATCH-SM001-2024-Q1',
                    'manufacturing_date' => '2024-01-15',
                    'notes' => 'Samsung Galaxy S24 Ultra - All units received in good condition',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'ELC-SM-002',
                    'quantity_ordered' => 30.00,
                    'quantity_received' => 30.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 19500000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => 'BATCH-SM002-2024-Q1',
                    'manufacturing_date' => '2024-01-20',
                    'notes' => 'iPhone 15 Pro Max - Perfect condition',
                    'sort_order' => 2,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-002 (RECEIVED) - Laptops
        // ========================================
        $po2 = $purchaseOrders->where('po_number', 'PO-2024-002')->first();
        if ($po2) {
            $this->createPOItems($po2, [
                [
                    'product_sku' => 'ELC-LT-001',
                    'quantity_ordered' => 20.00,
                    'quantity_received' => 20.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 75000000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'batch_number' => 'BATCH-LT001-2024-02',
                    'manufacturing_date' => '2024-02-01',
                    'notes' => 'MacBook Pro 16" - Corporate order',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'ELC-LT-002',
                    'quantity_ordered' => 35.00,
                    'quantity_received' => 35.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 38000000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'batch_number' => 'BATCH-LT002-2024-02',
                    'manufacturing_date' => '2024-02-05',
                    'notes' => 'Dell XPS 15 - All received',
                    'sort_order' => 2,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-003 (PARTIAL RECEIVED) - Raw Materials
        // ========================================
        $po3 = $purchaseOrders->where('po_number', 'PO-2024-003')->first();
        if ($po3) {
            $this->createPOItems($po3, [
                [
                    'product_sku' => 'RAW-MT-001',
                    'quantity_ordered' => 200.00,
                    'quantity_received' => 120.00,
                    'quantity_rejected' => 5.00,
                    'unit_price' => 285000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => 'BATCH-MT001-2024-03-A',
                    'manufacturing_date' => '2024-03-10',
                    'rejection_reason' => '5 sheets dengan surface defects',
                    'notes' => 'Steel Plate SPCC - Batch 1 of 2 received',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'RAW-MT-002',
                    'quantity_ordered' => 100.00,
                    'quantity_received' => 60.00,
                    'quantity_rejected' => 2.00,
                    'unit_price' => 1850000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => 'BATCH-MT002-2024-03-A',
                    'manufacturing_date' => '2024-03-12',
                    'rejection_reason' => '2 sheets dengan scratches',
                    'notes' => 'Stainless Steel SUS304 - Partial delivery',
                    'sort_order' => 2,
                ],
                [
                    'product_sku' => 'RAW-PL-001',
                    'quantity_ordered' => 500.00,
                    'quantity_received' => 300.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 625000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => 'BATCH-PL001-2024-03',
                    'manufacturing_date' => '2024-03-05',
                    'expiry_date' => '2029-03-05',
                    'notes' => 'HDPE Plastic Pellets - Batch 1 received',
                    'sort_order' => 3,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-004 (CONFIRMED) - Office Furniture
        // ========================================
        $po4 = $purchaseOrders->where('po_number', 'PO-2024-004')->first();
        if ($po4) {
            $this->createPOItems($po4, [
                [
                    'product_sku' => 'OFC-FR-001',
                    'quantity_ordered' => 25.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 1450000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 1.00,
                    'notes' => 'Kursi Kantor Ergonomis - Awaiting delivery',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'OFC-FR-002',
                    'quantity_ordered' => 20.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 980000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 1.00,
                    'notes' => 'Meja Kerja - Awaiting delivery',
                    'sort_order' => 2,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-005 (APPROVED) - Consumables & Safety
        // ========================================
        $po5 = $purchaseOrders->where('po_number', 'PO-2024-005')->first();
        if ($po5) {
            $this->createPOItems($po5, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 500.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 98000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'expiry_date' => Carbon::now()->addYear()->format('Y-m-d'),
                    'notes' => 'Indomie Goreng - Large quantity order',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 800.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 35000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'),
                    'notes' => 'Aqua Botol 600ml - Bulk order',
                    'sort_order' => 2,
                ],
                [
                    'product_sku' => 'SAF-EQ-001',
                    'quantity_ordered' => 200.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 285000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'notes' => 'Sepatu Safety - Various sizes 39-45',
                    'sort_order' => 3,
                ],
                [
                    'product_sku' => 'SAF-EQ-002',
                    'quantity_ordered' => 300.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 45000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 2.00,
                    'notes' => 'Helm Safety - Mixed colors',
                    'sort_order' => 4,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-006 (SUBMITTED) - Chemicals
        // ========================================
        $po6 = $purchaseOrders->where('po_number', 'PO-2024-006')->first();
        if ($po6) {
            $this->createPOItems($po6, [
                [
                    'product_sku' => 'CHM-LB-001',
                    'quantity_ordered' => 150.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 385000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'batch_number' => null,
                    'manufacturing_date' => null,
                    'expiry_date' => Carbon::now()->addYears(3)->format('Y-m-d'),
                    'notes' => 'Shell Helix Ultra - Pending approval',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'CHM-CL-001',
                    'quantity_ordered' => 80.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 285000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'expiry_date' => Carbon::now()->addYears(2)->format('Y-m-d'),
                    'notes' => 'Detergen Industrial - Pending approval',
                    'sort_order' => 2,
                ],
            ], $products, $units);
        }

        // ========================================
        // PO-2024-007 (DRAFT) - Office Supplies
        // ========================================
        $po7 = $purchaseOrders->where('po_number', 'PO-2024-007')->first();
        if ($po7) {
            $this->createPOItems($po7, [
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_ordered' => 500.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 38000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'notes' => 'Kertas A4 80gsm - Draft order',
                    'sort_order' => 1,
                ],
                [
                    'product_sku' => 'SPR-BR-001',
                    'quantity_ordered' => 200.00,
                    'quantity_received' => 0.00,
                    'quantity_rejected' => 0.00,
                    'unit_price' => 68000.00,
                    'tax_rate' => 11.00,
                    'discount_rate' => 0.00,
                    'notes' => 'Ball Bearing 6205 - Draft order',
                    'sort_order' => 2,
                ],
            ], $products, $units);
        }

        $this->command->info('✓ Purchase order items created successfully for all POs!');
    }

    /**
     * Create PO items for a purchase order
     */
    private function createPOItems($purchaseOrder, $items, $products, $units): void
    {
        foreach ($items as $item) {
            $product = $products->firstWhere('sku', $item['product_sku']);
            
            if (!$product) {
                $this->command->warn("Product {$item['product_sku']} not found, skipping...");
                continue;
            }

            $quantityOrdered = $item['quantity_ordered'];
            $quantityReceived = $item['quantity_received'];
            $quantityRejected = $item['quantity_rejected'] ?? 0;
            $unitPrice = $item['unit_price'];
            $taxRate = $item['tax_rate'];
            $discountRate = $item['discount_rate'] ?? 0;

            // Calculate amounts
            $subtotal = $quantityOrdered * $unitPrice;
            $discountAmount = $subtotal * ($discountRate / 100);
            $subtotalAfterDiscount = $subtotal - $discountAmount;
            $taxAmount = $subtotalAfterDiscount * ($taxRate / 100);
            $lineTotal = $subtotalAfterDiscount + $taxAmount;

            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $product->id,
                'product_sku' => $product->sku,
                'product_name' => $product->name,
                'quantity_ordered' => $quantityOrdered,
                'quantity_received' => $quantityReceived,
                'quantity_rejected' => $quantityRejected,
                'unit_id' => $product->unit_id,
                'unit_price' => $unitPrice,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'discount_rate' => $discountRate,
                'discount_amount' => $discountAmount,
                'subtotal' => $subtotal,
                'line_total' => $lineTotal,
                'batch_number' => $item['batch_number'] ?? null,
                'manufacturing_date' => $item['manufacturing_date'] ?? null,
                'expiry_date' => $item['expiry_date'] ?? null,
                'rejection_reason' => $item['rejection_reason'] ?? null,
                'notes' => $item['notes'] ?? null,
                'sort_order' => $item['sort_order'] ?? 0,
            ]);
        }
    }
}