<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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
            
        } catch (Exception $e) {
            Log::error('Error loading equipment list: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to load equipment list. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $warehouses = Warehouse::orderBy('name')->get();
            $types = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
            $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];

            return view('equipment.create', compact('warehouses', 'types', 'statuses'));
            
        } catch (Exception $e) {
            Log::error('Error loading equipment create form: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'equipment_type' => 'required|in:forklift,reach_truck,pallet_jack,scanner',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255|unique:equipment,serial_number',
                'warehouse_id' => 'required|exists:warehouses,id',
                'status' => 'required|in:available,in_use,maintenance,damaged,inactive',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
                'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
                'operating_hours' => 'nullable|integer|min:0|max:999999',
                'notes' => 'nullable|string|max:1000',
            ], [
                'equipment_type.required' => 'Equipment type is required.',
                'equipment_type.in' => 'Invalid equipment type selected.',
                'serial_number.unique' => 'This serial number already exists.',
                'warehouse_id.required' => 'Warehouse is required.',
                'warehouse_id.exists' => 'Selected warehouse does not exist.',
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status selected.',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
                'next_maintenance_date.after_or_equal' => 'Next maintenance date must be after last maintenance date.',
                'operating_hours.min' => 'Operating hours cannot be negative.',
                'operating_hours.max' => 'Operating hours value is too large.',
                'notes.max' => 'Notes cannot exceed 1000 characters.',
            ]);

            DB::beginTransaction();

            // Generate Equipment Number
            $validated['equipment_number'] = $this->generateEquipmentNumber();
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();

            $equipment = Equipment::create($validated);

            DB::commit();

            Log::info('Equipment created successfully', [
                'equipment_id' => $equipment->id,
                'equipment_number' => $equipment->equipment_number,
                'created_by' => Auth::id()
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('success', 'Equipment created successfully!');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating equipment: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'input' => $request->except(['_token']),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Failed to create equipment. Please try again or contact support.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        try {
            $equipment->load(['warehouse', 'createdBy', 'updatedBy']);
            
            return view('equipment.show', compact('equipment'));
            
        } catch (Exception $e) {
            Log::error('Error loading equipment details: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('error', 'Failed to load equipment details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        try {
            $warehouses = Warehouse::orderBy('name')->get();
            $types = ['forklift', 'reach_truck', 'pallet_jack', 'scanner'];
            $statuses = ['available', 'in_use', 'maintenance', 'damaged', 'inactive'];

            return view('equipment.edit', compact('equipment', 'warehouses', 'types', 'statuses'));
            
        } catch (Exception $e) {
            Log::error('Error loading equipment edit form: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        try {
            $validated = $request->validate([
                'equipment_type' => 'required|in:forklift,reach_truck,pallet_jack,scanner',
                'brand' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'serial_number' => 'nullable|string|max:255|unique:equipment,serial_number,' . $equipment->id,
                'warehouse_id' => 'required|exists:warehouses,id',
                'status' => 'required|in:available,in_use,maintenance,damaged,inactive',
                'last_maintenance_date' => 'nullable|date|before_or_equal:today',
                'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
                'operating_hours' => 'nullable|integer|min:0|max:999999',
                'notes' => 'nullable|string|max:1000',
            ], [
                'equipment_type.required' => 'Equipment type is required.',
                'equipment_type.in' => 'Invalid equipment type selected.',
                'serial_number.unique' => 'This serial number already exists.',
                'warehouse_id.required' => 'Warehouse is required.',
                'warehouse_id.exists' => 'Selected warehouse does not exist.',
                'status.required' => 'Status is required.',
                'status.in' => 'Invalid status selected.',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
                'next_maintenance_date.after_or_equal' => 'Next maintenance date must be after last maintenance date.',
                'operating_hours.min' => 'Operating hours cannot be negative.',
                'operating_hours.max' => 'Operating hours value is too large.',
                'notes.max' => 'Notes cannot exceed 1000 characters.',
            ]);

            DB::beginTransaction();

            $oldData = $equipment->toArray();
            $validated['updated_by'] = Auth::id();
            
            $equipment->update($validated);

            DB::commit();

            Log::info('Equipment updated successfully', [
                'equipment_id' => $equipment->id,
                'equipment_number' => $equipment->equipment_number,
                'updated_by' => Auth::id(),
                'old_data' => $oldData,
                'new_data' => $validated
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('success', 'Equipment updated successfully!');
                
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating equipment: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id ?? null,
                'user_id' => Auth::id(),
                'input' => $request->except(['_token', '_method']),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Failed to update equipment. Please try again or contact support.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        try {
            // Check if equipment can be deleted (add your business logic here)
            // For example, check if equipment is currently in use
            if ($equipment->status === 'in_use') {
                return back()->with('error', 'Cannot delete equipment that is currently in use.');
            }

            DB::beginTransaction();

            $equipmentNumber = $equipment->equipment_number;
            $equipmentId = $equipment->id;
            
            $equipment->delete();
            
            DB::commit();

            Log::info('Equipment deleted successfully', [
                'equipment_id' => $equipmentId,
                'equipment_number' => $equipmentNumber,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('equipment.equipments.index')
                ->with('success', 'Equipment deleted successfully!');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting equipment: ' . $e->getMessage(), [
                'equipment_id' => $equipment->id ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to delete equipment. It may be associated with other records.');
        }
    }

    /**
     * Generate unique equipment number
     */
    private function generateEquipmentNumber()
    {
        try {
            $prefix = 'EQ';
            $date = now()->format('Ymd');
            
            $lastEquipment = Equipment::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastEquipment && $lastEquipment->equipment_number) {
                $lastNumber = (int) substr($lastEquipment->equipment_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $equipmentNumber = $prefix . '-' . $date . '-' . $newNumber;
            
            // Double check uniqueness
            $exists = Equipment::where('equipment_number', $equipmentNumber)->exists();
            if ($exists) {
                // If somehow exists, generate random suffix
                $equipmentNumber = $prefix . '-' . $date . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            return $equipmentNumber;
            
        } catch (Exception $e) {
            Log::error('Error generating equipment number: ' . $e->getMessage());
            // Fallback to timestamp-based number
            return 'EQ-' . now()->format('Ymd') . '-' . now()->format('His');
        }
    }
}