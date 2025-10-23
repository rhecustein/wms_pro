<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status == 'active');
        }

        // Filter by city
        if ($request->has('city') && $request->city != '') {
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
            ->pluck('city');

        return view('master.warehouses.index', compact('warehouses', 'cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $managers = User::role(['warehouse_manager', 'admin'])
            ->orderBy('name')
            ->get();

        return view('master.warehouses.create', compact('managers'));
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

            activity()
                ->performedOn($warehouse)
                ->causedBy(Auth::user())
                ->log('Created warehouse: ' . $warehouse->name);

            DB::commit();

            return redirect()
                ->route('warehouses.index')
                ->with('success', 'Warehouse created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create warehouse: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['manager', 'creator', 'updater', 'storageAreas.storageBins']);
        
        // Get statistics
        $stats = [
            'total_bins' => $warehouse->storageBins()->count(),
            'occupied_bins' => $warehouse->storageBins()->where('status', 'occupied')->count(),
            'available_bins' => $warehouse->storageBins()->where('status', 'available')->count(),
            'reserved_bins' => $warehouse->storageBins()->where('status', 'reserved')->count(),
            'total_stock' => $warehouse->storageBins()->sum('current_quantity'),
            'utilization' => $warehouse->utilization,
        ];

        // Get recent activities
        $activities = activity()
            ->performedOn($warehouse)
            ->latest()
            ->limit(10)
            ->get();

        return view('master.warehouses.show', compact('warehouse', 'stats', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse)
    {
        $managers = User::role(['warehouse_manager', 'admin'])
            ->orderBy('name')
            ->get();

        return view('master.warehouses.edit', compact('warehouse', 'managers'));
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
                ->route('warehouses.index')
                ->with('success', 'Warehouse updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
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
                ->route('warehouses.index')
                ->with('success', 'Warehouse deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
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
    }
}