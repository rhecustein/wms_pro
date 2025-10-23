@extends('layouts.app')

@section('title', 'Execute Replenishment Task')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-play-circle text-green-600 mr-2"></i>
                Execute Replenishment Task
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $replenishmentTask->task_number }}</p>
        </div>
        <a href="{{ route('operations.replenishments.show', $replenishmentTask) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Details
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Execution Form --}}
        <div class="lg:col-span-2">
            {{-- Task Instructions --}}
            <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg mb-6">
                <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Task Instructions
                </h3>
                <ol class="list-decimal list-inside space-y-2 text-blue-800">
                    <li>Go to source location: <strong>{{ $replenishmentTask->fromStorageBin->bin_code }}</strong></li>
                    <li>Pick <strong>{{ number_format($replenishmentTask->quantity_suggested) }} {{ $replenishmentTask->unit_of_measure }}</strong> of <strong>{{ $replenishmentTask->product->name }}</strong></li>
                    @if($replenishmentTask->batch_number)
                        <li>Verify batch number: <strong>{{ $replenishmentTask->batch_number }}</strong></li>
                    @endif
                    <li>Move to destination: <strong>{{ $replenishmentTask->toStorageBin->bin_code }}</strong></li>
                    <li>Place items in the pick face location</li>
                    <li>Confirm quantity moved below</li>
                </ol>
            </div>

            {{-- Movement Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-route text-purple-600 mr-2"></i>
                    Movement Details
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- From Location --}}
                    <div class="p-4 bg-orange-50 rounded-lg border-2 border-orange-200">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">From Location</p>
                                <p class="text-lg font-bold text-gray-900">{{ $replenishmentTask->fromStorageBin->bin_code }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p><i class="fas fa-warehouse mr-2 text-orange-500"></i>{{ $replenishmentTask->warehouse->name }}</p>
                            @if($replenishmentTask->fromStorageBin->location)
                                <p class="mt-1"><i class="fas fa-location-arrow mr-2 text-orange-500"></i>{{ $replenishmentTask->fromStorageBin->location }}</p>
                            @endif
                            <p class="mt-1"><i class="fas fa-layer-group mr-2 text-orange-500"></i>High Rack</p>
                        </div>
                    </div>

                    {{-- To Location --}}
                    <div class="p-4 bg-green-50 rounded-lg border-2 border-green-200">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cube text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">To Location</p>
                                <p class="text-lg font-bold text-gray-900">{{ $replenishmentTask->toStorageBin->bin_code }}</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p><i class="fas fa-warehouse mr-2 text-green-500"></i>{{ $replenishmentTask->warehouse->name }}</p>
                            @if($replenishmentTask->toStorageBin->location)
                                <p class="mt-1"><i class="fas fa-location-arrow mr-2 text-green-500"></i>{{ $replenishmentTask->toStorageBin->location }}</p>
                            @endif
                            <p class="mt-1"><i class="fas fa-cube mr-2 text-green-500"></i>Pick Face</p>
                        </div>
                    </div>
                </div>

                {{-- Arrow Indicator --}}
                <div class="flex justify-center mb-6">
                    <div class="flex items-center">
                        <div class="text-orange-500">
                            <i class="fas fa-circle text-sm"></i>
                        </div>
                        <div class="flex-1 mx-4">
                            <div class="border-t-2 border-dashed border-gray-400 relative">
                                <i class="fas fa-arrow-right absolute -top-3 left-1/2 transform -translate-x-1/2 text-2xl text-gray-600"></i>
                            </div>
                        </div>
                        <div class="text-green-500">
                            <i class="fas fa-circle text-sm"></i>
                        </div>
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="p-4 bg-indigo-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-box text-indigo-600 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Product</p>
                            <p class="text-lg font-bold text-gray-900">{{ $replenishmentTask->product->name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ $replenishmentTask->product->sku ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold text-gray-500 uppercase">Quantity</p>
                            <p class="text-2xl font-bold text-indigo-600">{{ number_format($replenishmentTask->quantity_suggested) }}</p>
                            <p class="text-sm text-gray-600">{{ $replenishmentTask->unit_of_measure }}</p>
                        </div>
                    </div>
                    @if($replenishmentTask->batch_number)
                        <div class="mt-3 pt-3 border-t border-indigo-200">
                            <p class="text-sm"><span class="font-semibold text-gray-700">Batch:</span> <span class="text-gray-900 font-mono">{{ $replenishmentTask->batch_number }}</span></p>
                        </div>
                    @endif
                    @if($replenishmentTask->serial_number)
                        <div class="mt-2">
                            <p class="text-sm"><span class="font-semibold text-gray-700">Serial:</span> <span class="text-gray-900 font-mono">{{ $replenishmentTask->serial_number }}</span></p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Completion Form --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-check-double text-green-600 mr-2"></i>
                    Complete Task
                </h2>
                
                <form action="{{ route('operations.replenishments.complete', $replenishmentTask) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        {{-- Quantity Moved --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Quantity Moved <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    name="quantity_moved" 
                                    value="{{ old('quantity_moved', $replenishmentTask->quantity_suggested) }}" 
                                    class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('quantity_moved') border-red-500 @enderror" 
                                    placeholder="Enter quantity moved" 
                                    min="1" 
                                    max="{{ $replenishmentTask->quantity_suggested }}"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 text-sm">{{ $replenishmentTask->unit_of_measure }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Maximum: {{ number_format($replenishmentTask->quantity_suggested) }} {{ $replenishmentTask->unit_of_measure }}
                            </p>
                            @error('quantity_moved')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (Optional)
                            </label>
                            <textarea 
                                name="notes" 
                                rows="3" 
                                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 @error('notes') border-red-500 @enderror" 
                                placeholder="Enter any notes or issues encountered during the task..."
                            >{{ old('notes', $replenishmentTask->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmation Checkbox --}}
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="confirm" 
                                    name="confirm" 
                                    type="checkbox" 
                                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" 
                                    required
                                >
                            </div>
                            <div class="ml-3">
                                <label for="confirm" class="text-sm font-medium text-gray-700">
                                    I confirm that the quantity has been moved correctly and placed in the destination location
                                    <span class="text-red-500">*</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('operations.replenishments.show', $replenishmentTask) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Complete Task
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Task Status
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Current Status</p>
                        <div class="mt-1">{!! $replenishmentTask->status_badge !!}</div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Priority</p>
                        <div class="mt-1">{!! $replenishmentTask->priority_badge !!}</div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Trigger Type</p>
                        <div class="mt-1">{!! $replenishmentTask->trigger_type_badge !!}</div>
                    </div>
                </div>
            </div>

            {{-- Assignment Info --}}
            @if($replenishmentTask->assignedUser)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-check text-blue-600 mr-2"></i>
                        Assigned To
                    </h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $replenishmentTask->assignedUser->name }}</p>
                            <p class="text-xs text-gray-600">{{ $replenishmentTask->assignedUser->email }}</p>
                        </div>
                    </div>
                    @if($replenishmentTask->assigned_at)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-600">
                                <i class="fas fa-clock mr-1"></i>
                                Assigned: {{ $replenishmentTask->assigned_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Timeline
                </h3>
                <div class="space-y-3">
                    {{-- Created --}}
                    <div class="flex items-start">
                        <div class="w-6 h-6 bg-gray-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                            <i class="fas fa-plus text-gray-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-600">{{ $replenishmentTask->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    {{-- Assigned --}}
                    @if($replenishmentTask->assigned_at)
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                <i class="fas fa-user-check text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Assigned</p>
                                <p class="text-xs text-gray-600">{{ $replenishmentTask->assigned_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Started --}}
                    @if($replenishmentTask->started_at)
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-2 flex-shrink-0">
                                <i class="fas fa-play text-yellow-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">Started</p>
                                <p class="text-xs text-gray-600">{{ $replenishmentTask->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-sm font-bold text-yellow-800 mb-3 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-600 mr-2"></i>
                    Quick Tips
                </h3>
                <ul class="space-y-2 text-xs text-yellow-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Verify product and batch before picking</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Use proper lifting techniques for heavy items</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Ensure pick face is properly organized</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Report any discrepancies immediately</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection