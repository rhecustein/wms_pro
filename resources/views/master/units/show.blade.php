@extends('layouts.app')

@section('title', 'Unit Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Unit Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View unit information and related data</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.units.edit', $unit) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('master.units.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Basic Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-ruler mr-2"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Unit Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $unit->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Short Code</label>
                            <p class="text-lg">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-800 font-mono font-semibold">
                                    {{ strtoupper($unit->short_code) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                            <p class="text-lg">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium 
                                    @if($unit->type === 'base') bg-purple-100 text-purple-800
                                    @elseif($unit->type === 'weight') bg-yellow-100 text-yellow-800
                                    @elseif($unit->type === 'volume') bg-cyan-100 text-cyan-800
                                    @elseif($unit->type === 'length') bg-green-100 text-green-800
                                    @elseif($unit->type === 'area') bg-pink-100 text-pink-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    <i class="fas 
                                        @if($unit->type === 'weight') fa-weight-hanging
                                        @elseif($unit->type === 'volume') fa-flask
                                        @elseif($unit->type === 'length') fa-ruler
                                        @elseif($unit->type === 'area') fa-border-all
                                        @else fa-cube
                                        @endif mr-2"></i>
                                    {{ ucfirst($unit->type) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <p class="text-lg">
                                @if($unit->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        Inactive
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @if($unit->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                            <p class="text-gray-700">{{ $unit->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Conversion Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Conversion Information
                    </h2>
                </div>
                <div class="p-6">
                    @if($unit->baseUnit)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Base Unit</p>
                                    <p class="text-lg font-semibold text-gray-900">
                                        {{ $unit->baseUnit->name }}
                                        <span class="text-sm text-gray-600">({{ strtoupper($unit->baseUnit->short_code) }})</span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 mb-1">Conversion Rate</p>
                                    <p class="text-lg font-semibold text-blue-600">
                                        1 {{ strtoupper($unit->short_code) }} = {{ number_format($unit->base_unit_conversion, 4) }} {{ strtoupper($unit->baseUnit->short_code) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 text-center">
                            <i class="fas fa-star text-3xl text-indigo-600 mb-2"></i>
                            <p class="text-lg font-semibold text-indigo-900">Base Unit</p>
                            <p class="text-sm text-indigo-700">This unit doesn't convert to another unit</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Child Units Card --}}
            @if($childUnits->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-sitemap mr-2"></i>
                        Derived Units ({{ $childUnits->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($childUnits as $child)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $child->name }}</p>
                                    <p class="text-sm text-gray-600">{{ strtoupper($child->short_code) }} - {{ ucfirst($child->type) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-purple-600">
                                        1 {{ strtoupper($child->short_code) }} = {{ number_format($child->base_unit_conversion, 4) }} {{ strtoupper($unit->short_code) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Meta Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        Meta Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-sm text-gray-900">{{ $unit->created_at->format('d M Y, H:i') }}</p>
                        @if($unit->createdBy)
                            <p class="text-xs text-gray-500">by {{ $unit->createdBy->name }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ $unit->updated_at->format('d M Y, H:i') }}</p>
                        @if($unit->updatedBy)
                            <p class="text-xs text-gray-500">by {{ $unit->updatedBy->name }}</p>
                        @endif
                    </div>
                    @if($unit->deleted_at)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Deleted At</label>
                        <p class="text-sm text-red-600">{{ $unit->deleted_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('master.units.edit', $unit) }}" 
                       class="flex items-center px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Unit
                    </a>
                    <button onclick="window.print()" 
                            class="w-full flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        <i class="fas fa-print mr-2"></i>
                        Print Details
                    </button>
                    <form action="{{ route('master.units.destroy', $unit) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this unit?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Unit
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection