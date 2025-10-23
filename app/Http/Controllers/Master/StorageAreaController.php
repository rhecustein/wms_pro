<?php
// app/Http/Controllers/Master/StorageAreaController.php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StorageArea;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $storageAreas = $query->latest()->paginate(15)->withQueryString();
        
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        
        $types = ['spr', 'bulky', 'quarantine', 'staging_1', 'staging_2', 'virtual'];

        return view('master.storage-areas.index', compact('storageAreas', 'warehouses', 'types'));
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

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active') ? true : false;

        StorageArea::create($validated);

        return redirect()->route('master.storage-areas.index')
            ->with('success', 'Storage area created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StorageArea $storageArea)
    {
        $storageArea->load(['warehouse', 'createdBy', 'updatedBy', 'storageBins']);

        return view('master.storage-areas.show', compact('storageArea'));
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

        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $storageArea->update($validated);

        return redirect()->route('master.storage-areas.index')
            ->with('success', 'Storage area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StorageArea $storageArea)
    {
        try {
            $storageArea->delete();
            
            return redirect()->route('master.storage-areas.index')
                ->with('success', 'Storage area deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('master.storage-areas.index')
                ->with('error', 'Unable to delete storage area. It may be in use.');
        }
    }
}