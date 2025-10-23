{{-- resources/views/master/products/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-600 mr-2"></i>
                Edit Product
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update product information</p>
        </div>
        <a href="{{ route('master.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('master.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Main Information --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- SKU --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('sku') border-red-500 @enderror">
                            @error('sku')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Barcode --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('barcode') border-red-500 @enderror">
                            @error('barcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Unit of Measure --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure <span class="text-red-500">*</span></label>
                            <select name="unit_of_measure" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('unit_of_measure') border-red-500 @enderror">
                                <option value="pcs" {{ old('unit_of_measure', $product->unit_of_measure) == 'pcs' ? 'selected' : '' }}>PCS</option>
                                <option value="box" {{ old('unit_of_measure', $product->unit_of_measure) == 'box' ? 'selected' : '' }}>Box</option>
                                <option value="pallet" {{ old('unit_of_measure', $product->unit_of_measure) == 'pallet' ? 'selected' : '' }}>Pallet</option>
                                <option value="kg" {{ old('unit_of_measure', $product->unit_of_measure) == 'kg' ? 'selected' : '' }}>KG</option>
                                <option value="liter" {{ old('unit_of_measure', $product->unit_of_measure) == 'liter' ? 'selected' : '' }}>Liter</option>
                            </select>
                            @error('unit_of_measure')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Physical Properties --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-ruler-combined text-blue-600 mr-2"></i>Physical Properties
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- Weight --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Weight (KG)</label>
                            <input type="number" step="0.01" name="weight_kg" value="{{ old('weight_kg', $product->weight_kg) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('weight_kg') border-red-500 @enderror">
                            @error('weight_kg')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Length --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Length (CM)</label>
                            <input type="number" step="0.01" name="length_cm" value="{{ old('length_cm', $product->length_cm) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('length_cm') border-red-500 @enderror">
                            @error('length_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Width --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Width (CM)</label>
                            <input type="number" step="0.01" name="width_cm" value="{{ old('width_cm', $product->width_cm) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('width_cm') border-red-500 @enderror">
                            @error('width_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Height --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Height (CM)</label>
                            <input type="number" step="0.01" name="height_cm" value="{{ old('height_cm', $product->height_cm) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('height_cm') border-red-500 @enderror">
                            @error('height_cm')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Packaging Type --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Packaging Type <span class="text-red-500">*</span></label>
                            <select name="packaging_type" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('packaging_type') border-red-500 @enderror">
                                <option value="carton" {{ old('packaging_type', $product->packaging_type) == 'carton' ? 'selected' : '' }}>Carton</option>
                                <option value="drum" {{ old('packaging_type', $product->packaging_type) == 'drum' ? 'selected' : '' }}>Drum</option>
                                <option value="pallet" {{ old('packaging_type', $product->packaging_type) == 'pallet' ? 'selected' : '' }}>Pallet</option>
                                <option value="bag" {{ old('packaging_type', $product->packaging_type) == 'bag' ? 'selected' : '' }}>Bag</option>
                                <option value="bulk" {{ old('packaging_type', $product->packaging_type) == 'bulk' ? 'selected' : '' }}>Bulk</option>
                            </select>
                            @error('packaging_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Inventory Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cogs text-blue-600 mr-2"></i>Inventory Settings
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- Reorder Level --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reorder Level</label>
                            <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('reorder_level') border-red-500 @enderror">
                            @error('reorder_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Reorder Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reorder Quantity</label>
                            <input type="number" name="reorder_quantity" value="{{ old('reorder_quantity', $product->reorder_quantity) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('reorder_quantity') border-red-500 @enderror">
                            @error('reorder_quantity')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Min Stock Level --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Stock Level</label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('min_stock_level') border-red-500 @enderror">
                            @error('min_stock_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Max Stock Level --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Stock Level</label>
                            <input type="number" name="max_stock_level" value="{{ old('max_stock_level', $product->max_stock_level) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('max_stock_level') border-red-500 @enderror">
                            @error('max_stock_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Shelf Life Days --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Shelf Life (Days)</label>
                            <input type="number" name="shelf_life_days" value="{{ old('shelf_life_days', $product->shelf_life_days) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('shelf_life_days') border-red-500 @enderror">
                            @error('shelf_life_days')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Temperature Settings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-temperature-low text-blue-600 mr-2"></i>Temperature Settings
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Temperature Min --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Temperature (°C)</label>
                            <input type="number" step="0.01" name="temperature_min" value="{{ old('temperature_min', $product->temperature_min) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('temperature_min') border-red-500 @enderror">
                            @error('temperature_min')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Temperature Max --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Temperature (°C)</label>
                            <input type="number" step="0.01" name="temperature_max" value="{{ old('temperature_max', $product->temperature_max) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('temperature_max') border-red-500 @enderror">
                            @error('temperature_max')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                
                {{-- Product Image --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-image text-blue-600 mr-2"></i>Product Image
                    </h3>
                    
                    <div class="mb-4">
                        <div id="image-preview" class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-4 overflow-hidden">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-box text-6xl text-gray-400"></i>
                            @endif
                        </div>
                        <input type="file" name="image" id="image-input" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('image') border-red-500 @enderror">
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @if($product->image)
                            <p class="text-xs text-gray-500 mt-2">Current image will be replaced if you upload a new one</p>
                        @endif
                    </div>
                </div>

                {{-- Tracking Options --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>Tracking Options
                    </h3>
                    
                    <div class="space-y-3">
                        {{-- Batch Tracked --}}
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_batch_tracked" value="1" {{ old('is_batch_tracked', $product->is_batch_tracked) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Batch Tracked</span>
                        </label>

                        {{-- Serial Tracked --}}
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_serial_tracked" value="1" {{ old('is_serial_tracked', $product->is_serial_tracked) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Serial Tracked</span>
                        </label>

                        {{-- Expiry Tracked --}}
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_expiry_tracked" value="1" {{ old('is_expiry_tracked', $product->is_expiry_tracked) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-3 text-sm text-gray-700">Expiry Tracked</span>
                        </label>

                        {{-- Hazmat --}}
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_hazmat" value="1" {{ old('is_hazmat', $product->is_hazmat) ? 'checked' : '' }} class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-3 text-sm text-gray-700">Hazardous Material</span>
                        </label>
                    </div>
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-toggle-on text-blue-600 mr-2"></i>Status
                    </h3>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="ml-3 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info text-blue-600 mr-2"></i>Metadata
                    </h3>
                    
                    <div class="space-y-2 text-sm">
                        @if($product->createdBy)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-plus w-5"></i>
                                <span class="ml-2">Created by: {{ $product->createdBy->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-plus w-5"></i>
                            <span class="ml-2">{{ $product->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($product->updatedBy)
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-edit w-5"></i>
                                <span class="ml-2">Updated by: {{ $product->updatedBy->name }}</span>
                            </div>
                        @endif
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar-check w-5"></i>
                            <span class="ml-2">{{ $product->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('master.products.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Update Product
            </button>
        </div>
    </form>

</div>

<script>
// Image Preview
document.getElementById('image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">`;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection