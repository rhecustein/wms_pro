<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warehouse::with(['manager', 'creator'])
            ->withCount('storageBins');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $warehouses = $query->paginate(10)->withQueryString();
        
        // Get cities for filter
        $cities = Warehouse::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->orderBy('city')
            ->pluck('city');

        return view('master.warehouses.index', compact('warehouses', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $managers = User::withRoles(['warehouse-manager', 'warehouse_manager', 'super-admin'])
                ->active()
                ->orderBy('name')
                ->get();

            return view('master.warehouses.create', compact('managers'));
            
        } catch (\Exception $e) {
            Log::error('Error loading warehouse create form: ' . $e->getMessage());
            
            return redirect()
                ->route('master.warehouses.index')
                ->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'total_area_sqm' => 'nullable|numeric|min:0',
            'height_meters' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['created_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active');

            $warehouse = Warehouse::create($validated);

            // Log activity
            activity()
                ->performedOn($warehouse)
                ->causedBy(Auth::user())
                ->log('Created warehouse: ' . $warehouse->name);

            DB::commit();

            return redirect()
                ->route('master.warehouses.index')
                ->with('success', 'Warehouse created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to create warehouse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'data' => $request->except(['_token'])
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create warehouse: ' . $e->getMessage());
        }
    }

    // app/Http/Controllers/Master/WarehouseController.php
    public function show(Warehouse $warehouse)
    {
        try {
            $warehouse->load(['manager', 'creator', 'updater']);
            
            // Load storage areas dan bins jika ada
            if (Schema::hasTable('storage_areas') && Schema::hasColumn('storage_areas', 'warehouse_id')) {
                $warehouse->load('storageAreas.storageBins');
            }
            
            // Get statistics
            $stats = [
                'total_bins' => 0,
                'occupied_bins' => 0,
                'available_bins' => 0,
                'reserved_bins' => 0,
                'total_stock' => 0,
                'utilization' => 0,
            ];

            // Hitung statistics jika tabel storage_bins ada
            if (Schema::hasTable('storage_bins') && Schema::hasColumn('storage_bins', 'warehouse_id')) {
                $stats = [
                    'total_bins' => $warehouse->storageBins()->count(),
                    'occupied_bins' => $warehouse->storageBins()->where('status', 'occupied')->count(),
                    'available_bins' => $warehouse->storageBins()->where('status', 'available')->count(),
                    'reserved_bins' => $warehouse->storageBins()->where('status', 'reserved')->count(),
                    'total_stock' => $warehouse->storageBins()->sum('current_quantity'),
                    'utilization' => $warehouse->utilization ?? 0,
                ];
            }

            // ✅ PERBAIKAN: Get recent activities dengan cara yang benar
            $activities = \Spatie\Activitylog\Models\Activity::query()
                ->where('subject_type', Warehouse::class)
                ->where('subject_id', $warehouse->id)
                ->latest()
                ->limit(10)
                ->get();

            return view('master.warehouses.show', compact('warehouse', 'stats', 'activities'));
            
        } catch (\Exception $e) {
            Log::error('Error showing warehouse: ' . $e->getMessage());
            
            return redirect()
                ->route('master.warehouses.index')
                ->with('error', 'Failed to load warehouse details.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        try {
            $managers = User::withRoles(['warehouse-manager', 'warehouse_manager', 'super-admin'])
                ->active()
                ->orderBy('name')
                ->get();

            return view('master.warehouses.edit', compact('warehouse', 'managers'));
            
        } catch (\Exception $e) {
            Log::error('Error loading warehouse edit form: ' . $e->getMessage());
            
            return redirect()
                ->route('master.warehouses.index')
                ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique('warehouses')->ignore($warehouse->id)],
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'total_area_sqm' => 'nullable|numeric|min:0',
            'height_meters' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active');

            $warehouse->update($validated);

            activity()
                ->performedOn($warehouse)
                ->causedBy(Auth::user())
                ->log('Updated warehouse: ' . $warehouse->name);

            DB::commit();

            return redirect()
                ->route('master.warehouses.index')
                ->with('success', 'Warehouse updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to update warehouse', [
                'warehouse_id' => $warehouse->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        DB::beginTransaction();
        try {
            // Check if warehouse has related data
            if ($warehouse->storageBins()->exists()) {
                return back()->with('error', 'Cannot delete warehouse with existing storage bins.');
            }

            $warehouseName = $warehouse->name;
            $warehouse->delete();

            activity()
                ->causedBy(Auth::user())
                ->log('Deleted warehouse: ' . $warehouseName);

            DB::commit();

            return redirect()
                ->route('master.warehouses.index')
                ->with('success', 'Warehouse deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to delete warehouse', [
                'warehouse_id' => $warehouse->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to delete warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Activate warehouse
     */
    public function activate(Warehouse $warehouse)
    {
        DB::beginTransaction();
        try {
            $warehouse->update(['is_active' => true, 'updated_by' => Auth::id()]);

            activity()
                ->performedOn($warehouse)
                ->causedBy(Auth::user())
                ->log('Activated warehouse: ' . $warehouse->name);

            DB::commit();

            return back()->with('success', 'Warehouse activated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to activate warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate warehouse
     */
    public function deactivate(Warehouse $warehouse)
    {
        DB::beginTransaction();
        try {
            $warehouse->update(['is_active' => false, 'updated_by' => Auth::id()]);

            activity()
                ->performedOn($warehouse)
                ->causedBy(Auth::user())
                ->log('Deactivated warehouse: ' . $warehouse->name);

            DB::commit();

            return back()->with('success', 'Warehouse deactivated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to deactivate warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Show warehouse layout
     */
    public function layout(Warehouse $warehouse)
    {
        try {
            $warehouse->load(['storageAreas.storageBins']);

            // Group bins by aisle
            $aisles = $warehouse->storageAreas()
                ->with(['storageBins' => function($query) {
                    $query->orderBy('aisle')
                          ->orderBy('row')
                          ->orderBy('column')
                          ->orderBy('level');
                }])
                ->get()
                ->groupBy('type');

            return view('master.warehouses.layout', compact('warehouse', 'aisles'));
            
        } catch (\Exception $e) {
            Log::error('Error loading warehouse layout: ' . $e->getMessage());
            
            return redirect()
                ->route('master.warehouses.show', $warehouse)
                ->with('error', 'Failed to load warehouse layout.');
        }
    }
}