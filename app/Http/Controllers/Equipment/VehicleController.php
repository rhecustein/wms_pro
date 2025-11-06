<?php
// app/Http/Controllers/Equipment/VehicleController.php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

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
            
            // Validate sort column
            $allowedSortColumns = ['vehicle_number', 'license_plate', 'brand', 'model', 'status', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }
            
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = min($request->input('per_page', 15), 100);
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

        } catch (Exception $e) {
            Log::error('Error loading vehicles list', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->except(['password', 'token'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to load vehicles. Please try again or contact support if the problem persists.');
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
            
        } catch (Exception $e) {
            Log::error('Error loading vehicle create form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('equipment.vehicles.index')
                ->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        try {
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
                'next_maintenance_date' => 'nullable|date|after_or_equal:today|after:last_maintenance_date',
                'odometer_km' => 'nullable|integer|min:0',
                'fuel_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:5000',
            ], [
                'vehicle_number.required' => 'Vehicle number is required.',
                'vehicle_number.unique' => 'This vehicle number already exists.',
                'license_plate.required' => 'License plate is required.',
                'license_plate.unique' => 'This license plate already exists.',
                'vehicle_type.required' => 'Please select a vehicle type.',
                'vehicle_type.in' => 'Invalid vehicle type selected.',
                'status.required' => 'Please select a status.',
                'status.in' => 'Invalid status selected.',
                'ownership.required' => 'Please select ownership type.',
                'ownership.in' => 'Invalid ownership type selected.',
                'year.integer' => 'Year must be a valid number.',
                'year.min' => 'Year must be at least 1900.',
                'year.max' => 'Year cannot be in the future.',
                'capacity_kg.numeric' => 'Capacity (KG) must be a number.',
                'capacity_kg.min' => 'Capacity (KG) cannot be negative.',
                'capacity_cbm.numeric' => 'Capacity (m³) must be a number.',
                'capacity_cbm.min' => 'Capacity (m³) cannot be negative.',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
                'next_maintenance_date.after_or_equal' => 'Next maintenance date must be today or in the future.',
                'next_maintenance_date.after' => 'Next maintenance date must be after last maintenance date.',
                'odometer_km.integer' => 'Odometer must be a whole number.',
                'odometer_km.min' => 'Odometer cannot be negative.',
                'notes.max' => 'Notes cannot exceed 5000 characters.',
            ]);

            DB::beginTransaction();

            // Set default values
            $validated['odometer_km'] = $validated['odometer_km'] ?? 0;
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            // Clean up empty strings to null for nullable fields
            $nullableFields = ['brand', 'model', 'year', 'capacity_kg', 'capacity_cbm', 
                               'last_maintenance_date', 'next_maintenance_date', 'fuel_type', 'notes'];
            
            foreach ($nullableFields as $field) {
                if (isset($validated[$field]) && $validated[$field] === '') {
                    $validated[$field] = null;
                }
            }

            $vehicle = Vehicle::create($validated);

            DB::commit();

            Log::info('Vehicle created successfully', [
                'vehicle_id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'user_id' => auth()->id(),
                'data' => $validated
            ]);

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicle->vehicle_number} has been created successfully!");

        } catch (ValidationException $e) {
            Log::warning('Vehicle validation failed', [
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
                'request_data' => $request->except(['password', 'token'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors($e->errors());
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating vehicle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->except(['password', 'token'])
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create vehicle. Please try again or contact support if the problem persists.');
        }
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle)
    {
        try {
            $vehicle->load(['creator', 'updater']);
            
            // Additional statistics for this vehicle
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
            
        } catch (Exception $e) {
            Log::error('Error loading vehicle details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicle_id' => $vehicle->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('equipment.vehicles.index')
                ->with('error', 'Failed to load vehicle details. Please try again.');
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
            
        } catch (Exception $e) {
            Log::error('Error loading vehicle edit form', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicle_id' => $vehicle->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('equipment.vehicles.index')
                ->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        try {
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
                'next_maintenance_date' => 'nullable|date|after_or_equal:today|after:last_maintenance_date',
                'odometer_km' => 'nullable|integer|min:0|min:' . ($vehicle->odometer_km ?? 0),
                'fuel_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:5000',
            ], [
                'vehicle_number.required' => 'Vehicle number is required.',
                'vehicle_number.unique' => 'This vehicle number already exists.',
                'license_plate.required' => 'License plate is required.',
                'license_plate.unique' => 'This license plate already exists.',
                'vehicle_type.required' => 'Please select a vehicle type.',
                'vehicle_type.in' => 'Invalid vehicle type selected.',
                'status.required' => 'Please select a status.',
                'status.in' => 'Invalid status selected.',
                'ownership.required' => 'Please select ownership type.',
                'ownership.in' => 'Invalid ownership type selected.',
                'year.integer' => 'Year must be a valid number.',
                'year.min' => 'Year must be at least 1900.',
                'year.max' => 'Year cannot be in the future.',
                'capacity_kg.numeric' => 'Capacity (KG) must be a number.',
                'capacity_kg.min' => 'Capacity (KG) cannot be negative.',
                'capacity_cbm.numeric' => 'Capacity (m³) must be a number.',
                'capacity_cbm.min' => 'Capacity (m³) cannot be negative.',
                'last_maintenance_date.before_or_equal' => 'Last maintenance date cannot be in the future.',
                'next_maintenance_date.after_or_equal' => 'Next maintenance date must be today or in the future.',
                'next_maintenance_date.after' => 'Next maintenance date must be after last maintenance date.',
                'odometer_km.integer' => 'Odometer must be a whole number.',
                'odometer_km.min' => 'Odometer cannot be less than the current value.',
                'notes.max' => 'Notes cannot exceed 5000 characters.',
            ]);

            DB::beginTransaction();

            // Store old values for logging
            $oldValues = $vehicle->only([
                'vehicle_number', 'license_plate', 'vehicle_type', 'brand', 'model',
                'year', 'capacity_kg', 'capacity_cbm', 'status', 'ownership',
                'last_maintenance_date', 'next_maintenance_date', 'odometer_km', 'fuel_type'
            ]);

            $validated['updated_by'] = auth()->id();

            // Clean up empty strings to null for nullable fields
            $nullableFields = ['brand', 'model', 'year', 'capacity_kg', 'capacity_cbm', 
                               'last_maintenance_date', 'next_maintenance_date', 'fuel_type', 'notes'];
            
            foreach ($nullableFields as $field) {
                if (isset($validated[$field]) && $validated[$field] === '') {
                    $validated[$field] = null;
                }
            }

            $vehicle->update($validated);

            DB::commit();

            Log::info('Vehicle updated successfully', [
                'vehicle_id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'user_id' => auth()->id(),
                'old_values' => $oldValues,
                'new_values' => $validated,
                'changes' => $vehicle->getChanges()
            ]);

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicle->vehicle_number} has been updated successfully!");

        } catch (ValidationException $e) {
            Log::warning('Vehicle update validation failed', [
                'errors' => $e->errors(),
                'vehicle_id' => $vehicle->id,
                'user_id' => auth()->id(),
                'request_data' => $request->except(['password', 'token'])
            ]);
            
            return back()
                ->withInput()
                ->withErrors($e->errors());
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating vehicle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicle_id' => $vehicle->id,
                'user_id' => auth()->id(),
                'request_data' => $request->except(['password', 'token'])
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update vehicle. Please try again or contact support if the problem persists.');
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
            $vehicleId = $vehicle->id;
            
            $vehicle->delete();

            DB::commit();

            Log::info('Vehicle deleted successfully', [
                'vehicle_id' => $vehicleId,
                'vehicle_number' => $vehicleNumber,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('equipment.vehicles.index')
                ->with('success', "Vehicle {$vehicleNumber} has been deleted successfully!");

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting vehicle', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vehicle_id' => $vehicle->id ?? null,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete vehicle. This vehicle may be in use or have related records.');
        }
    }

    /**
     * Print vehicle list
     */
    public function print(Request $request)
    {
        try {
            $query = Vehicle::query()->with(['creator', 'updater']);

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('license_plate', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('vehicle_type')) {
                $query->where('vehicle_type', $request->vehicle_type);
            }

            if ($request->filled('ownership')) {
                $query->where('ownership', $request->ownership);
            }

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

            $vehicles = $query->orderBy('vehicle_number')->get();

            // Get statistics
            $statistics = [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('status', 'available')->count(),
                'in_use' => Vehicle::where('status', 'in_use')->count(),
                'maintenance' => Vehicle::where('status', 'maintenance')->count(),
                'inactive' => Vehicle::where('status', 'inactive')->count(),
            ];

            Log::info('Vehicle list printed', [
                'user_id' => auth()->id(),
                'total_records' => $vehicles->count(),
                'filters' => $request->except(['password', 'token'])
            ]);

            return view('equipment.vehicles.print', compact('vehicles', 'statistics'));
            
        } catch (Exception $e) {
            Log::error('Error loading vehicle print page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->route('equipment.vehicles.index')
                ->with('error', 'Failed to load print page. Please try again.');
        }
    }

    /**
     * Export vehicles data to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = Vehicle::query()->with(['creator', 'updater']);

            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('vehicle_number', 'like', "%{$search}%")
                      ->orWhere('license_plate', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('vehicle_type')) {
                $query->where('vehicle_type', $request->vehicle_type);
            }

            if ($request->filled('ownership')) {
                $query->where('ownership', $request->ownership);
            }

            $vehicles = $query->orderBy('vehicle_number')->get();

            $fileName = 'vehicles_export_' . date('Y-m-d_His') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($vehicles) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for Excel UTF-8 support
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // CSV Headers
                fputcsv($file, [
                    'Vehicle Number',
                    'License Plate',
                    'Vehicle Type',
                    'Brand',
                    'Model',
                    'Year',
                    'Capacity (KG)',
                    'Capacity (m³)',
                    'Status',
                    'Ownership',
                    'Odometer (KM)',
                    'Fuel Type',
                    'Last Maintenance',
                    'Next Maintenance',
                    'Created By',
                    'Created At',
                    'Updated By',
                    'Updated At',
                    'Notes'
                ]);

                // CSV Data
                foreach ($vehicles as $vehicle) {
                    fputcsv($file, [
                        $vehicle->vehicle_number,
                        $vehicle->license_plate,
                        ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)),
                        $vehicle->brand ?? '-',
                        $vehicle->model ?? '-',
                        $vehicle->year ?? '-',
                        $vehicle->capacity_kg ? number_format($vehicle->capacity_kg, 2) : '-',
                        $vehicle->capacity_cbm ? number_format($vehicle->capacity_cbm, 2) : '-',
                        ucfirst(str_replace('_', ' ', $vehicle->status)),
                        ucfirst($vehicle->ownership),
                        number_format($vehicle->odometer_km, 0),
                        $vehicle->fuel_type ?? '-',
                        $vehicle->last_maintenance_date ? $vehicle->last_maintenance_date->format('Y-m-d') : '-',
                        $vehicle->next_maintenance_date ? $vehicle->next_maintenance_date->format('Y-m-d') : '-',
                        $vehicle->creator->name ?? '-',
                        $vehicle->created_at->format('Y-m-d H:i:s'),
                        $vehicle->updater->name ?? '-',
                        $vehicle->updated_at->format('Y-m-d H:i:s'),
                        $vehicle->notes ? str_replace(["\r\n", "\r", "\n"], ' ', $vehicle->notes) : '-'
                    ]);
                }

                fclose($file);
            };

            Log::info('Vehicles exported successfully', [
                'user_id' => auth()->id(),
                'total_records' => $vehicles->count(),
                'filters' => $request->except(['password', 'token'])
            ]);

            return response()->stream($callback, 200, $headers);

        } catch (Exception $e) {
            Log::error('Error exporting vehicles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'filters' => $request->except(['password', 'token'])
            ]);
            
            return back()->with('error', 'Failed to export data. Please try again.');
        }
    }
}