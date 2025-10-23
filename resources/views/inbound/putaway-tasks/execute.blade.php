@extends('layouts.app')

@section('title', 'Execute Putaway Task')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-play-circle text-green-600 mr-2"></i>
                Execute Putaway Task
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $putawayTask->task_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('inbound.putaway-tasks.show', $putawayTask) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Details
            </a>
        </div>
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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Task Progress --}}
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold">
                        <i class="fas fa-tasks mr-2"></i>
                        Task Execution
                    </h2>
                    {!! str_replace(['bg-', 'text-'], ['bg-white/20 text-', 'text-white '], $putawayTask->status_badge) !!}
                </div>
                
                <div class="grid grid-cols-3 gap-4 mt-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($putawayTask->quantity) }}</div>
                        <div class="text-sm opacity-90">Units to Move</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="text-sm opacity-90">Storage Bin</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">
                            @if($putawayTask->priority === 'high')
                                <i class="fas fa-exclamation-circle"></i>
                            @elseif($putawayTask->priority === 'medium')
                                <i class="fas fa-minus-circle"></i>
                            @else
                                <i class="fas fa-arrow-down"></i>
                            @endif
                        </div>
                        <div class="text-sm opacity-90">{{ ucfirst($putawayTask->priority) }} Priority</div>
                    </div>
                </div>
            </div>

            {{-- Product Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">
                    <i class="fas fa-box text-indigo-600 mr-2"></i>
                    Product Details
                </h2>

                <div class="flex items-start space-x-6">
                    <div class="w-24 h-24 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-4xl text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $putawayTask->product->name }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">SKU</p>
                                <p class="text-base font-semibold text-gray-900">{{ $putawayTask->product->sku ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Quantity</p>
                                <p class="text-base font-semibold text-gray-900">{{ number_format($putawayTask->quantity) }} {{ $putawayTask->unit_of_measure }}</p>
                            </div>
                            @if($putawayTask->batch_number)
                                <div>
                                    <p class="text-sm text-gray-500">Batch Number</p>
                                    <p class="text-base font-mono font-semibold text-gray-900">{{ $putawayTask->batch_number }}</p>
                                </div>
                            @endif
                            @if($putawayTask->serial_number)
                                <div>
                                    <p class="text-sm text-gray-500">Serial Number</p>
                                    <p class="text-base font-mono font-semibold text-gray-900">{{ $putawayTask->serial_number }}</p>
                                </div>
                            @endif
                            @if($putawayTask->packaging_type)
                                <div>
                                    <p class="text-sm text-gray-500">Packaging</p>
                                    <p class="text-base font-semibold text-gray-900">{{ $putawayTask->packaging_type }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location Movement Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">
                    <i class="fas fa-route text-purple-600 mr-2"></i>
                    Movement Instructions
                </h2>

                <div class="space-y-6">
                    {{-- From Location --}}
                    <div class="relative">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-2xl text-orange-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-orange-700 mb-1">PICK FROM</p>
                                <p class="text-2xl font-bold text-orange-900">{{ $putawayTask->from_location }}</p>
                                <p class="text-sm text-gray-600 mt-1">Current staging location</p>
                            </div>
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <div class="flex justify-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-arrow-down text-2xl text-gray-600"></i>
                        </div>
                    </div>

                    {{-- To Location --}}
                    <div class="relative">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-layer-group text-2xl text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-700 mb-1">PUT TO</p>
                                <p class="text-2xl font-bold text-green-900">
                                    {{ $putawayTask->storageBin->bin_code ?? 'Select Bin Below' }}
                                </p>
                                @if($putawayTask->storageBin)
                                    <p class="text-sm text-gray-600 mt-1">
                                        Aisle: {{ $putawayTask->storageBin->aisle }} | 
                                        Rack: {{ $putawayTask->storageBin->rack }} | 
                                        Level: {{ $putawayTask->storageBin->level }}
                                    </p>
                                @else
                                    <p class="text-sm text-red-600 mt-1">⚠️ Storage bin not assigned yet</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Complete Task Form --}}
            @if($putawayTask->status === 'in_progress')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-6">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        Complete Task
                    </h2>

                    <form action="{{ route('inbound.putaway-tasks.complete', $putawayTask) }}" method="POST">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Storage Bin <span class="text-red-500">*</span>
                                </label>
                                <select name="to_storage_bin_id" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Storage Bin</option>
                                    @foreach($availableBins as $bin)
                                        <option value="{{ $bin->id }}" {{ $putawayTask->to_storage_bin_id == $bin->id ? 'selected' : '' }}>
                                            {{ $bin->bin_code }} - {{ $bin->aisle }}/{{ $bin->rack }}/{{ $bin->level }}
                                            @if($bin->status !== 'active') ({{ ucfirst($bin->status) }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_storage_bin_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Scan or select the storage bin where items were placed
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Completion Notes
                                </label>
                                <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Any additional notes or observations...">{{ old('notes', $putawayTask->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold text-lg">
                                <i class="fas fa-check-circle mr-2"></i>Complete Putaway Task
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Additional Notes --}}
            @if($putawayTask->notes)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-yellow-900 mb-3">
                        <i class="fas fa-sticky-note mr-2"></i>
                        Task Notes
                    </h3>
                    <p class="text-yellow-800 whitespace-pre-line">{{ $putawayTask->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h2>

                <div class="space-y-3">
                    @if($putawayTask->status === 'assigned')
                        <form action="{{ route('inbound.putaway-tasks.start', $putawayTask) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                                <i class="fas fa-play mr-2"></i>Start Task
                            </button>
                        </form>
                    @endif

                    @if($putawayTask->status === 'in_progress')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                            <i class="fas fa-spinner fa-spin text-yellow-600 text-2xl mb-2"></i>
                            <p class="text-sm font-semibold text-yellow-800">Task In Progress</p>
                            <p class="text-xs text-yellow-700 mt-1">Complete the form below to finish</p>
                        </div>
                    @endif

                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-times mr-2"></i>Cancel Task
                    </button>

                    <a href="{{ route('inbound.putaway-tasks.show', $putawayTask) }}" class="block w-full px-4 py-3 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition font-semibold">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                </div>
            </div>

            {{-- Task Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Task Information
                </h2>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Task Number</p>
                        <p class="text-base font-mono font-bold text-gray-900">{{ $putawayTask->task_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Warehouse</p>
                        <p class="text-base font-semibold text-gray-900">{{ $putawayTask->warehouse->name }}</p>
                        <p class="text-xs text-gray-600">{{ $putawayTask->warehouse->code }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Good Receiving</p>
                        <a href="{{ route('inbound.good-receivings.show', $putawayTask->goodReceiving) }}" class="text-base font-semibold text-blue-600 hover:text-blue-800">
                            {{ $putawayTask->goodReceiving->gr_number }}
                        </a>
                    </div>

                    @if($putawayTask->assignedUser)
                        <div>
                            <p class="text-sm text-gray-500">Assigned To</p>
                            <div class="flex items-center mt-1">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $putawayTask->assignedUser->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($putawayTask->pallet)
                        <div>
                            <p class="text-sm text-gray-500">Pallet</p>
                            <p class="text-base font-mono font-semibold text-gray-900">{{ $putawayTask->pallet->pallet_code }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-clock text-purple-600 mr-2"></i>
                    Timeline
                </h2>

                <div class="space-y-4">
                    <div class="flex">
                        <div class="flex flex-col items-center mr-4">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <div class="w-0.5 h-full bg-gray-300"></div>
                        </div>
                        <div class="pb-4">
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-600">{{ $putawayTask->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if($putawayTask->assigned_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <div class="w-0.5 h-full bg-gray-300"></div>
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-semibold text-gray-900">Assigned</p>
                                <p class="text-xs text-gray-600">{{ $putawayTask->assigned_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($putawayTask->started_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                @if(!$putawayTask->completed_at)
                                    <div class="w-0.5 h-full bg-gray-300"></div>
                                @endif
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-semibold text-gray-900">Started</p>
                                <p class="text-xs text-gray-600">{{ $putawayTask->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(!$putawayTask->started_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-400">Not Started Yet</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Safety Tips --}}
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-3">
                    <i class="fas fa-hard-hat mr-2"></i>
                    Safety Reminders
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Verify product and quantity before moving</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Use proper lifting techniques</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Ensure storage bin is accessible and safe</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-0.5"></i>
                        <span>Double-check bin location before placing items</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Cancel Task</h3>
            <button onclick="document.getElementById('cancelModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('inbound.putaway-tasks.cancel', $putawayTask) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation <span class="text-red-500">*</span></label>
                <textarea name="notes" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Please provide a reason..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Close
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Cancel Task
                </button>
            </div>
        </form>
    </div>
</div>

@endsection