<?php

namespace Database\Seeders;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SalesOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesOrders = SalesOrder::all();
        $products = Product::where('is_active', true)->get();

        if ($salesOrders->isEmpty()) {
            $this->command->warn('Tidak ada sales orders. Jalankan SalesOrderSeeder terlebih dahulu.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada products. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        // ========================================
        // SO-00001 (DELIVERED) - VIP Customer Mixed Items
        // ========================================
        $so1 = $salesOrders->firstWhere('so_number', 'SO-00001');
        if ($so1) {
            $this->createOrderItems($so1, [
                [
                    'product_sku' => 'ELC-SM-001',
                    'quantity_ordered' => 5,
                    'quantity_picked' => 5,
                    'quantity_packed' => 5,
                    'quantity_shipped' => 5,
                ],
                [
                    'product_sku' => 'ELC-LT-001',
                    'quantity_ordered' => 2,
                    'quantity_picked' => 2,
                    'quantity_packed' => 2,
                    'quantity_shipped' => 2,
                ],
                [
                    'product_sku' => 'OFC-FR-001',
                    'quantity_ordered' => 15,
                    'quantity_picked' => 15,
                    'quantity_packed' => 15,
                    'quantity_shipped' => 15,
                ],
                [
                    'product_sku' => 'OFC-FR-002',
                    'quantity_ordered' => 15,
                    'quantity_picked' => 15,
                    'quantity_packed' => 15,
                    'quantity_shipped' => 15,
                ],
            ], $products);
        }

        // ========================================
        // SO-00002 (DELIVERED) - Wholesale FMCG
        // ========================================
        $so2 = $salesOrders->firstWhere('so_number', 'SO-00002');
        if ($so2) {
            $this->createOrderItems($so2, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 100,
                    'quantity_picked' => 100,
                    'quantity_packed' => 100,
                    'quantity_shipped' => 100,
                ],
                [
                    'product_sku' => 'FNB-IN-002',
                    'quantity_ordered' => 80,
                    'quantity_picked' => 80,
                    'quantity_packed' => 80,
                    'quantity_shipped' => 80,
                ],
                [
                    'product_sku' => 'FNB-BV-001',
                    'quantity_ordered' => 150,
                    'quantity_picked' => 150,
                    'quantity_packed' => 150,
                    'quantity_shipped' => 150,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 200,
                    'quantity_picked' => 200,
                    'quantity_packed' => 200,
                    'quantity_shipped' => 200,
                ],
            ], $products);
        }

        // ========================================
        // SO-00003 (DELIVERED) - Regular Retail Mix
        // ========================================
        $so3 = $salesOrders->firstWhere('so_number', 'SO-00003');
        if ($so3) {
            $this->createOrderItems($so3, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 50,
                    'quantity_packed' => 50,
                    'quantity_shipped' => 50,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 100,
                    'quantity_picked' => 100,
                    'quantity_packed' => 100,
                    'quantity_shipped' => 100,
                ],
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_ordered' => 30,
                    'quantity_picked' => 30,
                    'quantity_packed' => 30,
                    'quantity_shipped' => 30,
                ],
                [
                    'product_sku' => 'SAF-EQ-002',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 50,
                    'quantity_packed' => 50,
                    'quantity_shipped' => 50,
                ],
            ], $products);
        }

        // ========================================
        // SO-00004 (SHIPPED) - VIP Electronics Order
        // ========================================
        $so4 = $salesOrders->firstWhere('so_number', 'SO-00004');
        if ($so4) {
            $this->createOrderItems($so4, [
                [
                    'product_sku' => 'ELC-SM-002',
                    'quantity_ordered' => 10,
                    'quantity_picked' => 10,
                    'quantity_packed' => 10,
                    'quantity_shipped' => 10,
                ],
                [
                    'product_sku' => 'ELC-LT-001',
                    'quantity_ordered' => 3,
                    'quantity_picked' => 3,
                    'quantity_packed' => 3,
                    'quantity_shipped' => 3,
                ],
                [
                    'product_sku' => 'ELC-LT-002',
                    'quantity_ordered' => 5,
                    'quantity_picked' => 5,
                    'quantity_packed' => 5,
                    'quantity_shipped' => 5,
                ],
            ], $products);
        }

        // ========================================
        // SO-00005 (SHIPPED) - FMCG Wholesale
        // ========================================
        $so5 = $salesOrders->firstWhere('so_number', 'SO-00005');
        if ($so5) {
            $this->createOrderItems($so5, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 120,
                    'quantity_picked' => 120,
                    'quantity_packed' => 120,
                    'quantity_shipped' => 120,
                ],
                [
                    'product_sku' => 'FNB-IN-002',
                    'quantity_ordered' => 100,
                    'quantity_picked' => 100,
                    'quantity_packed' => 100,
                    'quantity_shipped' => 100,
                ],
                [
                    'product_sku' => 'FNB-BV-001',
                    'quantity_ordered' => 200,
                    'quantity_picked' => 200,
                    'quantity_packed' => 200,
                    'quantity_shipped' => 200,
                ],
            ], $products);
        }

        // ========================================
        // SO-00006 (PACKING) - Small Retail Order
        // ========================================
        $so6 = $salesOrders->firstWhere('so_number', 'SO-00006');
        if ($so6) {
            $this->createOrderItems($so6, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 40,
                    'quantity_picked' => 40,
                    'quantity_packed' => 35, // Partially packed
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 80,
                    'quantity_picked' => 80,
                    'quantity_packed' => 80,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-PP-002',
                    'quantity_ordered' => 20,
                    'quantity_picked' => 20,
                    'quantity_packed' => 15, // Partially packed
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00007 (PACKING) - Regional Distribution
        // ========================================
        $so7 = $salesOrders->firstWhere('so_number', 'SO-00007');
        if ($so7) {
            $this->createOrderItems($so7, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 80,
                    'quantity_picked' => 80,
                    'quantity_packed' => 60, // Partially packed
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-001',
                    'quantity_ordered' => 150,
                    'quantity_picked' => 150,
                    'quantity_packed' => 150,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'CHM-LB-002',
                    'quantity_ordered' => 30,
                    'quantity_picked' => 30,
                    'quantity_packed' => 20, // Partially packed
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'SAF-EQ-001',
                    'quantity_ordered' => 25,
                    'quantity_picked' => 25,
                    'quantity_packed' => 25,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00008 (PICKING) - Minimarket Order
        // ========================================
        $so8 = $salesOrders->firstWhere('so_number', 'SO-00008');
        if ($so8) {
            $this->createOrderItems($so8, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 30,
                    'quantity_picked' => 25, // Partially picked
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 60,
                    'quantity_picked' => 60,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_ordered' => 15,
                    'quantity_picked' => 10, // Partially picked
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00009 (PICKING) - Kalimantan Order
        // ========================================
        $so9 = $salesOrders->firstWhere('so_number', 'SO-00009');
        if ($so9) {
            $this->createOrderItems($so9, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 60,
                    'quantity_picked' => 40, // Partially picked
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-001',
                    'quantity_ordered' => 100,
                    'quantity_picked' => 80, // Partially picked
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'CHM-LB-001',
                    'quantity_ordered' => 20,
                    'quantity_picked' => 15, // Partially picked
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'SPR-BR-001',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 50,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00010 (CONFIRMED) - VIP Repeat Order
        // ========================================
        $so10 = $salesOrders->firstWhere('so_number', 'SO-00010');
        if ($so10) {
            $this->createOrderItems($so10, [
                [
                    'product_sku' => 'ELC-SM-001',
                    'quantity_ordered' => 8,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'ELC-LT-002',
                    'quantity_ordered' => 4,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-FR-001',
                    'quantity_ordered' => 20,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00011 (CONFIRMED) - Electronics Padang
        // ========================================
        $so11 = $salesOrders->firstWhere('so_number', 'SO-00011');
        if ($so11) {
            $this->createOrderItems($so11, [
                [
                    'product_sku' => 'ELC-SM-002',
                    'quantity_ordered' => 3,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'ELC-LT-001',
                    'quantity_ordered' => 2,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00012 (DRAFT) - Medan Wholesale
        // ========================================
        $so12 = $salesOrders->firstWhere('so_number', 'SO-00012');
        if ($so12) {
            $this->createOrderItems($so12, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 100,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'CHM-LB-002',
                    'quantity_ordered' => 25,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00013 (DRAFT) - Banjarmasin Small Order
        // ========================================
        $so13 = $salesOrders->firstWhere('so_number', 'SO-00013');
        if ($so13) {
            $this->createOrderItems($so13, [
                [
                    'product_sku' => 'FNB-IN-002',
                    'quantity_ordered' => 30,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-001',
                    'quantity_ordered' => 50,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-PP-002',
                    'quantity_ordered' => 20,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00014 (DRAFT) - Lampung Mixed
        // ========================================
        $so14 = $salesOrders->firstWhere('so_number', 'SO-00014');
        if ($so14) {
            $this->createOrderItems($so14, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 70,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'SPR-BR-002',
                    'quantity_ordered' => 40,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'SAF-EQ-001',
                    'quantity_ordered' => 30,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'CHM-LB-001',
                    'quantity_ordered' => 15,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        // ========================================
        // SO-00015 (CANCELLED) - Malang Order
        // ========================================
        $so15 = $salesOrders->firstWhere('so_number', 'SO-00015');
        if ($so15) {
            $this->createOrderItems($so15, [
                [
                    'product_sku' => 'FNB-IN-001',
                    'quantity_ordered' => 40,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'FNB-BV-002',
                    'quantity_ordered' => 80,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
                [
                    'product_sku' => 'OFC-PP-001',
                    'quantity_ordered' => 25,
                    'quantity_picked' => 0,
                    'quantity_packed' => 0,
                    'quantity_shipped' => 0,
                ],
            ], $products);
        }

        $this->command->info('✓ Created sales order items successfully for all orders!');
    }

    /**
     * Create order items for a sales order
     */
    private function createOrderItems($salesOrder, $items, $products)
    {
        foreach ($items as $item) {
            $product = $products->firstWhere('sku', $item['product_sku']);
            
            if (!$product) {
                $this->command->warn("Product with SKU {$item['product_sku']} not found, skipping...");
                continue;
            }

            $unitPrice = $product->selling_price;
            $quantity = $item['quantity_ordered'];
            $taxRate = $product->is_taxable ? $product->tax_rate : 0;
            $discountRate = 0; // Bisa customize jika ada discount per item

            // Calculate line total
            $subtotal = $unitPrice * $quantity;
            $taxAmount = $subtotal * ($taxRate / 100);
            $discountAmount = $subtotal * ($discountRate / 100);
            $lineTotal = $subtotal + $taxAmount - $discountAmount;

            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $product->id,
                'quantity_ordered' => $quantity,
                'quantity_picked' => $item['quantity_picked'],
                'quantity_packed' => $item['quantity_packed'],
                'quantity_shipped' => $item['quantity_shipped'],
                'unit_price' => $unitPrice,
                'tax_rate' => $taxRate,
                'discount_rate' => $discountRate,
                'line_total' => $lineTotal,
                'unit_of_measure' => $product->unit->abbreviation ?? 'PCS',
                'notes' => $item['notes'] ?? null,
            ]);
        }
    }
}