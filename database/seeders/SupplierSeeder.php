<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-001',
                'name' => 'Electronics Supplier',
                'contact_person' => 'John Doe',
                'email' => 'john@electronics.com',
                'phone' => '081234567890',
                'address' => 'Jl. Elektronik No. 123, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-002',
                'name' => 'Food Supplier',
                'contact_person' => 'Jane Smith',
                'email' => 'jane@food.com',
                'phone' => '081234567891',
                'address' => 'Jl. Pangan No. 456, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-003',
                'name' => 'Material Supplier',
                'contact_person' => 'Bob Wilson',
                'email' => 'bob@materials.com',
                'phone' => '081234567892',
                'address' => 'Jl. Material No. 789, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-004',
                'name' => 'Parts Supplier',
                'contact_person' => 'Alice Brown',
                'email' => 'alice@parts.com',
                'phone' => '081234567893',
                'address' => 'Jl. Spare Part No. 101, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-005',
                'name' => 'Office Supplier',
                'contact_person' => 'Charlie Davis',
                'email' => 'charlie@office.com',
                'phone' => '081234567894',
                'address' => 'Jl. Kantor No. 202, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-006',
                'name' => 'Chemical Supplier',
                'contact_person' => 'Diana Evans',
                'email' => 'diana@chemical.com',
                'phone' => '081234567895',
                'address' => 'Jl. Kimia No. 303, Jakarta',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-007',
                'name' => 'Safety Equipment Supplier',
                'contact_person' => 'Frank Green',
                'email' => 'frank@safety.com',
                'phone' => '081234567896',
                'address' => 'Jl. Safety No. 404, Jakarta',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}