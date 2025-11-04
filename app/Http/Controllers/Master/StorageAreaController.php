<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StorageArea;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StorageAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StorageArea::with(['warehouse', 'createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('warehouse', function($wq) use ($search) {
                      $wq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Warehouse
        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Get paginated storage areas
        $storageAreas = $query->latest()->paginate(15)->withQueryString();
        
        // Get warehouses for filter dropdown
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        
        // Types array for filter
        $types = ['spr', 'bulky', 'quarantine', 'staging_1', 'staging_2', 'virtual'];

        // Calculate statistics for all storage areas
        $stats = [
            'total_areas' => StorageArea::count(),
            'active_areas' => StorageArea::where('is_active', true)->count(),
            'inactive_areas' => StorageArea::where('is_active', false)->count(),
            'total_capacity' => StorageArea::sum('capacity_pallets') ?? 0,
            'total_area_sqm' => StorageArea::sum('area_sqm') ?? 0,
        ];

        // Count by type
        $stats['by_type'] = [
            'spr' => StorageArea::where('type', 'spr')->count(),
            'bulky' => StorageArea::where('type', 'bulky')->count(),
            'quarantine' => StorageArea::where('type', 'quarantine')->count(),
            'staging_1' => StorageArea::where('type', 'staging_1')->count(),
            'staging_2' => StorageArea::where('type', 'staging_2')->count(),
            'virtual' => StorageArea::where('type', 'virtual')->count(),
        ];

        // Return view with all data
        return view('master.storage-areas.index', [
            'storageAreas' => $storageAreas,
            'warehouses' => $warehouses,
            'types' => $types,
            'stats' => $stats
        ]);
    }

    /**
     * Show storage areas by warehouse.
     */
    public function byWarehouse(Warehouse $warehouse)
    {
        $storageAreas = StorageArea::where('warehouse_id', $warehouse->id)
            ->with(['createdBy', 'updatedBy'])
            ->latest()
            ->get();

        return view('master.storage-areas.by-warehouse', compact('warehouse', 'storageAreas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        
        $types = [
            'spr' => 'SPR (Standard Pallet Rack)',
            'bulky' => 'Bulky Storage',
            'quarantine' => 'Quarantine Area',
            'staging_1' => 'Staging Area 1',
            'staging_2' => 'Staging Area 2',
            'virtual' => 'Virtual Storage'
        ];

        return view('master.storage-areas.create', compact('warehouses', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('storage_areas')->where(function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                })
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:spr,bulky,quarantine,staging_1,staging_2,virtual',
            'area_sqm' => 'nullable|numeric|min:0',
            'height_meters' => 'nullable|numeric|min:0',
            'capacity_pallets' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active');

            $storageArea = StorageArea::create($validated);

            // Log activity
            activity()
                ->performedOn($storageArea)
                ->causedBy(Auth::user())
                ->log('Created storage area: ' . $storageArea->name);

            DB::commit();

            return redirect()
                ->route('master.storage-areas.index')
                ->with('success', 'Storage area created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create storage area', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'data' => $request->except(['_token'])
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create storage area: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StorageArea $storageArea)
    {
        $storageArea->load(['warehouse', 'createdBy', 'updatedBy', 'storageBins']);

        // Get statistics for this storage area
        $stats = [
            'total_bins' => $storageArea->storageBins()->count(),
            'available_bins' => $storageArea->storageBins()->where('status', 'available')->count(),
            'occupied_bins' => $storageArea->storageBins()->where('status', 'occupied')->count(),
            'reserved_bins' => $storageArea->storageBins()->where('status', 'reserved')->count(),
            'utilization' => 0,
        ];

        // Calculate utilization percentage
        if ($stats['total_bins'] > 0) {
            $stats['utilization'] = round(($stats['occupied_bins'] / $stats['total_bins']) * 100, 2);
        }

        return view('master.storage-areas.show', compact('storageArea', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StorageArea $storageArea)
    {
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        
        $types = [
            'spr' => 'SPR (Standard Pallet Rack)',
            'bulky' => 'Bulky Storage',
            'quarantine' => 'Quarantine Area',
            'staging_1' => 'Staging Area 1',
            'staging_2' => 'Staging Area 2',
            'virtual' => 'Virtual Storage'
        ];

        return view('master.storage-areas.edit', compact('storageArea', 'warehouses', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StorageArea $storageArea)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('storage_areas')->where(function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                })->ignore($storageArea->id)
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:spr,bulky,quarantine,staging_1,staging_2,virtual',
            'area_sqm' => 'nullable|numeric|min:0',
            'height_meters' => 'nullable|numeric|min:0',
            'capacity_pallets' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active');

            $storageArea->update($validated);

            // Log activity
            activity()
                ->performedOn($storageArea)
                ->causedBy(Auth::user())
                ->log('Updated storage area: ' . $storageArea->name);

            DB::commit();

            return redirect()
                ->route('master.storage-areas.index')
                ->with('success', 'Storage area updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update storage area', [
                'storage_area_id' => $storageArea->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update storage area: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StorageArea $storageArea)
    {
        // Check if storage area has storage bins
        if ($storageArea->storageBins()->exists()) {
            return back()->with('error', 'Cannot delete storage area with existing storage bins.');
        }

        DB::beginTransaction();
        try {
            $areaName = $storageArea->name;
            $storageArea->delete();

            // Log activity
            activity()
                ->causedBy(Auth::user())
                ->log('Deleted storage area: ' . $areaName);

            DB::commit();
            
            return redirect()
                ->route('master.storage-areas.index')
                ->with('success', 'Storage area deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete storage area', [
                'storage_area_id' => $storageArea->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to delete storage area: ' . $e->getMessage());
        }
    }

    /**
     * Activate storage area
     */
    public function activate(StorageArea $storageArea)
    {
        DB::beginTransaction();
        try {
            $storageArea->update([
                'is_active' => true,
                'updated_by' => Auth::id()
            ]);

            activity()
                ->performedOn($storageArea)
                ->causedBy(Auth::user())
                ->log('Activated storage area: ' . $storageArea->name);

            DB::commit();

            return back()->with('success', 'Storage area activated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to activate storage area', [
                'storage_area_id' => $storageArea->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to activate storage area: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate storage area
     */
    public function deactivate(StorageArea $storageArea)
    {
        DB::beginTransaction();
        try {
            $storageArea->update([
                'is_active' => false,
                'updated_by' => Auth::id()
            ]);

            activity()
                ->performedOn($storageArea)
                ->causedBy(Auth::user())
                ->log('Deactivated storage area: ' . $storageArea->name);

            DB::commit();

            return back()->with('success', 'Storage area deactivated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to deactivate storage area', [
                'storage_area_id' => $storageArea->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to deactivate storage area: ' . $e->getMessage());
        }
    }
}