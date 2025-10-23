<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipment::with(['warehouse', 'createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('equipment_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        // Filter by Type
        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', $request->equipment_type);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Filter by Maintenance Status
        if ($request->filled('maintenance_status')) {
            $today = now()->toDateString();
            
            if ($request->maintenance_status === 'overdue') {
                $query->where('next_maintenance_date', '<', $today);
            } elseif ($request->maintenance_status === 'due_soon') {
                $query->whereBetween('next_maintenance_date', [$today, now()->addDays(7)->toDateString()]);
            }
        }

        $equipments = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $types = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
        $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];
        $warehouses = Warehouse::orderBy('name')->get();

        return view('equipment.index', compact('equipments', 'types', 'statuses', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $types = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
        $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];

        return view('equipment.create', compact('warehouses', 'types', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_type' => 'required|in:forklift,reach_truck,pallet_jack,scanner',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'required|in:available,in_use,maintenance,damaged,inactive',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
            'operating_hours' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate Equipment Number
            $validated['equipment_number'] = $this->generateEquipmentNumber();
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            Equipment::create($validated);

            DB::commit();

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create equipment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $equipment->load(['warehouse', 'createdBy', 'updatedBy']);
        
        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $types = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
        $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];

        return view('equipment.edit', compact('equipment', 'warehouses', 'types', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'equipment_type' => 'required|in:forklift,reach_truck,pallet_jack,scanner',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'required|in:available,in_use,maintenance,damaged,inactive',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
            'operating_hours' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = Auth::id();
            $equipment->update($validated);

            DB::commit();

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update equipment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        DB::beginTransaction();
        try {
            $equipment->delete();
            DB::commit();

            return redirect()->route('equipment.index')
                ->with('success', 'Equipment deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete equipment: ' . $e->getMessage());
        }
    }

    /**
     * Generate unique equipment number
     */
    private function generateEquipmentNumber()
    {
        $prefix = 'EQ';
        $date = now()->format('Ymd');
        
        $lastEquipment = Equipment::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEquipment) {
            $lastNumber = (int) substr($lastEquipment->equipment_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . '-' . $date . '-' . $newNumber;
    }
}