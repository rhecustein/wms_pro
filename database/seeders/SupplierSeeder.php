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
                'code' => 'SUP-0011',
                'name' => 'PT Sumber Makmur',
                'company_name' => 'Sumber Makmur Indonesia',
                'email' => 'info@sumbermakmur.co.id',
                'phone' => '021-5551234',
                'mobile' => '0812-3456-7890',
                'address' => 'Jl. Industri No. 123',
                'city' => 'Jakarta',
                'state' => 'DKI Jakarta',
                'postal_code' => '12345',
                'country' => 'Indonesia',
                'tax_number' => '01.234.567.8-901.000',
                'payment_term_days' => 30,
                'payment_method' => 'Transfer',
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'PT Sumber Makmur',
                'rating' => 'A',
                'type' => 'distributor',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-002',
                'name' => 'CV Mitra Sejahtera',
                'company_name' => 'Mitra Sejahtera',
                'email' => 'contact@mitrasejahtera.com',
                'phone' => '031-7771234',
                'city' => 'Surabaya',
                'state' => 'Jawa Timur',
                'country' => 'Indonesia',
                'payment_term_days' => 45,
                'rating' => 'B',
                'type' => 'wholesaler',
                'is_active' => true,
            ],
            [
                'code' => 'SUP-003',
                'name' => 'PT Indo Manufacturing',
                'company_name' => 'Indo Manufacturing Corporation',
                'email' => 'sales@indomfg.co.id',
                'phone' => '022-8881234',
                'city' => 'Bandung',
                'state' => 'Jawa Barat',
                'country' => 'Indonesia',
                'payment_term_days' => 60,
                'rating' => 'A',
                'type' => 'manufacturer',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            // Gunakan updateOrCreate untuk update jika sudah ada, create jika belum
            Supplier::updateOrCreate(
                ['code' => $supplier['code']], // Kondisi pencarian
                $supplier // Data yang akan di-create atau di-update
            );
        }
    }
}