<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Parent Categories
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Produk elektronik dan gadget',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Smartphones',
                        'slug' => 'smartphones',
                        'description' => 'Telepon pintar dan aksesorisnya',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Laptops',
                        'slug' => 'laptops',
                        'description' => 'Laptop dan notebook',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Tablets',
                        'slug' => 'tablets',
                        'description' => 'Tablet dan iPad',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Accessories',
                        'slug' => 'electronics-accessories',
                        'description' => 'Aksesoris elektronik seperti charger, kabel, dll',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Food',
                'slug' => 'food',
                'description' => 'Produk makanan dan bahan makanan',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Instant Food',
                        'slug' => 'instant-food',
                        'description' => 'Makanan instan seperti mi instan, makanan kaleng',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Snacks',
                        'slug' => 'snacks',
                        'description' => 'Makanan ringan dan cemilan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Frozen Food',
                        'slug' => 'frozen-food',
                        'description' => 'Makanan beku',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Dairy Products',
                        'slug' => 'dairy-products',
                        'description' => 'Produk susu dan olahannya',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Beverage',
                'slug' => 'beverage',
                'description' => 'Minuman dan produk minuman',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Soft Drinks',
                        'slug' => 'soft-drinks',
                        'description' => 'Minuman ringan berkarbonasi',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Juices',
                        'slug' => 'juices',
                        'description' => 'Jus buah dan sayur',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Water',
                        'slug' => 'water',
                        'description' => 'Air minum kemasan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Energy Drinks',
                        'slug' => 'energy-drinks',
                        'description' => 'Minuman berenergi',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Produk otomotif dan spare parts',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Engine Oil',
                        'slug' => 'engine-oil',
                        'description' => 'Oli mesin dan pelumas',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Tires',
                        'slug' => 'tires',
                        'description' => 'Ban kendaraan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Spare Parts',
                        'slug' => 'spare-parts',
                        'description' => 'Suku cadang kendaraan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Car Care',
                        'slug' => 'car-care',
                        'description' => 'Produk perawatan mobil',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Furniture',
                'slug' => 'furniture',
                'description' => 'Furnitur dan perabotan',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Office Furniture',
                        'slug' => 'office-furniture',
                        'description' => 'Furnitur kantor',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Home Furniture',
                        'slug' => 'home-furniture',
                        'description' => 'Furnitur rumah',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Outdoor Furniture',
                        'slug' => 'outdoor-furniture',
                        'description' => 'Furnitur luar ruangan',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Chemical',
                'slug' => 'chemical',
                'description' => 'Produk kimia dan bahan kimia',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Cleaning Products',
                        'slug' => 'cleaning-products',
                        'description' => 'Produk pembersih',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Detergents',
                        'slug' => 'detergents',
                        'description' => 'Detergen dan sabun cuci',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Industrial Chemicals',
                        'slug' => 'industrial-chemicals',
                        'description' => 'Bahan kimia industri',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Textile',
                'slug' => 'textile',
                'description' => 'Produk tekstil dan pakaian',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Clothing',
                        'slug' => 'clothing',
                        'description' => 'Pakaian jadi',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Fabrics',
                        'slug' => 'fabrics',
                        'description' => 'Kain dan bahan tekstil',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Home Textiles',
                        'slug' => 'home-textiles',
                        'description' => 'Tekstil rumah tangga',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Pharmaceutical',
                'slug' => 'pharmaceutical',
                'description' => 'Produk farmasi dan obat-obatan',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Medicines',
                        'slug' => 'medicines',
                        'description' => 'Obat-obatan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Supplements',
                        'slug' => 'supplements',
                        'description' => 'Suplemen dan vitamin',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Medical Devices',
                        'slug' => 'medical-devices',
                        'description' => 'Alat kesehatan',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Building',
                'slug' => 'building',
                'description' => 'Material bangunan dan konstruksi',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Cement & Concrete',
                        'slug' => 'cement-concrete',
                        'description' => 'Semen dan beton',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Steel & Metal',
                        'slug' => 'steel-metal',
                        'description' => 'Besi dan logam',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Wood & Timber',
                        'slug' => 'wood-timber',
                        'description' => 'Kayu dan papan',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Paint & Coating',
                        'slug' => 'paint-coating',
                        'description' => 'Cat dan pelapis',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Stationery',
                'slug' => 'stationery',
                'description' => 'Alat tulis dan perlengkapan kantor',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Paper Products',
                        'slug' => 'paper-products',
                        'description' => 'Produk kertas',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Writing Instruments',
                        'slug' => 'writing-instruments',
                        'description' => 'Alat tulis',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Office Supplies',
                        'slug' => 'office-supplies',
                        'description' => 'Perlengkapan kantor',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Cosmetics',
                'slug' => 'cosmetics',
                'description' => 'Produk kecantikan dan kosmetik',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Skincare',
                        'slug' => 'skincare',
                        'description' => 'Perawatan kulit',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Makeup',
                        'slug' => 'makeup',
                        'description' => 'Produk makeup',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Haircare',
                        'slug' => 'haircare',
                        'description' => 'Perawatan rambut',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Fragrances',
                        'slug' => 'fragrances',
                        'description' => 'Parfum dan pewangi',
                        'is_active' => true,
                    ],
                ],
            ],
            
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'description' => 'Peralatan olahraga dan fitness',
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    [
                        'name' => 'Fitness Equipment',
                        'slug' => 'fitness-equipment',
                        'description' => 'Peralatan fitness',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Sports Apparel',
                        'slug' => 'sports-apparel',
                        'description' => 'Pakaian olahraga',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Outdoor Gear',
                        'slug' => 'outdoor-gear',
                        'description' => 'Perlengkapan outdoor',
                        'is_active' => true,
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            // Create parent category
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $parent = ProductCategory::create($categoryData);
            
            $this->command->info("Created parent category: {$parent->name}");
            
            // Create child categories
            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $child = ProductCategory::create($childData);
                
                $this->command->info("  └─ Created child category: {$child->name}");
            }
        }

        $this->command->info('Product categories seeded successfully!');
    }
}