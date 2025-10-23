<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StorageBin;
use App\Models\Warehouse;
use App\Models\StorageArea;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StorageBinController extends Controller
{
    // No middleware - without role

    /**
     * Display a listing of storage bins
     */
    public function index(Request $request)
    {
        $query = StorageBin::with(['warehouse', 'storageArea', 'customer']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('aisle', 'like', "%{$search}%")
                    ->orWhere('row', 'like', "%{$search}%")
                    ->orWhere('column', 'like', "%{$search}%")
                    ->orWhere('level', 'like', "%{$search}%");
            });
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Storage Area filter
        if ($request->filled('storage_area_id')) {
            $query->where('storage_area_id', $request->storage_area_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Bin Type filter
        if ($request->filled('bin_type')) {
            $query->where('bin_type', $request->bin_type);
        }

        // Active filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Hazmat filter
        if ($request->filled('is_hazmat')) {
            $query->where('is_hazmat', $request->is_hazmat);
        }

        $storageBins = $query->latest()->paginate(20)->withQueryString();
        
        // Get filter options
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $statuses = ['available', 'occupied', 'reserved', 'blocked', 'maintenance'];
        $binTypes = ['pick_face', 'high_rack', 'staging', 'quarantine'];

        return view('master.storage-bins.index', compact(
            'storageBins',
            'warehouses',
            'storageAreas',
            'statuses',
            'binTypes'
        ));
    }

    /**
     * Show the form for creating a new storage bin
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('master.storage-bins.create', compact('warehouses', 'storageAreas', 'customers'));
    }

    /**
     * Store a newly created storage bin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle' => 'required|string|max:10',
            'row' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'status' => 'required|in:available,occupied,reserved,blocked,maintenance',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
            'packaging_restriction' => 'nullable|in:none,drum,carton,pallet',
            'customer_id' => 'nullable|exists:customers,id',
            'is_hazmat' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate code
        $validated['code'] = strtoupper(
            $validated['aisle'] . 
            $validated['row'] . 
            $validated['column'] . 
            $validated['level']
        );

        // Check if code already exists
        if (StorageBin::where('code', $validated['code'])->exists()) {
            return back()->withInput()->withErrors(['code' => 'Storage bin code already exists.']);
        }

        $storageBin = StorageBin::create($validated);

        return redirect()
            ->route('master.storage-bins.show', $storageBin)
            ->with('success', 'Storage bin created successfully.');
    }

    /**
     * Display the specified storage bin
     */
    public function show(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea', 'customer']);
        
        return view('master.storage-bins.show', compact('storageBin'));
    }

    /**
     * Show the form for editing the specified storage bin
     */
    public function edit(StorageBin $storageBin)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $storageAreas = StorageArea::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        
        return view('master.storage-bins.edit', compact('storageBin', 'warehouses', 'storageAreas', 'customers'));
    }

    /**
     * Update the specified storage bin
     */
    public function update(Request $request, StorageBin $storageBin)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle' => 'required|string|max:10',
            'row' => 'required|string|max:10',
            'column' => 'required|string|max:10',
            'level' => 'required|string|max:10',
            'status' => 'required|in:available,occupied,reserved,blocked,maintenance',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
            'packaging_restriction' => 'nullable|in:none,drum,carton,pallet',
            'customer_id' => 'nullable|exists:customers,id',
            'is_hazmat' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Generate code
        $newCode = strtoupper(
            $validated['aisle'] . 
            $validated['row'] . 
            $validated['column'] . 
            $validated['level']
        );

        // Check if code already exists (except current bin)
        if (StorageBin::where('code', $newCode)->where('id', '!=', $storageBin->id)->exists()) {
            return back()->withInput()->withErrors(['code' => 'Storage bin code already exists.']);
        }

        $validated['code'] = $newCode;
        $storageBin->update($validated);

        return redirect()
            ->route('master.storage-bins.show', $storageBin)
            ->with('success', 'Storage bin updated successfully.');
    }

    /**
     * Remove the specified storage bin
     */
    public function destroy(StorageBin $storageBin)
    {
        // Check if bin has current stock
        if ($storageBin->current_quantity > 0) {
            return back()->with('error', 'Cannot delete storage bin with existing stock.');
        }

        $storageBin->delete();

        return redirect()
            ->route('master.storage-bins.index')
            ->with('success', 'Storage bin deleted successfully.');
    }

    /**
     * Get bins by storage area
     */
    public function byStorageArea(StorageArea $storageArea)
    {
        $bins = $storageArea->storageBins()
            ->with(['warehouse'])
            ->orderBy('code')
            ->get();

        return view('master.storage-bins.by-area', compact('storageArea', 'bins'));
    }

    /**
     * Generate multiple storage bins
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'storage_area_id' => 'nullable|exists:storage_areas,id',
            'aisle_start' => 'required|string|max:10',
            'aisle_end' => 'required|string|max:10',
            'row_start' => 'required|integer|min:1',
            'row_end' => 'required|integer|min:1|gte:row_start',
            'column_start' => 'required|integer|min:1',
            'column_end' => 'required|integer|min:1|gte:column_start',
            'level_start' => 'required|string|max:10',
            'level_end' => 'required|string|max:10',
            'bin_type' => 'required|in:pick_face,high_rack,staging,quarantine',
            'max_weight_kg' => 'nullable|numeric|min:0',
            'max_volume_cbm' => 'nullable|numeric|min:0',
        ]);

        $generated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            // Generate aisles range
            $aisles = $this->generateRange($validated['aisle_start'], $validated['aisle_end']);
            $levels = $this->generateRange($validated['level_start'], $validated['level_end']);

            foreach ($aisles as $aisle) {
                for ($row = $validated['row_start']; $row <= $validated['row_end']; $row++) {
                    for ($column = $validated['column_start']; $column <= $validated['column_end']; $column++) {
                        foreach ($levels as $level) {
                            $code = strtoupper($aisle . str_pad($row, 2, '0', STR_PAD_LEFT) . str_pad($column, 2, '0', STR_PAD_LEFT) . $level);

                            // Skip if exists
                            if (StorageBin::where('code', $code)->exists()) {
                                $skipped++;
                                continue;
                            }

                            StorageBin::create([
                                'warehouse_id' => $validated['warehouse_id'],
                                'storage_area_id' => $validated['storage_area_id'],
                                'code' => $code,
                                'aisle' => $aisle,
                                'row' => str_pad($row, 2, '0', STR_PAD_LEFT),
                                'column' => str_pad($column, 2, '0', STR_PAD_LEFT),
                                'level' => $level,
                                'status' => 'available',
                                'bin_type' => $validated['bin_type'],
                                'max_weight_kg' => $validated['max_weight_kg'],
                                'max_volume_cbm' => $validated['max_volume_cbm'],
                                'is_active' => true,
                            ]);

                            $generated++;
                        }
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('master.storage-bins.index')
                ->with('success', "Generated {$generated} storage bins successfully. Skipped {$skipped} existing bins.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error generating storage bins: ' . $e->getMessage());
        }
    }

    /**
     * Activate storage bin
     */
    public function activate(StorageBin $storageBin)
    {
        $storageBin->update(['is_active' => true]);

        return back()->with('success', 'Storage bin activated successfully.');
    }

    /**
     * Deactivate storage bin
     */
    public function deactivate(StorageBin $storageBin)
    {
        $storageBin->update(['is_active' => false]);

        return back()->with('success', 'Storage bin deactivated successfully.');
    }

    /**
     * Get current stock in bin
     */
    public function currentStock(StorageBin $storageBin)
    {
        $storageBin->load(['warehouse', 'storageArea']);
        
        // You can add relationship to get actual stock items if you have inventory table
        return view('master.storage-bins.current-stock', compact('storageBin'));
    }

    /**
     * Helper: Generate range for aisles/levels
     */
    private function generateRange($start, $end)
    {
        $range = [];
        
        if (is_numeric($start) && is_numeric($end)) {
            for ($i = $start; $i <= $end; $i++) {
                $range[] = $i;
            }
        } else {
            // For letters (A-Z)
            $current = $start;
            while ($current <= $end) {
                $range[] = $current;
                $current++;
            }
        }
        
        return $range;
    }
}