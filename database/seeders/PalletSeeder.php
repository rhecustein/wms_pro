<?php

namespace Database\Seeders;

use App\Models\Pallet;
use App\Models\StorageBin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil storage bins yang available atau occupied
        $availableBins = StorageBin::whereIn('status', ['available', 'occupied'])
            ->where('is_active', true)
            ->get();

        if ($availableBins->isEmpty()) {
            $this->command->warn('Tidak ada storage bins yang tersedia. Jalankan StorageBinSeeder terlebih dahulu.');
            return;
        }

        // Ambil user pertama sebagai creator (optional)
        $user = User::first();

        $pallets = [];
        $palletCount = 500; // Total pallet yang akan dibuat

        for ($i = 1; $i <= $palletCount; $i++) {
            $palletNumber = 'PLT-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            // Random pallet type
            $palletType = $this->getRandomPalletType();
            
            // Dimensi berdasarkan type
            $dimensions = $this->getPalletDimensions($palletType);
            
            // Random status
            $status = $this->getRandomStatus();
            
            // Tentukan storage bin (60% dialokasikan ke bin, 40% tidak)
            $storageBinId = null;
            if (rand(1, 100) <= 60 && $availableBins->isNotEmpty()) {
                $storageBinId = $availableBins->random()->id;
            }
            
            // Current weight berdasarkan status
            $currentWeight = 0;
            if ($status === 'loaded') {
                $currentWeight = rand(100, (int)$dimensions['max_weight']);
            }
            
            // Condition berdasarkan status
            $condition = $this->getCondition($status);
            
            // Is available
            $isAvailable = in_array($status, ['empty', 'loaded']);
            
            // Last used date (80% punya history)
            $lastUsedDate = null;
            if (rand(1, 100) <= 80) {
                $lastUsedDate = now()->subDays(rand(1, 365));
            }
            
            $pallets[] = [
                'pallet_number' => $palletNumber,
                'pallet_type' => $palletType,
                'barcode' => 'BC-' . strtoupper(Str::random(12)),
                'qr_code' => 'QR-' . strtoupper(Str::random(16)),
                'width_cm' => $dimensions['width'],
                'depth_cm' => $dimensions['depth'],
                'height_cm' => $dimensions['height'],
                'max_weight_kg' => $dimensions['max_weight'],
                'current_weight_kg' => $currentWeight,
                'storage_bin_id' => $storageBinId,
                'status' => $status,
                'is_available' => $isAvailable,
                'last_used_date' => $lastUsedDate,
                'condition' => $condition,
                'notes' => $this->getRandomNotes($status, $condition),
                'created_by' => $user?->id,
                'updated_by' => $user?->id,
                'created_at' => now()->subDays(rand(1, 180)),
                'updated_at' => now()->subDays(rand(0, 30)),
            ];
        }

        // Insert pallets in chunks untuk performa
        $chunks = array_chunk($pallets, 100);
        foreach ($chunks as $chunk) {
            Pallet::insert($chunk);
        }

        $this->command->info("Created {$palletCount} pallets successfully!");
        
        // Tampilkan statistik
        $this->displayStatistics();
    }

    /**
     * Get random pallet type with distribution
     */
    private function getRandomPalletType(): string
    {
        $types = [
            'standard' => 70,  // 70% standard
            'euro' => 20,      // 20% euro
            'custom' => 10,    // 10% custom
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($types as $type => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $type;
            }
        }

        return 'standard';
    }

    /**
     * Get pallet dimensions based on type
     */
    private function getPalletDimensions(string $type): array
    {
        return match($type) {
            'standard' => [
                'width' => 120.00,
                'depth' => 120.00,
                'height' => 16.00,
                'max_weight' => 1200.00,
            ],
            'euro' => [
                'width' => 120.00,
                'depth' => 80.00,
                'height' => 14.40,
                'max_weight' => 1500.00,
            ],
            'custom' => [
                'width' => rand(80, 150),
                'depth' => rand(80, 150),
                'height' => rand(12, 20),
                'max_weight' => rand(800, 2000),
            ],
        };
    }

    /**
     * Get random status for pallet
     */
    private function getRandomStatus(): string
    {
        $statuses = [
            'empty' => 50,      // 50% empty
            'loaded' => 35,     // 35% loaded
            'in_transit' => 10, // 10% in transit
            'damaged' => 5,     // 5% damaged
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($statuses as $status => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $status;
            }
        }

        return 'empty';
    }

    /**
     * Get condition based on status
     */
    private function getCondition(string $status): string
    {
        if ($status === 'damaged') {
            return ['damaged', 'poor'][array_rand(['damaged', 'poor'])];
        }

        $conditions = [
            'good' => 60,   // 60% good
            'fair' => 30,   // 30% fair
            'poor' => 10,   // 10% poor
        ];

        $random = rand(1, 100);
        $cumulative = 0;

        foreach ($conditions as $condition => $percentage) {
            $cumulative += $percentage;
            if ($random <= $cumulative) {
                return $condition;
            }
        }

        return 'good';
    }

    /**
     * Get random notes based on status and condition
     */
    private function getRandomNotes(?string $status, string $condition): ?string
    {
        // 70% tidak punya notes
        if (rand(1, 100) <= 70) {
            return null;
        }

        $notes = [];

        if ($status === 'damaged') {
            $notes = [
                'Pallet rusak pada bagian sudut kanan',
                'Terdapat keretakan pada papan',
                'Perlu perbaikan sebelum digunakan kembali',
                'Rusak akibat beban berlebih',
            ];
        } elseif ($condition === 'poor') {
            $notes = [
                'Kondisi kurang baik, perlu inspeksi',
                'Beberapa bagian aus',
                'Direkomendasikan untuk tidak muat barang berat',
            ];
        } elseif ($condition === 'fair') {
            $notes = [
                'Kondisi cukup baik untuk penggunaan normal',
                'Sudah digunakan cukup lama',
            ];
        } else {
            $notes = [
                'Pallet dalam kondisi baik',
                'Baru dari supplier',
                'Sudah diinspeksi dan layak pakai',
            ];
        }

        return $notes[array_rand($notes)];
    }

    /**
     * Display pallet statistics
     */
    private function displayStatistics(): void
    {
        $total = Pallet::count();
        $byStatus = Pallet::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        $byType = Pallet::selectRaw('pallet_type, COUNT(*) as count')
            ->groupBy('pallet_type')
            ->get()
            ->pluck('count', 'pallet_type');
        
        $byCondition = Pallet::selectRaw('`condition`, COUNT(*) as count')
            ->groupBy('condition')
            ->get()
            ->pluck('count', 'condition');

        $this->command->info("\n=== Pallet Statistics ===");
        $this->command->info("Total Pallets: {$total}");
        
        $this->command->info("\nBy Status:");
        foreach ($byStatus as $status => $count) {
            $this->command->info("  - {$status}: {$count}");
        }
        
        $this->command->info("\nBy Type:");
        foreach ($byType as $type => $count) {
            $this->command->info("  - {$type}: {$count}");
        }
        
        $this->command->info("\nBy Condition:");
        foreach ($byCondition as $condition => $count) {
            $this->command->info("  - {$condition}: {$count}");
        }
    }
}