<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $createdBy = $adminUser ? $adminUser->id : null;

        // Ambil categories, units, dan suppliers
        $categories = ProductCategory::all();
        $units = Unit::all();
        $suppliers = Supplier::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Tidak ada product category. Jalankan ProductCategorySeeder terlebih dahulu.');
            return;
        }

        if ($units->isEmpty()) {
            $this->command->warn('Tidak ada units. Jalankan UnitSeeder terlebih dahulu.');
            return;
        }

        $products = [
            // Electronics
            [
                'sku' => 'ELC-001',
                'barcode' => '8991234567001',
                'name' => 'Samsung Galaxy S24',
                'description' => 'Smartphone flagship dengan kamera 200MP dan layar AMOLED 6.8 inch',
                'category_id' => $this->getCategoryId($categories, 'Smartphones'),
                'brand' => 'Samsung',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Electronics Supplier'),
                'purchase_price' => 12000000.00,
                'selling_price' => 14500000.00,
                'minimum_selling_price' => 13500000.00,
                'minimum_stock' => 30,
                'maximum_stock' => 500,
                'reorder_level' => 50,
                'current_stock' => 150,
                'weight' => 0.23,
                'length' => 16.20,
                'width' => 7.60,
                'height' => 0.79,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'finished_goods',
                'is_active' => true,
                'is_serialized' => true,
                'is_batch_tracked' => true,
                'notes' => 'High-end smartphone with premium features',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'ELC-002',
                'barcode' => '8991234567002',
                'name' => 'MacBook Pro 16" M3',
                'description' => 'Laptop profesional dengan chip M3 dan RAM 32GB',
                'category_id' => $this->getCategoryId($categories, 'Computers'),
                'brand' => 'Apple',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Electronics Supplier'),
                'purchase_price' => 35000000.00,
                'selling_price' => 42000000.00,
                'minimum_selling_price' => 40000000.00,
                'minimum_stock' => 10,
                'maximum_stock' => 200,
                'reorder_level' => 20,
                'current_stock' => 45,
                'weight' => 2.15,
                'length' => 35.57,
                'width' => 24.81,
                'height' => 1.55,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'finished_goods',
                'is_active' => true,
                'is_serialized' => true,
                'is_batch_tracked' => true,
                'notes' => 'Professional laptop for creative work',
                'created_by' => $createdBy,
            ],

            // Food & Beverage (sebagai finished goods)
            [
                'sku' => 'FNB-001',
                'barcode' => '8992234567001',
                'name' => 'Indomie Goreng 85g',
                'description' => 'Mi instan goreng original',
                'category_id' => $this->getCategoryId($categories, 'Consumables'),
                'brand' => 'Indomie',
                'unit_id' => $this->getUnitId($units, 'Box'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Food Supplier'),
                'purchase_price' => 2500.00,
                'selling_price' => 3200.00,
                'minimum_selling_price' => 2800.00,
                'minimum_stock' => 300,
                'maximum_stock' => 10000,
                'reorder_level' => 500,
                'current_stock' => 2500,
                'weight' => 0.085,
                'length' => 18.00,
                'width' => 8.00,
                'height' => 6.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'consumable',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Expiry date tracking required',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'FNB-002',
                'barcode' => '8992234567002',
                'name' => 'Coca Cola 330ml Can',
                'description' => 'Minuman berkarbonasi kemasan kaleng',
                'category_id' => $this->getCategoryId($categories, 'Consumables'),
                'brand' => 'Coca Cola',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Food Supplier'),
                'purchase_price' => 4500.00,
                'selling_price' => 6000.00,
                'minimum_selling_price' => 5500.00,
                'minimum_stock' => 500,
                'maximum_stock' => 20000,
                'reorder_level' => 1000,
                'current_stock' => 5000,
                'weight' => 0.35,
                'length' => 6.60,
                'width' => 6.60,
                'height' => 12.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'consumable',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Refrigerated storage recommended',
                'created_by' => $createdBy,
            ],

            // Raw Materials
            [
                'sku' => 'RAW-001',
                'barcode' => '8993234567001',
                'name' => 'Steel Plate 1mm',
                'description' => 'Plat baja lembaran 1mm ketebalan',
                'category_id' => $this->getCategoryId($categories, 'Metals'),
                'brand' => 'BlueScope',
                'unit_id' => $this->getUnitId($units, 'Sheet'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Material Supplier'),
                'purchase_price' => 150000.00,
                'selling_price' => 185000.00,
                'minimum_selling_price' => 170000.00,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_level' => 100,
                'current_stock' => 200,
                'weight' => 7.85,
                'length' => 240.00,
                'width' => 120.00,
                'height' => 0.10,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'raw_material',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'For manufacturing purposes',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'RAW-002',
                'barcode' => '8993234567002',
                'name' => 'Plastic Pellets HDPE',
                'description' => 'High-density polyethylene pellets',
                'category_id' => $this->getCategoryId($categories, 'Plastics'),
                'brand' => 'Dow Chemical',
                'unit_id' => $this->getUnitId($units, 'Kilogram'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Material Supplier'),
                'purchase_price' => 25000.00,
                'selling_price' => 32000.00,
                'minimum_selling_price' => 28000.00,
                'minimum_stock' => 500,
                'maximum_stock' => 5000,
                'reorder_level' => 1000,
                'current_stock' => 2000,
                'weight' => 1.00,
                'length' => 50.00,
                'width' => 30.00,
                'height' => 10.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'raw_material',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Store in dry place',
                'created_by' => $createdBy,
            ],

            // Spare Parts
            [
                'sku' => 'SPR-001',
                'barcode' => '8994234567001',
                'name' => 'Motor Bearing 6205',
                'description' => 'Ball bearing untuk motor industri',
                'category_id' => $this->getCategoryId($categories, 'Spare Parts'),
                'brand' => 'SKF',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Parts Supplier'),
                'purchase_price' => 45000.00,
                'selling_price' => 65000.00,
                'minimum_selling_price' => 55000.00,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_level' => 100,
                'current_stock' => 150,
                'weight' => 0.15,
                'length' => 5.20,
                'width' => 5.20,
                'height' => 1.50,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'spare_parts',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Common replacement part',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'SPR-002',
                'barcode' => '8994234567002',
                'name' => 'V-Belt A53',
                'description' => 'Sabuk V untuk mesin industri',
                'category_id' => $this->getCategoryId($categories, 'Spare Parts'),
                'brand' => 'Gates',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Parts Supplier'),
                'purchase_price' => 75000.00,
                'selling_price' => 105000.00,
                'minimum_selling_price' => 90000.00,
                'minimum_stock' => 30,
                'maximum_stock' => 300,
                'reorder_level' => 50,
                'current_stock' => 100,
                'weight' => 0.35,
                'length' => 135.00,
                'width' => 1.30,
                'height' => 0.80,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'spare_parts',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => false,
                'notes' => 'Standard industrial belt',
                'created_by' => $createdBy,
            ],

            // Office Supplies
            [
                'sku' => 'OFC-001',
                'barcode' => '8995234567001',
                'name' => 'Kertas A4 80gsm',
                'description' => 'Kertas fotokopi A4 80gsm isi 500 lembar',
                'category_id' => $this->getCategoryId($categories, 'Stationery'),
                'brand' => 'Sinar Dunia',
                'unit_id' => $this->getUnitId($units, 'Ream'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Office Supplier'),
                'purchase_price' => 35000.00,
                'selling_price' => 45000.00,
                'minimum_selling_price' => 40000.00,
                'minimum_stock' => 100,
                'maximum_stock' => 1000,
                'reorder_level' => 200,
                'current_stock' => 350,
                'weight' => 2.50,
                'length' => 30.00,
                'width' => 21.00,
                'height' => 5.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'consumable',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => false,
                'notes' => 'Standard office paper',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'OFC-002',
                'barcode' => '8995234567002',
                'name' => 'Kursi Kantor Ergonomis',
                'description' => 'Kursi kantor dengan sandaran punggung mesh',
                'category_id' => $this->getCategoryId($categories, 'Furniture'),
                'brand' => 'Ergotec',
                'unit_id' => $this->getUnitId($units, 'Piece'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Office Supplier'),
                'purchase_price' => 1200000.00,
                'selling_price' => 1650000.00,
                'minimum_selling_price' => 1450000.00,
                'minimum_stock' => 10,
                'maximum_stock' => 200,
                'reorder_level' => 20,
                'current_stock' => 45,
                'weight' => 15.00,
                'length' => 70.00,
                'width' => 70.00,
                'height' => 120.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'finished_goods',
                'is_active' => true,
                'is_serialized' => true,
                'is_batch_tracked' => false,
                'notes' => 'Adjustable height and armrests',
                'created_by' => $createdBy,
            ],

            // Chemicals
            [
                'sku' => 'CHM-001',
                'barcode' => '8996234567001',
                'name' => 'Oli Mesin Shell Helix',
                'description' => 'Oli mesin sintetik SAE 10W-40',
                'category_id' => $this->getCategoryId($categories, 'Chemicals'),
                'brand' => 'Shell',
                'unit_id' => $this->getUnitId($units, 'Liter'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Chemical Supplier'),
                'purchase_price' => 85000.00,
                'selling_price' => 120000.00,
                'minimum_selling_price' => 105000.00,
                'minimum_stock' => 100,
                'maximum_stock' => 2000,
                'reorder_level' => 200,
                'current_stock' => 500,
                'weight' => 0.95,
                'length' => 10.00,
                'width' => 10.00,
                'height' => 20.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'consumable',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Hazardous material - handle with care',
                'created_by' => $createdBy,
            ],
            [
                'sku' => 'CHM-002',
                'barcode' => '8996234567002',
                'name' => 'Detergen Bubuk Industrial',
                'description' => 'Detergen bubuk untuk laundry industri 25kg',
                'category_id' => $this->getCategoryId($categories, 'Chemicals'),
                'brand' => 'Industrial Clean',
                'unit_id' => $this->getUnitId($units, 'Kilogram'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Chemical Supplier'),
                'purchase_price' => 180000.00,
                'selling_price' => 245000.00,
                'minimum_selling_price' => 220000.00,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_level' => 100,
                'current_stock' => 200,
                'weight' => 25.00,
                'length' => 50.00,
                'width' => 35.00,
                'height' => 15.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'consumable',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Store in dry place away from food',
                'created_by' => $createdBy,
            ],

            // Finished Goods
            [
                'sku' => 'FIN-001',
                'barcode' => '8997234567001',
                'name' => 'Sepatu Safety Steel Toe',
                'description' => 'Sepatu keselamatan kerja dengan steel toe',
                'category_id' => $this->getCategoryId($categories, 'Consumables'),
                'brand' => 'Safety First',
                'unit_id' => $this->getUnitId($units, 'Pair'),
                'supplier_id' => $this->getSupplierId($suppliers, 'Safety Equipment Supplier'),
                'purchase_price' => 250000.00,
                'selling_price' => 350000.00,
                'minimum_selling_price' => 300000.00,
                'minimum_stock' => 50,
                'maximum_stock' => 500,
                'reorder_level' => 100,
                'current_stock' => 180,
                'weight' => 1.20,
                'length' => 30.00,
                'width' => 15.00,
                'height' => 12.00,
                'is_taxable' => true,
                'tax_rate' => 11.00,
                'type' => 'finished_goods',
                'is_active' => true,
                'is_serialized' => false,
                'is_batch_tracked' => true,
                'notes' => 'Multiple sizes available',
                'created_by' => $createdBy,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('Created ' . count($products) . ' products successfully!');
    }

    /**
     * Get category ID by name
     */
    private function getCategoryId($categories, $categoryName): ?int
    {
        $category = $categories->firstWhere('name', $categoryName);
        return $category ? $category->id : null;
    }

    /**
     * Get unit ID by name
     */
    private function getUnitId($units, $unitName): ?int
    {
        $unit = $units->firstWhere('name', $unitName);
        return $unit ? $unit->id : $units->first()?->id;
    }

    /**
     * Get supplier ID by name
     */
    private function getSupplierId($suppliers, $supplierName): ?int
    {
        if ($suppliers->isEmpty()) {
            return null;
        }
        $supplier = $suppliers->firstWhere('name', $supplierName);
        return $supplier ? $supplier->id : $suppliers->first()?->id;
    }
}