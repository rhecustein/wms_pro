@extends('layouts.app')

@section('title', 'Replenishment Task Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Replenishment Task Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $replenishment->task_number }}</p>
        </div>
        <div class="flex space-x-3">
            @if(in_array($replenishment->status, ['assigned', 'in_progress']))
                <a href="{{ route('operations.replenishments.execute', $replenishment) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-play-circle mr-2"></i>Execute Task
                </a>
            @endif
            @if(in_array($replenishment->status, ['pending', 'assigned']))
                <a href="{{ route('operations.replenishments.edit', $replenishment) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif
            <a href="{{ route('operations.replenishments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
                    Task Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Task Number</label>
                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $replenishment->task_number }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                        <div>{!! $replenishment->status_badge !!}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Priority</label>
                        <div>{!! $replenishment->priority_badge !!}</div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Trigger Type</label>
                        <div>{!! $replenishment->trigger_type_badge !!}</div>
                    </div>
                </div>
            </div>

            {{-- Product & Location Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-box text-indigo-600 mr-2"></i>
                    Product & Location
                </h2>
                
                {{-- Product --}}
                <div class="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Product</label>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-box text-indigo-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $replenishment->product->name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ $replenishment->product->sku ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Warehouse --}}
                <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Warehouse</label>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-warehouse text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-base font-semibold text-gray-900">{{ $replenishment->warehouse->name }}</p>
                            <p class="text-sm text-gray-600">{{ $replenishment->warehouse->code ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Movement Path --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-orange-50 rounded-lg border border-orange-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                            From (High Rack)
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $replenishment->fromStorageBin->bin_code }}</p>
                        @if($replenishment->fromStorageBin->location)
                            <p class="text-sm text-gray-600">{{ $replenishment->fromStorageBin->location }}</p>
                        @endif
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                            <i class="fas fa-cube text-green-500 mr-1"></i>
                            To (Pick Face)
                        </label>
                        <p class="text-lg font-semibold text-gray-900">{{ $replenishment->toStorageBin->bin_code }}</p>
                        @if($replenishment->toStorageBin->location)
                            <p class="text-sm text-gray-600">{{ $replenishment->toStorageBin->location }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quantity Details --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calculator text-green-600 mr-2"></i>
                    Quantity Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Suggested Quantity</label>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($replenishment->quantity_suggested) }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $replenishment->unit_of_measure }}</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Moved Quantity</label>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($replenishment->quantity_moved) }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $replenishment->unit_of_measure }}</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Remaining</label>
                        <p class="text-2xl font-bold text-yellow-600">{{ number_format($replenishment->quantity_suggested - $replenishment->quantity_moved) }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $replenishment->unit_of_measure }}</p>
                    </div>
                </div>

                @if($replenishment->batch_number || $replenishment->serial_number)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-200">
                        @if($replenishment->batch_number)
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Batch Number</label>
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ $replenishment->batch_number }}</p>
                            </div>
                        @endif
                        @if($replenishment->serial_number)
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Serial Number</label>
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ $replenishment->serial_number }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Notes --}}
            @if($replenishment->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $replenishment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Assignment Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-check text-blue-600 mr-2"></i>
                    Assignment
                </h2>
                
                @if($replenishment->assignedUser)
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg border border-blue-100 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $replenishment->assignedUser->name }}</p>
                            <p class="text-xs text-gray-600">{{ $replenishment->assignedUser->email }}</p>
                        </div>
                    </div>
                    @if($replenishment->assigned_at)
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-clock mr-1"></i>
                            Assigned: {{ $replenishment->assigned_at->format('d M Y, H:i') }}
                        </p>
                    @endif
                @else
                    <div class="text-center py-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-times text-3xl text-gray-300"></i>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Not assigned yet</p>
                        
                        @if($replenishment->status === 'pending')
                            <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-user-plus mr-2"></i>Assign Task
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Action Buttons --}}
                @if($replenishment->status === 'assigned')
                    <form action="{{ route('operations.replenishments.start', $replenishment) }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-play mr-2"></i>Start Task
                        </button>
                    </form>
                @endif

                @if(!in_array($replenishment->status, ['completed', 'cancelled']))
                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition mt-2">
                        <i class="fas fa-times-circle mr-2"></i>Cancel Task
                    </button>
                @endif
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-purple-600 mr-2"></i>
                    Timeline
                </h2>
                <div class="space-y-4">
                    {{-- Created --}}
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-plus text-gray-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Created</p>
                            <p class="text-xs text-gray-600">{{ $replenishment->created_at->format('d M Y, H:i') }}</p>
                            @if($replenishment->createdBy)
                                <p class="text-xs text-gray-500">by {{ $replenishment->createdBy->name }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Assigned --}}
                    @if($replenishment->assigned_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-user-check text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Assigned</p>
                                <p class="text-xs text-gray-600">{{ $replenishment->assigned_at->format('d M Y, H:i') }}</p>
                                @if($replenishment->assignedUser)
                                    <p class="text-xs text-gray-500">to {{ $replenishment->assignedUser->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Started --}}
                    @if($replenishment->started_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-play text-yellow-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Started</p>
                                <p class="text-xs text-gray-600">{{ $replenishment->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Completed --}}
                    @if($replenishment->completed_at)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Completed</p>
                                <p class="text-xs text-gray-600">{{ $replenishment->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Metadata --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                    Metadata
                </h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created At:</span>
                        <span class="font-semibold text-gray-900">{{ $replenishment->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Updated At:</span>
                        <span class="font-semibold text-gray-900">{{ $replenishment->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($replenishment->createdBy)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created By:</span>
                            <span class="font-semibold text-gray-900">{{ $replenishment->createdBy->name }}</span>
                        </div>
                    @endif
                    @if($replenishment->updatedBy)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Updated By:</span>
                            <span class="font-semibold text-gray-900">{{ $replenishment->updatedBy->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Assign Modal --}}
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assign Task</h3>
            <form action="{{ route('operations.replenishments.assign', $replenishment) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign To <span class="text-red-500">*</span></label>
                    <select name="assigned_to" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">Select User</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Cancel Task</h3>
            <form action="{{ route('operations.replenishments.cancel', $replenishment) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Enter reason..." required></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Close
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Cancel Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection