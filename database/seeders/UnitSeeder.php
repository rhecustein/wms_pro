<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Base Units (units that don't have conversion)
        $baseUnits = [
            // Quantity (Piece-based)
            [
                'name' => 'Piece',
                'short_code' => 'PCS',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => null,
                'description' => 'Single piece or unit - base unit for counting',
                'is_active' => true,
            ],
            
            // Weight
            [
                'name' => 'Kilogram',
                'short_code' => 'KG',
                'type' => 'weight',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => null,
                'description' => 'Kilogram - base unit for weight',
                'is_active' => true,
            ],
            
            // Volume
            [
                'name' => 'Liter',
                'short_code' => 'L',
                'type' => 'volume',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => null,
                'description' => 'Liter - base unit for volume',
                'is_active' => true,
            ],
            
            // Length
            [
                'name' => 'Meter',
                'short_code' => 'M',
                'type' => 'length',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => null,
                'description' => 'Meter - base unit for length',
                'is_active' => true,
            ],
        ];

        // Create base units first
        foreach ($baseUnits as $unitData) {
            Unit::create($unitData);
        }

        // Get base units for reference
        $pieceUnit = Unit::where('short_code', 'PCS')->first();
        $kgUnit = Unit::where('short_code', 'KG')->first();
        $literUnit = Unit::where('short_code', 'L')->first();
        $meterUnit = Unit::where('short_code', 'M')->first();

        // Derived Units (units that have conversion to base units)
        $derivedUnits = [
            // Piece-based conversions
            [
                'name' => 'Box',
                'short_code' => 'BOX',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Box container (1 box = 1 piece for inventory)',
                'is_active' => true,
            ],
            [
                'name' => 'Carton',
                'short_code' => 'CTN',
                'type' => 'base',
                'base_unit_conversion' => 12.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Carton box (1 carton = 12 pieces)',
                'is_active' => true,
            ],
            [
                'name' => 'Dozen',
                'short_code' => 'DOZ',
                'type' => 'base',
                'base_unit_conversion' => 12.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Dozen (1 dozen = 12 pieces)',
                'is_active' => true,
            ],
            [
                'name' => 'Pair',
                'short_code' => 'PR',
                'type' => 'base',
                'base_unit_conversion' => 2.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Pair of items (1 pair = 2 pieces)',
                'is_active' => true,
            ],
            [
                'name' => 'Set',
                'short_code' => 'SET',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Set of items',
                'is_active' => true,
            ],
            [
                'name' => 'Pack',
                'short_code' => 'PACK',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Package of items',
                'is_active' => true,
            ],
            [
                'name' => 'Ream',
                'short_code' => 'RM',
                'type' => 'base',
                'base_unit_conversion' => 500.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Ream (1 ream = 500 sheets)',
                'is_active' => true,
            ],
            [
                'name' => 'Sheet',
                'short_code' => 'SHT',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Sheet or panel',
                'is_active' => true,
            ],
            [
                'name' => 'Roll',
                'short_code' => 'ROLL',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Roll of material',
                'is_active' => true,
            ],
            [
                'name' => 'Unit',
                'short_code' => 'UNIT',
                'type' => 'base',
                'base_unit_conversion' => 1.0000,
                'base_unit_id' => $pieceUnit->id,
                'description' => 'Generic unit',
                'is_active' => true,
            ],

            // Weight conversions
            [
                'name' => 'Gram',
                'short_code' => 'G',
                'type' => 'weight',
                'base_unit_conversion' => 0.0010,
                'base_unit_id' => $kgUnit->id,
                'description' => 'Gram (1000 grams = 1 kilogram)',
                'is_active' => true,
            ],
            [
                'name' => 'Ton',
                'short_code' => 'TON',
                'type' => 'weight',
                'base_unit_conversion' => 1000.0000,
                'base_unit_id' => $kgUnit->id,
                'description' => 'Metric ton (1 ton = 1000 kilograms)',
                'is_active' => true,
            ],
            [
                'name' => 'Quintal',
                'short_code' => 'QTL',
                'type' => 'weight',
                'base_unit_conversion' => 100.0000,
                'base_unit_id' => $kgUnit->id,
                'description' => 'Quintal (1 quintal = 100 kilograms)',
                'is_active' => true,
            ],

            // Volume conversions
            [
                'name' => 'Milliliter',
                'short_code' => 'ML',
                'type' => 'volume',
                'base_unit_conversion' => 0.0010,
                'base_unit_id' => $literUnit->id,
                'description' => 'Milliliter (1000 milliliters = 1 liter)',
                'is_active' => true,
            ],
            [
                'name' => 'Gallon',
                'short_code' => 'GAL',
                'type' => 'volume',
                'base_unit_conversion' => 3.7854,
                'base_unit_id' => $literUnit->id,
                'description' => 'US Gallon (1 gallon ≈ 3.785 liters)',
                'is_active' => true,
            ],
            [
                'name' => 'Cubic Meter',
                'short_code' => 'M3',
                'type' => 'volume',
                'base_unit_conversion' => 1000.0000,
                'base_unit_id' => $literUnit->id,
                'description' => 'Cubic meter (1 m³ = 1000 liters)',
                'is_active' => true,
            ],

            // Length conversions
            [
                'name' => 'Centimeter',
                'short_code' => 'CM',
                'type' => 'length',
                'base_unit_conversion' => 0.0100,
                'base_unit_id' => $meterUnit->id,
                'description' => 'Centimeter (100 centimeters = 1 meter)',
                'is_active' => true,
            ],
            [
                'name' => 'Millimeter',
                'short_code' => 'MM',
                'type' => 'length',
                'base_unit_conversion' => 0.0010,
                'base_unit_id' => $meterUnit->id,
                'description' => 'Millimeter (1000 millimeters = 1 meter)',
                'is_active' => true,
            ],
            [
                'name' => 'Kilometer',
                'short_code' => 'KM',
                'type' => 'length',
                'base_unit_conversion' => 1000.0000,
                'base_unit_id' => $meterUnit->id,
                'description' => 'Kilometer (1 kilometer = 1000 meters)',
                'is_active' => true,
            ],
            [
                'name' => 'Inch',
                'short_code' => 'IN',
                'type' => 'length',
                'base_unit_conversion' => 0.0254,
                'base_unit_id' => $meterUnit->id,
                'description' => 'Inch (1 inch ≈ 0.0254 meters)',
                'is_active' => true,
            ],
            [
                'name' => 'Foot',
                'short_code' => 'FT',
                'type' => 'length',
                'base_unit_conversion' => 0.3048,
                'base_unit_id' => $meterUnit->id,
                'description' => 'Foot (1 foot ≈ 0.3048 meters)',
                'is_active' => true,
            ],
        ];

        // Create derived units
        foreach ($derivedUnits as $unitData) {
            Unit::create($unitData);
        }

        $this->command->info('Created ' . (count($baseUnits) + count($derivedUnits)) . ' units successfully!');
        $this->command->info('Base units: ' . count($baseUnits));
        $this->command->info('Derived units: ' . count($derivedUnits));
    }
}