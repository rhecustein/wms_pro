<?php

namespace App\Http\Controllers\Outbound;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalesOrder::with(['warehouse', 'customer', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('so_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment Status Filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Warehouse Filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $salesOrders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data for filters
        $statuses = ['draft', 'confirmed', 'picking', 'packing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'partial', 'paid'];
        $warehouses = Warehouse::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('outbound.sales-orders.index', compact(
            'salesOrders',
            'statuses',
            'paymentStatuses',
            'warehouses',
            'customers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $soNumber = SalesOrder::generateSONumber();

        return view('outbound.sales-orders.create', compact(
            'warehouses',
            'customers',
            'products',
            'soNumber'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_province' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $subtotal += $itemTotal;
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $shippingCost = $request->shipping_cost ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCost;

            // Create Sales Order
            $salesOrder = SalesOrder::create([
                'so_number' => SalesOrder::generateSONumber(),
                'warehouse_id' => $validated['warehouse_id'],
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'requested_delivery_date' => $validated['requested_delivery_date'],
                'status' => 'draft',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create Sales Order Items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $itemTotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['warehouse', 'customer', 'items.product', 'createdBy', 'updatedBy']);

        return view('outbound.sales-orders.show', compact('salesOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canEdit()) {
            return redirect()
                ->route('sales-orders.show', $salesOrder)
                ->with('error', 'Cannot edit Sales Order with status: ' . $salesOrder->status);
        }

        $warehouses = Warehouse::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $salesOrder->load('items.product');

        return view('outbound.sales-orders.edit', compact(
            'salesOrder',
            'warehouses',
            'customers',
            'products'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        if (!$salesOrder->canEdit()) {
            return redirect()
                ->route('sales-orders.show', $salesOrder)
                ->with('error', 'Cannot edit Sales Order with status: ' . $salesOrder->status);
        }

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'shipping_address' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_province' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $subtotal += $itemTotal;
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $shippingCost = $request->shipping_cost ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCost;

            // Update Sales Order
            $salesOrder->update([
                'warehouse_id' => $validated['warehouse_id'],
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'requested_delivery_date' => $validated['requested_delivery_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);

            // Delete existing items and create new ones
            $salesOrder->items()->delete();

            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'total' => $itemTotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canDelete()) {
            return back()->with('error', 'Cannot delete Sales Order with status: ' . $salesOrder->status);
        }

        try {
            $salesOrder->delete();
            return redirect()
                ->route('sales-orders.index')
                ->with('success', 'Sales Order deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Confirm sales order
     */
    public function confirm(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canConfirm()) {
            return back()->with('error', 'Cannot confirm Sales Order with status: ' . $salesOrder->status);
        }

        try {
            $salesOrder->update([
                'status' => 'confirmed',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Sales Order confirmed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to confirm Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel sales order
     */
    public function cancel(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canCancel()) {
            return back()->with('error', 'Cannot cancel Sales Order with status: ' . $salesOrder->status);
        }

        try {
            $salesOrder->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            return back()->with('success', 'Sales Order cancelled successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Generate picking list
     */
    public function generatePicking(SalesOrder $salesOrder)
    {
        if (!$salesOrder->canGeneratePicking()) {
            return back()->with('error', 'Cannot generate picking list for Sales Order with status: ' . $salesOrder->status);
        }

        try {
            // Update status to picking
            $salesOrder->update([
                'status' => 'picking',
                'updated_by' => Auth::id(),
            ]);

            // Here you can add logic to create picking list records
            // For now, we just change the status

            return back()->with('success', 'Picking list generated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate picking list: ' . $e->getMessage());
        }
    }

    /**
     * Print sales order
     */
    public function print(SalesOrder $salesOrder)
    {
        $salesOrder->load(['warehouse', 'customer', 'items.product', 'createdBy']);

        return view('outbound.sales-orders.print', compact('salesOrder'));
    }
}