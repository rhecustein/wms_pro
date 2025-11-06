<?php
// app/Http/Controllers/Equipment/VehicleController.php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles with filters
     */
    public function index(Request $request)
    {
        try {
            $query = Vehicle::query()->with(['creator', 'updater']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('license_plate', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by vehicle type
            if ($request->filled('vehicle_type')) {
                $query->where('vehicle_type', $request->vehicle_type);
            }

            // Filter by ownership
            if ($request->filled('ownership')) {
                $query->where('ownership', $request->ownership);
            }

            // Filter by maintenance status
            if ($request->filled('maintenance_status')) {
                $now = now();
                switch ($request->maintenance_status) {
                    case 'overdue':
                        $query->where('next_maintenance_date', '<', $now)
                              ->whereNotNull('next_maintenance_date');
                        break;
                    case 'due_soon':
                        $query->whereBetween('next_maintenance_date', [
                            $now, 
                            $now->copy()->addDays(7)
                        ]);
                        break;
                    case 'scheduled':
                        $query->where('next_maintenance_date', '>', $now->copy()->addDays(7));
                        break;
                    case 'not_scheduled':
                        $query->whereNull('next_maintenance_date');
                        break;
                }
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->input('per_page', 15);
            $vehicles = $query->paginate($perPage)->withQueryString();

            // Get statistics
            $statistics = [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('status', 'available')->count(),
                'in_use' => Vehicle::where('status', 'in_use')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'inactive' => Vehicle::where('status', 'inactive')->count(),
            ];

            // Filter options
            $statuses = ['available', 'in_use', 'maintenance', 'inactive'];
            $types = ['truck', 'van', 'forklift', 'reach_truck'];
            $ownerships = ['owned', 'rented', 'leased'];
            $maintenanceStatuses = ['overdue', 'due_soon', 'scheduled', 'not_scheduled'];

            return view('equipment.vehicles.index', compact(
                'vehicles',
                'statistics',
                'statuses',
                'types',
                'ownerships',
                'maintenanceStatuses'
            ));

        } catch (\Exception $e) {
            Log::error('Error loading vehicles: ' . $e->getMessage());
            return back()->with('error', 'Failed to load vehicles. Please try again.');
        }
    }

    /**
     * Show the form for creating a new vehicle
     */
    public function create()
    {
        try {
            $vehicleNumber = Vehicle::generateVehicleNumber();
            
            $statuses = ['available', 'in_use', 'maintenance', 'inactive'];
            $types = ['truck', 'van', 'forklift', 'reach_truck'];
            $ownerships = ['owned', 'rented', 'leased'];
            
            return view('equipment.vehicles.create', compact(
                'vehicleNumber',
                'statuses',
                'types',
                'ownerships'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load form. Please try again.');
        }
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number',
            'license_plate' => 'required|string|max:255|unique:vehicles,license_plate',
            'vehicle_type' => 'required|in:truck,van,forklift,reach_truck',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'nullable|numeric|min:0|max:999999.99',
            'capacity_cbm' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'required|in:available,in_use,maintenance,inactive',
            'ownership' => 'required|in:owned,rented,leased',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'next_maintenance_date' => 'nullable|date|after_or_equal:today',
            'odometer_km' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ], [
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_number.unique' => 'This vehicle number already exists.',
            'license_plate.required' => 'License plate is required.',
            'license_plate.unique' => 'This license plate already exists.',
            'vehicle_type.required' => 'Please select a vehicle type.',
            'status.required' => 'Please select a status.',
            'ownership.required' => 'Please select ownership type.',
            'year.max' => 'Year cannot be in the future.',
            'next_maintenance_date.after_or_equal' => 'Next maintenance date must be today or in the future.',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $vehicle = Vehicle::create($validated);

            DB::commit();

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicle->vehicle_number} has been created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating vehicle: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create vehicle. Please try again.');
        }
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle)
    {
        try {
            $vehicle->load(['creator', 'updater']);
            
            // Additional statistics for this vehicle (if needed)
            $stats = [
                'total_days' => $vehicle->created_at->diffInDays(now()),
                'days_since_maintenance' => $vehicle->last_maintenance_date 
                    ? $vehicle->last_maintenance_date->diffInDays(now()) 
                    : null,
                'days_until_maintenance' => $vehicle->next_maintenance_date 
                    ? now()->diffInDays($vehicle->next_maintenance_date, false) 
                    : null,
            ];
            
            return view('equipment.vehicles.show', compact('vehicle', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading vehicle details: ' . $e->getMessage());
            return back()->with('error', 'Failed to load vehicle details.');
        }
    }

    /**
     * Show the form for editing the vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        try {
            $statuses = ['available', 'in_use', 'maintenance', 'inactive'];
            $types = ['truck', 'van', 'forklift', 'reach_truck'];
            $ownerships = ['owned', 'rented', 'leased'];
            
            return view('equipment.vehicles.edit', compact(
                'vehicle',
                'statuses',
                'types',
                'ownerships'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return back()->with('error', 'Failed to load form. Please try again.');
        }
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,' . $vehicle->id,
            'license_plate' => 'required|string|max:255|unique:vehicles,license_plate,' . $vehicle->id,
            'vehicle_type' => 'required|in:truck,van,forklift,reach_truck',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'nullable|numeric|min:0|max:999999.99',
            'capacity_cbm' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'required|in:available,in_use,maintenance,inactive',
            'ownership' => 'required|in:owned,rented,leased',
            'last_maintenance_date' => 'nullable|date|before_or_equal:today',
            'next_maintenance_date' => 'nullable|date|after_or_equal:today',
            'odometer_km' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ], [
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_number.unique' => 'This vehicle number already exists.',
            'license_plate.required' => 'License plate is required.',
            'license_plate.unique' => 'This license plate already exists.',
            'vehicle_type.required' => 'Please select a vehicle type.',
            'status.required' => 'Please select a status.',
            'ownership.required' => 'Please select ownership type.',
            'year.max' => 'Year cannot be in the future.',
            'next_maintenance_date.after_or_equal' => 'Next maintenance date must be today or in the future.',
        ]);

        try {
            DB::beginTransaction();

            $validated['updated_by'] = auth()->id();
            $vehicle->update($validated);

            DB::commit();

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicle->vehicle_number} has been updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating vehicle: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update vehicle. Please try again.');
        }
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        try {
            DB::beginTransaction();

            $vehicleNumber = $vehicle->vehicle_number;
            $vehicle->delete();

            DB::commit();

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicleNumber} has been deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting vehicle: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete vehicle. This vehicle may be in use.');
        }
    }

    /**
     * Export vehicles data
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality (CSV/Excel/PDF)
        return back()->with('info', 'Export feature will be available soon.');
    }

    /**
     * Print vehicle details
     */
    public function print(Vehicle $vehicle)
    {
        // TODO: Implement print view
        $vehicle->load(['creator', 'updater']);
        return view('equipment.vehicles.print', compact('vehicle'));
    }
}