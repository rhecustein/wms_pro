<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'children' => [
                    ['name' => 'Computers', 'description' => 'Desktop and laptop computers'],
                    ['name' => 'Smartphones', 'description' => 'Mobile phones and tablets'],
                    ['name' => 'Accessories', 'description' => 'Electronic accessories'],
                ]
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Office equipment and stationery',
                'children' => [
                    ['name' => 'Stationery', 'description' => 'Paper, pens, and office supplies'],
                    ['name' => 'Furniture', 'description' => 'Office furniture'],
                ]
            ],
            [
                'name' => 'Raw Materials',
                'description' => 'Materials for production',
                'children' => [
                    ['name' => 'Metals', 'description' => 'Metal materials'],
                    ['name' => 'Plastics', 'description' => 'Plastic materials'],
                    ['name' => 'Chemicals', 'description' => 'Chemical materials'],
                ]
            ],
            [
                'name' => 'Spare Parts',
                'description' => 'Replacement parts and components',
            ],
            [
                'name' => 'Consumables',
                'description' => 'Consumable items',
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $category = ProductCategory::create($categoryData);
            
            foreach ($children as $childData) {
                $childData['parent_id'] = $category->id;
                ProductCategory::create($childData);
            }
        }
    }
}