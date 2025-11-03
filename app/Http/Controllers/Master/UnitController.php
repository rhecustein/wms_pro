<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unit::query()->with(['baseUnit', 'createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by Base Unit
        if ($request->filled('base_unit_id')) {
            $query->where('base_unit_id', $request->base_unit_id);
        }

        $units = $query->latest()->paginate(15)->withQueryString();
        
        // Get base units for filter
        $baseUnits = Unit::whereNull('base_unit_id')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        return view('master.units.index', compact('units', 'baseUnits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all base units (units without parent)
        $baseUnits = Unit::whereNull('base_unit_id')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        return view('master.units.create', compact('baseUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'short_code' => 'required|string|max:10|unique:units,short_code',
            'type' => 'required|in:base,volume,weight,length,area,other',
            'base_unit_conversion' => 'nullable|numeric|min:0',
            'base_unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Set default conversion rate
        if (empty($validated['base_unit_conversion'])) {
            $validated['base_unit_conversion'] = 1;
        }

        // Add audit fields
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        Unit::create($validated);

        return redirect()
            ->route('master.units.index')
            ->with('success', 'Unit successfully created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $unit->load(['baseUnit', 'createdBy', 'updatedBy']);
        
        // Get child units (units that use this as base)
        $childUnits = Unit::where('base_unit_id', $unit->id)->get();

        return view('master.units.show', compact('unit', 'childUnits'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        // Get all base units except current unit
        $baseUnits = Unit::whereNull('base_unit_id')
                        ->where('id', '!=', $unit->id)
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get();

        return view('master.units.edit', compact('unit', 'baseUnits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'short_code' => 'required|string|max:10|unique:units,short_code,' . $unit->id,
            'type' => 'required|in:base,volume,weight,length,area,other',
            'base_unit_conversion' => 'nullable|numeric|min:0',
            'base_unit_id' => 'nullable|exists:units,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if ($validated['base_unit_id'] == $unit->id) {
            return back()->withErrors(['base_unit_id' => 'Unit cannot be its own base unit.']);
        }

        // Set default conversion rate
        if (empty($validated['base_unit_conversion'])) {
            $validated['base_unit_conversion'] = 1;
        }

        // Add audit fields
        $validated['updated_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        $unit->update($validated);

        return redirect()
            ->route('master.units.index')
            ->with('success', 'Unit successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        // Check if unit is used as base unit by other units
        $childUnitsCount = Unit::where('base_unit_id', $unit->id)->count();
        
        if ($childUnitsCount > 0) {
            return back()->withErrors([
                'delete' => "Cannot delete this unit. It is being used as a base unit by {$childUnitsCount} other unit(s)."
            ]);
        }

        // Soft delete
        $unit->delete();

        return redirect()
            ->route('master.units.index')
            ->with('success', 'Unit successfully deleted!');
    }
}