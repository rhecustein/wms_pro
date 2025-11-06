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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error loading sales orders list: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memuat daftar Sales Order. Silakan coba lagi.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            
            if ($warehouses->isEmpty()) {
                return redirect()->route('outbound.sales-orders.index')
                    ->with('warning', 'Tidak ada gudang aktif. Silakan aktifkan gudang terlebih dahulu.');
            }

            $customers = Customer::where('is_active', true)
                ->select('id', 'code', 'name', 'company_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'customer_type')
                ->orderBy('name')
                ->get();

            if ($customers->isEmpty()) {
                return redirect()->route('outbound.sales-orders.index')
                    ->with('warning', 'Tidak ada customer aktif. Silakan tambahkan customer terlebih dahulu.');
            }

            $products = Product::with('unit:id,name,short_code')
                ->where('is_active', true)
                ->select('id', 'sku', 'barcode', 'name', 'description', 'selling_price', 'current_stock', 'unit_id', 'type', 'image')
                ->orderBy('name')
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->selling_price,
                        'stock' => $product->current_stock ?? 0,
                        'unit' => $product->unit ? $product->unit->short_code : 'pcs',
                        'unit_name' => $product->unit ? $product->unit->name : 'Pieces',
                        'type' => $product->type,
                        'image' => $product->image,
                    ];
                });

            if ($products->isEmpty()) {
                return redirect()->route('outbound.sales-orders.index')
                    ->with('warning', 'Tidak ada produk aktif. Silakan tambahkan produk terlebih dahulu.');
            }

            $soNumber = SalesOrder::generateSONumber();

            return view('outbound.sales-orders.create', compact(
                'warehouses',
                'customers',
                'products',
                'soNumber'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading create sales order form: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            return redirect()->route('outbound.sales-orders.index')
                ->with('error', 'Terjadi kesalahan saat memuat form. Silakan coba lagi.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'customer_id' => 'required|exists:customers,id',
                'order_date' => 'required|date',
                'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
                'shipping_address' => 'nullable|string|max:500',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_province' => 'nullable|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.notes' => 'nullable|string|max:500',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'shipping_cost' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
            ], [
                'warehouse_id.required' => 'Gudang harus dipilih',
                'warehouse_id.exists' => 'Gudang tidak valid',
                'customer_id.required' => 'Customer harus dipilih',
                'customer_id.exists' => 'Customer tidak valid',
                'order_date.required' => 'Tanggal order harus diisi',
                'order_date.date' => 'Format tanggal tidak valid',
                'requested_delivery_date.date' => 'Format tanggal pengiriman tidak valid',
                'requested_delivery_date.after_or_equal' => 'Tanggal pengiriman tidak boleh lebih awal dari tanggal order',
                'items.required' => 'Minimal harus ada 1 item produk',
                'items.min' => 'Minimal harus ada 1 item produk',
                'items.*.product_id.required' => 'Produk harus dipilih',
                'items.*.product_id.exists' => 'Produk tidak valid',
                'items.*.quantity.required' => 'Jumlah harus diisi',
                'items.*.quantity.min' => 'Jumlah minimal 1',
                'items.*.unit_price.required' => 'Harga harus diisi',
                'items.*.unit_price.min' => 'Harga tidak boleh negatif',
                'items.*.discount_rate.max' => 'Diskon rate maksimal 100%',
                'items.*.tax_rate.max' => 'Tax rate maksimal 100%',
            ]);

            DB::beginTransaction();

            // Validate warehouse is active
            $warehouse = Warehouse::find($validated['warehouse_id']);
            if (!$warehouse || !$warehouse->is_active) {
                throw new \Exception('Gudang tidak aktif atau tidak ditemukan');
            }

            // Validate customer is active
            $customer = Customer::find($validated['customer_id']);
            if (!$customer || !$customer->is_active) {
                throw new \Exception('Customer tidak aktif atau tidak ditemukan');
            }

            // Validate all products and stock
            foreach ($request->items as $index => $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                    throw new \Exception("Produk pada baris " . ($index + 1) . " tidak ditemukan");
                }
                
                if (!$product->is_active) {
                    throw new \Exception("Produk '{$product->name}' tidak aktif");
                }

                // Check stock for non-service products
                if ($product->type !== 'service' && $product->current_stock < $item['quantity']) {
                    throw new \Exception("Stok '{$product->name}' tidak mencukupi. Stok tersedia: {$product->current_stock}");
                }

                // Calculate line total
                $subtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $subtotal * (($item['discount_rate'] ?? 0) / 100);
                $afterDiscount = $subtotal - $discountAmount;
                $taxAmount = $afterDiscount * (($item['tax_rate'] ?? 0) / 100);
                $lineTotal = $afterDiscount + $taxAmount;

                // Validate line total
                if ($lineTotal < 0) {
                    throw new \Exception("Total pada produk '{$product->name}' tidak boleh negatif");
                }
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemSubtotal * (($item['discount_rate'] ?? 0) / 100);
                $afterDiscount = $itemSubtotal - $discountAmount;
                $taxAmount = $afterDiscount * (($item['tax_rate'] ?? 0) / 100);
                $lineTotal = $afterDiscount + $taxAmount;
                $subtotal += $lineTotal;
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $shippingCost = $request->shipping_cost ?? 0;

            // Validate discount not greater than subtotal
            if ($discountAmount > $subtotal) {
                throw new \Exception('Diskon total tidak boleh lebih besar dari subtotal');
            }

            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCost;

            // Validate total amount
            if ($totalAmount < 0) {
                throw new \Exception('Total amount tidak boleh negatif');
            }

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
                'currency' => $validated['currency'] ?? 'IDR',
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_province' => $validated['shipping_province'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create Sales Order Items
            foreach ($request->items as $item) {
                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $discountAmount = $itemSubtotal * (($item['discount_rate'] ?? 0) / 100);
                $afterDiscount = $itemSubtotal - $discountAmount;
                $taxAmount = $afterDiscount * (($item['tax_rate'] ?? 0) / 100);
                $lineTotal = $afterDiscount + $taxAmount;

                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_rate' => $item['discount_rate'] ?? 0,
                    'tax_rate' => $item['tax_rate'] ?? 0,
                    'line_total' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Sales Order created successfully', [
                'so_number' => $salesOrder->so_number,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('outbound.sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order berhasil dibuat!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali data yang diinput.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->except(['_token']),
                'user_id' => Auth::id()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal membuat Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $salesOrder)
    {
        try {
            $salesOrder->load(['warehouse', 'customer', 'items.product.unit', 'createdBy', 'updatedBy']);

            return view('outbound.sales-orders.show', compact('salesOrder'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('outbound.sales-orders.index')
                ->with('error', 'Sales Order tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error showing sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id ?? null
            ]);
            
            return redirect()->route('outbound.sales-orders.index')
                ->with('error', 'Terjadi kesalahan saat memuat Sales Order.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canEdit()) {
                return redirect()
                    ->route('outbound.sales-orders.show', $salesOrder)
                    ->with('error', 'Sales Order dengan status ' . $salesOrder->status . ' tidak dapat diedit.');
            }

            $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
            $customers = Customer::where('is_active', true)
                ->select('id', 'code', 'name', 'company_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'customer_type')
                ->orderBy('name')
                ->get();
            $products = Product::with('unit:id,name,short_code')
                ->where('is_active', true)
                ->select('id', 'sku', 'barcode', 'name', 'description', 'selling_price', 'current_stock', 'unit_id', 'type', 'image')
                ->orderBy('name')
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'barcode' => $product->barcode,
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->selling_price,
                        'stock' => $product->current_stock ?? 0,
                        'unit' => $product->unit ? $product->unit->short_code : 'pcs',
                        'unit_name' => $product->unit ? $product->unit->name : 'Pieces',
                        'type' => $product->type,
                        'image' => $product->image,
                    ];
                });
            $salesOrder->load('items.product.unit', 'customer');

            return view('outbound.sales-orders.edit', compact(
                'salesOrder',
                'warehouses',
                'customers',
                'products'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading edit sales order form: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id
            ]);
            
            return redirect()->route('outbound.sales-orders.show', $salesOrder)
                ->with('error', 'Terjadi kesalahan saat memuat form edit.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canEdit()) {
                return redirect()
                    ->route('outbound.sales-orders.show', $salesOrder)
                    ->with('error', 'Sales Order dengan status ' . $salesOrder->status . ' tidak dapat diedit.');
            }

            $validated = $request->validate([
                'warehouse_id' => 'required|exists:warehouses,id',
                'customer_id' => 'required|exists:customers,id',
                'order_date' => 'required|date',
                'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
                'shipping_address' => 'nullable|string|max:500',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_province' => 'nullable|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:1000',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.discount_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'items.*.notes' => 'nullable|string|max:500',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'shipping_cost' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
            ], [
                'warehouse_id.required' => 'Gudang harus dipilih',
                'customer_id.required' => 'Customer harus dipilih',
                'order_date.required' => 'Tanggal order harus diisi',
                'items.required' => 'Minimal harus ada 1 item produk',
                'items.min' => 'Minimal harus ada 1 item produk',
            ]);

            DB::beginTransaction();

            // Validate products
            foreach ($request->items as $index => $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product || !$product->is_active) {
                    throw new \Exception("Produk pada baris " . ($index + 1) . " tidak aktif atau tidak ditemukan");
                }

                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                if (($item['discount'] ?? 0) > $itemSubtotal) {
                    throw new \Exception("Diskon pada produk '{$product->name}' tidak boleh lebih besar dari subtotal item");
                }
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $itemTotal = ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0);
                $subtotal += $itemTotal;
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $shippingCost = $request->shipping_cost ?? 0;

            if ($discountAmount > $subtotal) {
                throw new \Exception('Diskon total tidak boleh lebih besar dari subtotal');
            }

            $totalAmount = $subtotal + $taxAmount - $discountAmount + $shippingCost;

            if ($totalAmount < 0) {
                throw new \Exception('Total amount tidak boleh negatif');
            }

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

            Log::info('Sales Order updated successfully', [
                'so_number' => $salesOrder->so_number,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('outbound.sales-orders.show', $salesOrder)
                ->with('success', 'Sales Order berhasil diupdate!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal. Silakan periksa kembali data yang diinput.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id,
                'user_id' => Auth::id()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canDelete()) {
                return back()->with('error', 'Sales Order dengan status ' . $salesOrder->status . ' tidak dapat dihapus.');
            }

            DB::beginTransaction();

            $soNumber = $salesOrder->so_number;
            $salesOrder->delete();

            DB::commit();

            Log::info('Sales Order deleted successfully', [
                'so_number' => $soNumber,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('outbound.sales-orders.index')
                ->with('success', 'Sales Order berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal menghapus Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Confirm sales order
     */
    public function confirm(SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canConfirm()) {
                return back()->with('error', 'Sales Order dengan status ' . $salesOrder->status . ' tidak dapat dikonfirmasi.');
            }

            DB::beginTransaction();

            // Validate stock availability before confirming
            foreach ($salesOrder->items as $item) {
                if ($item->product->type !== 'service' && $item->product->current_stock < $item->quantity) {
                    throw new \Exception("Stok '{$item->product->name}' tidak mencukupi. Stok tersedia: {$item->product->current_stock}");
                }
            }

            $salesOrder->update([
                'status' => 'confirmed',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Sales Order confirmed successfully', [
                'so_number' => $salesOrder->so_number,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Sales Order berhasil dikonfirmasi!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error confirming sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal mengkonfirmasi Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel sales order
     */
    public function cancel(SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canCancel()) {
                return back()->with('error', 'Sales Order dengan status ' . $salesOrder->status . ' tidak dapat dibatalkan.');
            }

            DB::beginTransaction();

            $salesOrder->update([
                'status' => 'cancelled',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Sales Order cancelled successfully', [
                'so_number' => $salesOrder->so_number,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Sales Order berhasil dibatalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error cancelling sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal membatalkan Sales Order: ' . $e->getMessage());
        }
    }

    /**
     * Generate picking list
     */
    public function generatePicking(SalesOrder $salesOrder)
    {
        try {
            if (!$salesOrder->canGeneratePicking()) {
                return back()->with('error', 'Tidak dapat generate picking list untuk Sales Order dengan status: ' . $salesOrder->status);
            }

            DB::beginTransaction();

            // Validate stock before generating picking
            foreach ($salesOrder->items as $item) {
                if ($item->product->type !== 'service' && $item->product->current_stock < $item->quantity) {
                    throw new \Exception("Stok '{$item->product->name}' tidak mencukupi. Stok tersedia: {$item->product->current_stock}");
                }
            }

            $salesOrder->update([
                'status' => 'picking',
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Picking list generated successfully', [
                'so_number' => $salesOrder->so_number,
                'user_id' => Auth::id()
            ]);

            return back()->with('success', 'Picking list berhasil digenerate!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error generating picking list: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id,
                'user_id' => Auth::id()
            ]);

            return back()->with('error', 'Gagal generate picking list: ' . $e->getMessage());
        }
    }

    /**
     * Print sales order
     */
    public function print(SalesOrder $salesOrder)
    {
        try {
            $salesOrder->load(['warehouse', 'customer', 'items.product.unit', 'createdBy']);

            return view('outbound.sales-orders.print', compact('salesOrder'));
        } catch (\Exception $e) {
            Log::error('Error printing sales order: ' . $e->getMessage(), [
                'exception' => $e,
                'sales_order_id' => $salesOrder->id
            ]);
            
            return redirect()->route('outbound.sales-orders.show', $salesOrder)
                ->with('error', 'Terjadi kesalahan saat memuat halaman print.');
        }
    }

    /**
     * Search customers for Select2 (AJAX)
     */
    public function searchCustomers(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $page = $request->get('page', 1);
            $perPage = 10;

            $query = Customer::query()
                ->where('is_active', true)
                ->orderBy('name');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $customers = $query->skip(($page - 1) * $perPage)
                              ->take($perPage)
                              ->get();

            $results = $customers->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'text' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'province' => $customer->province,
                    'postal_code' => $customer->postal_code,
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching customers: ' . $e->getMessage(), [
                'exception' => $e,
                'search' => $request->get('q')
            ]);

            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
                'error' => 'Terjadi kesalahan saat mencari customer'
            ], 500);
        }
    }

    /**
     * Search products for Select2 (AJAX)
     */
    public function searchProducts(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $page = $request->get('page', 1);
            $perPage = 10;

            $query = Product::with('unit:id,name,short_code')
                ->where('is_active', true)
                ->orderBy('name');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $products = $query->skip(($page - 1) * $perPage)
                             ->take($perPage)
                             ->get();

            $results = $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name . ' - ' . $product->sku,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price' => $product->selling_price,
                    'stock' => $product->current_stock ?? 0,
                    'unit' => $product->unit ? $product->unit->short_code : 'pcs',
                    'unit_name' => $product->unit ? $product->unit->name : 'Pieces',
                    'type' => $product->type,
                    'image' => $product->image,
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching products: ' . $e->getMessage(), [
                'exception' => $e,
                'search' => $request->get('q')
            ]);

            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
                'error' => 'Terjadi kesalahan saat mencari produk'
            ], 500);
        }
    }

    /**
     * Get customer details by ID (AJAX)
     */
    public function getCustomer($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'province' => $customer->province,
                    'postal_code' => $customer->postal_code,
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Customer tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting customer: ' . $e->getMessage(), [
                'exception' => $e,
                'customer_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data customer'
            ], 500);
        }
    }

    /**
     * Get product details by ID (AJAX)
     */
    public function getProduct($id)
    {
        try {
            $product = Product::with('unit:id,name,short_code')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price' => $product->selling_price,
                    'stock' => $product->current_stock ?? 0,
                    'unit' => $product->unit ? $product->unit->short_code : 'pcs',
                    'unit_name' => $product->unit ? $product->unit->name : 'Pieces',
                    'description' => $product->description,
                    'type' => $product->type,
                    'image' => $product->image,
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error getting product: ' . $e->getMessage(), [
                'exception' => $e,
                'product_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk'
            ], 500);
        }
    }
}