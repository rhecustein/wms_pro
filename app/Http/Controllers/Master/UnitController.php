<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Asumsi: Anda telah membuat Model Unit
        $units = Unit::paginate(10);
        return view('master.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'short_code' => 'required|string|max:10|unique:units',
            'type' => 'required|string|max:50',
        ]);

        Unit::create($request->all());

        return redirect()->route('master.units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('master.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $unit->id,
            'short_code' => 'required|string|max:10|unique:units,short_code,' . $unit->id,
            'type' => 'required|string|max:50',
        ]);

        $unit->update($request->all());

        return redirect()->route('master.units.index')->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('master.units.index')->with('success', 'Unit berhasil dihapus.');
    }
}