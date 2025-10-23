{{-- resources/views/master/products/stock-summary.blade.php --}}
@extends('layouts.app')

@section('title', 'Stock Summary - ' . $product->name)

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Stock Summary
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $product->name }} ({{ $product->sku }})</p>
        </div>
        <a href="{{ route('master.products.show', $product) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Product
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Stock</p>
                    <p class="text-2xl font-bold text-gray-900">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Available</p>
                    <p class="text-2xl font-bold text-green-600">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Reserved</p>
                    <p class="text-2xl font-bold text-orange-600">0</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">In Transit</p>
                    <p class="text-2xl font-bold text-purple-600">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock by Warehouse --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-warehouse text-blue-600 mr-2"></i>Stock by Warehouse
        </h3>
        
        <div class="text-center py-12">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-bar text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Data Available</h3>
            <p class="text-gray-600">Stock information will appear here once inventory is added</p>
        </div>
    </div>

</div>
@endsection