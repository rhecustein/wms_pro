<?php
// app/Http/Controllers/Equipment/VehicleController.php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->with(['creator', 'updater']);

        // Search
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
                    $query->where('next_maintenance_date', '<', $now);
                    break;
                case 'due_soon':
                    $query->whereBetween('next_maintenance_date', [$now, $now->copy()->addDays(7)]);
                    break;
                case 'scheduled':
                    $query->where('next_maintenance_date', '>', $now->copy()->addDays(7));
                    break;
            }
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        $statuses = ['available', 'in_use', 'maintenance', 'inactive'];
        $types = ['truck', 'van', 'forklift', 'reach_truck'];
        $ownerships = ['owned', 'rented', 'leased'];

        return view('equipment.vehicles.index', compact('vehicles', 'statuses', 'types', 'ownerships'));
    }

    public function create()
    {
        $vehicleNumber = Vehicle::generateVehicleNumber();
        return view('equipment.vehicles.create', compact('vehicleNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number',
            'license_plate' => 'required|unique:vehicles,license_plate',
            'vehicle_type' => 'required|in:truck,van,forklift,reach_truck',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'nullable|numeric|min:0',
            'capacity_cbm' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance,inactive',
            'ownership' => 'required|in:owned,rented,leased',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:today',
            'odometer_km' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            Vehicle::create($validated);

            DB::commit();

            return redirect()->route('equipment.vehicles.index')
                           ->with('success', 'Vehicle created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to create vehicle: ' . $e->getMessage());
        }
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['creator', 'updater']);
        return view('equipment.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('equipment.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle_number' => 'required|unique:vehicles,vehicle_number,' . $vehicle->id,
            'license_plate' => 'required|unique:vehicles,license_plate,' . $vehicle->id,
            'vehicle_type' => 'required|in:truck,van,forklift,reach_truck',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity_kg' => 'nullable|numeric|min:0',
            'capacity_cbm' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,in_use,maintenance,inactive',
            'ownership' => 'required|in:owned,rented,leased',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:today',
            'odometer_km' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['updated_by'] = auth()->id();
            $vehicle->update($validated);

            DB::commit();

            return redirect()->route('equipment.vehicles.index')
                           ->with('success', 'Vehicle updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Failed to update vehicle: ' . $e->getMessage());
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        try {
            $vehicle->delete();
            return redirect()->route('equipment.vehicles.index')
                           ->with('success', 'Vehicle deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }
}