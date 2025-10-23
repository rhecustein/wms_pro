<?php
// app/Http/Controllers/Master/CustomerController.php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by customer type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get unique cities for filter
        $cities = Customer::whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->sort();

        return view('master.customers.index', compact('customers', 'cities'));
    }

    public function create()
    {
        $code = Customer::generateCode();
        return view('master.customers.create', compact('code'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers,code',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'required|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'customer_type' => 'required|in:regular,vip,wholesale',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $customer = Customer::create($validated);

        return redirect()
            ->route('master.customers.index')
            ->with('success', 'Customer created successfully!');
    }

    public function show(Customer $customer)
    {
        return view('master.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('master.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers,code,' . $customer->id,
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'required|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'customer_type' => 'required|in:regular,vip,wholesale',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'required|integer|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect()
            ->route('master.customers.index')
            ->with('success', 'Customer updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('master.customers.index')
            ->with('success', 'Customer deleted successfully!');
    }

    public function activate(Customer $customer)
    {
        $customer->update(['is_active' => true]);

        return back()->with('success', 'Customer activated successfully!');
    }

    public function deactivate(Customer $customer)
    {
        $customer->update(['is_active' => false]);

        return back()->with('success', 'Customer deactivated successfully!');
    }

    public function orders(Customer $customer)
    {
        // Placeholder for orders view
        return view('master.customers.orders', compact('customer'));
    }

    public function stock(Customer $customer)
    {
        // Placeholder for stock view
        return view('master.customers.stock', compact('customer'));
    }
}