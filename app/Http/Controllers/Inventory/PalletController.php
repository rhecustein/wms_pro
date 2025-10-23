<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Pallet;
use App\Models\StorageBin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pallet::with(['storageBin.warehouse', 'createdBy', 'updatedBy'])
            ->orderBy('created_at', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pallet_number', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pallet Type Filter
        if ($request->filled('pallet_type')) {
            $query->where('pallet_type', $request->pallet_type);
        }

        // Condition Filter
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Availability Filter
        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Storage Bin Filter
        if ($request->filled('storage_bin_id')) {
            $query->where('storage_bin_id', $request->storage_bin_id);
        }

        $pallets = $query->paginate(15)->withQueryString();

        // Get filter options
        $storageBins = StorageBin::with('warehouse')->where('is_active', true)->orderBy('code')->get();
        $statuses = ['empty', 'loaded', 'in_transit', 'damaged'];
        $types = ['standard', 'euro', 'custom'];
        $conditions = ['good', 'fair', 'poor', 'damaged'];

        return view('inventory.pallets.index', compact(
            'pallets',
            'storageBins',
            'statuses',
            'types',
            'conditions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $storageBins = StorageBin::with('warehouse')->where('is_active', true)->orderBy('code')->get();
        
        return view('inventory.pallets.create', compact('storageBins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pallet_type' => 'required|in:standard,euro,custom',
            'barcode' => 'nullable|string|unique:pallets,barcode',
            'qr_code' => 'nullable|string|unique:pallets,qr_code',
            'width_cm' => 'required|numeric|min:0',
            'depth_cm' => 'required|numeric|min:0',
            'height_cm' => 'required|numeric|min:0',
            'max_weight_kg' => 'required|numeric|min:0',
            'storage_bin_id' => 'nullable|exists:storage_bins,id',
            'status' => 'required|in:empty,loaded,in_transit,damaged',
            'condition' => 'required|in:good,fair,poor,damaged',
            'notes' => 'nullable|string',
        ]);

        // Generate pallet number
        $lastPallet = Pallet::withTrashed()->latest('id')->first();
        $nextNumber = $lastPallet ? (int)substr($lastPallet->pallet_number, 4) + 1 : 1;
        $validated['pallet_number'] = 'PLT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Auto-generate barcode if not provided
        if (empty($validated['barcode'])) {
            $validated['barcode'] = 'BC-' . strtoupper(Str::random(10));
        }

        // Auto-generate QR code if not provided
        if (empty($validated['qr_code'])) {
            $validated['qr_code'] = 'QR-' . strtoupper(Str::random(12));
        }

        $validated['created_by'] = Auth::id();
        $validated['is_available'] = $validated['status'] === 'empty';

        $pallet = Pallet::create($validated);

        return redirect()->route('inventory.pallets.show', $pallet)
            ->with('success', 'Pallet created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pallet $pallet)
    {
        $pallet->load(['storageBin.warehouse', 'createdBy', 'updatedBy']);

        return view('inventory.pallets.show', compact('pallet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pallet $pallet)
    {
        $storageBins = StorageBin::with('warehouse')->where('is_active', true)->orderBy('code')->get();
        
        return view('inventory.pallets.edit', compact('pallet', 'storageBins'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pallet $pallet)
    {
        $validated = $request->validate([
            'pallet_type' => 'required|in:standard,euro,custom',
            'barcode' => 'nullable|string|unique:pallets,barcode,' . $pallet->id,
            'qr_code' => 'nullable|string|unique:pallets,qr_code,' . $pallet->id,
            'width_cm' => 'required|numeric|min:0',
            'depth_cm' => 'required|numeric|min:0',
            'height_cm' => 'required|numeric|min:0',
            'max_weight_kg' => 'required|numeric|min:0',
            'current_weight_kg' => 'required|numeric|min:0',
            'storage_bin_id' => 'nullable|exists:storage_bins,id',
            'status' => 'required|in:empty,loaded,in_transit,damaged',
            'condition' => 'required|in:good,fair,poor,damaged',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_available'] = $validated['status'] === 'empty';

        $pallet->update($validated);

        return redirect()->route('inventory.pallets.show', $pallet)
            ->with('success', 'Pallet updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pallet $pallet)
    {
        $pallet->delete();

        return redirect()->route('inventory.pallets.index')
            ->with('success', 'Pallet deleted successfully!');
    }

    /**
     * Activate pallet
     */
    public function activate(Pallet $pallet)
    {
        $pallet->update([
            'is_available' => true,
            'status' => 'empty',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Pallet activated successfully!');
    }

    /**
     * Deactivate pallet
     */
    public function deactivate(Pallet $pallet)
    {
        $pallet->update([
            'is_available' => false,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Pallet deactivated successfully!');
    }

    /**
     * Show pallet history
     */
    public function history(Pallet $pallet)
    {
        $pallet->load(['storageBin.warehouse', 'createdBy', 'updatedBy']);

        // You can add actual movement history here if you have a pallet_movements table
        // For now, we'll just show the pallet details
        
        return view('inventory.pallets.history', compact('pallet'));
    }

    /**
     * Generate multiple pallets
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'pallet_type' => 'required|in:standard,euro,custom',
            'width_cm' => 'required|numeric|min:0',
            'depth_cm' => 'required|numeric|min:0',
            'height_cm' => 'required|numeric|min:0',
            'max_weight_kg' => 'required|numeric|min:0',
            'condition' => 'required|in:good,fair,poor,damaged',
        ]);

        $lastPallet = Pallet::withTrashed()->latest('id')->first();
        $startNumber = $lastPallet ? (int)substr($lastPallet->pallet_number, 4) + 1 : 1;

        $pallets = [];
        for ($i = 0; $i < $validated['quantity']; $i++) {
            $pallets[] = [
                'pallet_number' => 'PLT-' . str_pad($startNumber + $i, 5, '0', STR_PAD_LEFT),
                'pallet_type' => $validated['pallet_type'],
                'barcode' => 'BC-' . strtoupper(Str::random(10)),
                'qr_code' => 'QR-' . strtoupper(Str::random(12)),
                'width_cm' => $validated['width_cm'],
                'depth_cm' => $validated['depth_cm'],
                'height_cm' => $validated['height_cm'],
                'max_weight_kg' => $validated['max_weight_kg'],
                'current_weight_kg' => 0,
                'status' => 'empty',
                'is_available' => true,
                'condition' => $validated['condition'],
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Pallet::insert($pallets);

        return redirect()->route('inventory.pallets.index')
            ->with('success', "{$validated['quantity']} pallets generated successfully!");
    }
}