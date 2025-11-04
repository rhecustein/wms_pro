@extends('layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Create Product
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new product to your inventory</p>
        </div>
        <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Error Messages --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                <div class="flex-1">
                    <strong class="font-semibold">Whoops! There were some problems with your input.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('master.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column - Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Basic Information --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Basic Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- SKU --}}
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-barcode mr-1"></i>SKU <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('sku') border-red-500 @enderror"
                                       placeholder="e.g., PRD-001">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Barcode --}}
                            <div>
                                <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-qrcode mr-1"></i>Barcode
                                </label>
                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('barcode') border-red-500 @enderror"
                                       placeholder="e.g., 1234567890123">
                                @error('barcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Product Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1"></i>Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                   placeholder="Enter product name">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1"></i>Description
                            </label>
                            <textarea name="description" id="description" rows="4"
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                      placeholder="Enter product description (optional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Category --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-layer-group mr-1"></i>Category
                                </label>
                                <select name="category_id" id="category_id"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Brand --}}
                            <div>
                                <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-copyright mr-1"></i>Brand
                                </label>
                                <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('brand') border-red-500 @enderror"
                                       placeholder="Enter brand name">
                                @error('brand')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Unit --}}
                            <div>
                                <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-ruler mr-1"></i>Unit <span class="text-red-500">*</span>
                                </label>
                                <select name="unit_id" id="unit_id" required
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('unit_id') border-red-500 @enderror">
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->short_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Supplier --}}
                            <div>
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-truck mr-1"></i>Supplier
                                </label>
                                <select name="supplier_id" id="supplier_id"
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('supplier_id') border-red-500 @enderror">
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Type --}}
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-boxes mr-1"></i>Product Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                                <option value="">-- Select Type --</option>
                                <option value="raw_material" {{ old('type') == 'raw_material' ? 'selected' : '' }}>Raw Material</option>
                                <option value="finished_goods" {{ old('type') == 'finished_goods' ? 'selected' : '' }}>Finished Goods</option>
                                <option value="spare_parts" {{ old('type') == 'spare_parts' ? 'selected' : '' }}>Spare Parts</option>
                                <option value="consumable" {{ old('type') == 'consumable' ? 'selected' : '' }}>Consumable</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            Pricing
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Purchase Price --}}
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-shopping-cart mr-1"></i>Purchase Price <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('purchase_price') border-red-500 @enderror"
                                       placeholder="0.00">
                                @error('purchase_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Selling Price --}}
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-1"></i>Selling Price <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price') }}" required
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror"
                                       placeholder="0.00">
                                @error('selling_price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Minimum Selling Price --}}
                            <div>
                                <label for="minimum_selling_price" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-arrow-down mr-1"></i>Min Selling Price
                                </label>
                                <input type="number" step="0.01" name="minimum_selling_price" id="minimum_selling_price" value="{{ old('minimum_selling_price') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stock Management --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-purple-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-warehouse mr-2"></i>
                            Stock Management
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-boxes mr-1"></i>Current Stock
                                </label>
                                <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', 0) }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="0">
                            </div>
                            <div>
                                <label for="minimum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-arrow-down mr-1"></i>Min Stock
                                </label>
                                <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="0">
                            </div>
                            <div>
                                <label for="maximum_stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-arrow-up mr-1"></i>Max Stock
                                </label>
                                <input type="number" name="maximum_stock" id="maximum_stock" value="{{ old('maximum_stock') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="0">
                            </div>
                            <div>
                                <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-sync-alt mr-1"></i>Reorder Level
                                </label>
                                <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level') }}"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Physical Properties --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-orange-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-cube mr-2"></i>
                            Physical Properties
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Weight --}}
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-weight-hanging mr-1"></i>Weight (kg)
                            </label>
                            <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>

                        {{-- Dimensions --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-ruler-combined mr-1"></i>Dimensions (cm)
                            </label>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <input type="number" step="0.01" name="length" id="length" value="{{ old('length') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Length">
                                </div>
                                <div>
                                    <input type="number" step="0.01" name="width" id="width" value="{{ old('width') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Width">
                                </div>
                                <div>
                                    <input type="number" step="0.01" name="height" id="height" value="{{ old('height') }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="Height">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tax Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-indigo-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-percentage mr-2"></i>
                            Tax Settings
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_taxable" value="1" {{ old('is_taxable') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-file-invoice-dollar text-blue-500 mr-1"></i>Product is Taxable
                            </span>
                        </label>

                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calculator mr-1"></i>Tax Rate (%)
                            </label>
                            <input type="number" step="0.01" name="tax_rate" id="tax_rate" value="{{ old('tax_rate') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                {{-- Additional Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-teal-500 to-teal-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-cog mr-2"></i>
                            Additional Settings
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center cursor-pointer p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <input type="checkbox" name="is_serialized" value="1" {{ old('is_serialized') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-hashtag text-green-500 mr-1"></i>Serial Tracked
                                </span>
                            </label>

                            <label class="flex items-center cursor-pointer p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                                <input type="checkbox" name="is_batch_tracked" value="1" {{ old('is_batch_tracked') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    <i class="fas fa-layer-group text-blue-500 mr-1"></i>Batch Tracked
                                </span>
                            </label>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-sticky-note mr-1"></i>Notes
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Additional notes (max 1000 characters)">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column - Sidebar --}}
            <div class="space-y-6">
                
                {{-- Product Image --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-pink-500 to-pink-600 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-image mr-2"></i>
                            Product Image
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col items-center">
                            <div id="image-preview" class="w-full h-48 mb-4 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                                <div class="text-center">
                                    <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">No image selected</p>
                                </div>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                            <button type="button" onclick="document.getElementById('image').click()" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-upload mr-2"></i>Upload Image
                            </button>
                            <p class="text-xs text-gray-500 mt-2">Max size: 2MB (JPG, PNG, GIF, WEBP)</p>
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-800 border-b">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-toggle-on mr-2"></i>
                            Status
                        </h3>
                    </div>
                    <div class="p-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm font-medium text-gray-700">
                                <i class="fas fa-check-circle text-green-500 mr-1"></i>Active
                            </span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500">Inactive products will not be available for transactions</p>
                    </div>
                </div>

                {{-- Quick Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Quick Tips</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>SKU must be unique</li>
                                <li>Enable tracking for better inventory control</li>
                                <li>Set reorder levels to automate purchasing</li>
                                <li>All required fields marked with <span class="text-red-500">*</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Form Actions --}}
        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('master.products.index') }}" 
               class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Create Product
            </button>
        </div>

    </form>

</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('image-preview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
        }
        reader.readAsDataURL(file);
    }
}
</script>
@endsection