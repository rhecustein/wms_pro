<?php
// app/Http/Controllers/Master/VendorController.php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::with(['createdBy', 'updatedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // City Filter
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Country Filter
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        $vendors = $query->latest()->paginate(15)->withQueryString();
        
        // Get unique cities for filter
        $cities = Vendor::whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        // Get unique countries for filter
        $countries = Vendor::whereNotNull('country')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('master.vendors.index', compact('vendors', 'cities', 'countries'));
    }

    public function create()
    {
        return view('master.vendors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:vendors,code',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'payment_terms_days' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        Vendor::create($validated);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['createdBy', 'updatedBy']);
        return view('master.vendors.show', compact('vendor'));
    }

    public function edit(Vendor $vendor)
    {
        return view('master.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:vendors,code,' . $vendor->id,
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'payment_terms_days' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $vendor->update($validated);

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('master.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    public function activate(Vendor $vendor)
    {
        $vendor->update(['is_active' => true, 'updated_by' => auth()->id()]);
        return back()->with('success', 'Vendor activated successfully.');
    }

    public function deactivate(Vendor $vendor)
    {
        $vendor->update(['is_active' => false, 'updated_by' => auth()->id()]);
        return back()->with('success', 'Vendor deactivated successfully.');
    }

    public function purchaseOrders(Vendor $vendor)
    {
        // Load vendor with relationships
        $vendor->load(['createdBy', 'updatedBy']);
        
        // TODO: Implement purchase orders logic
        // $purchaseOrders = $vendor->purchaseOrders()->latest()->paginate(15);
        
        return view('master.vendors.purchase-orders', compact('vendor'));
    }
}