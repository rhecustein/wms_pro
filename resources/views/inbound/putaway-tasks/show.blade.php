@extends('layouts.app')

@section('title', 'Putaway Task Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-dolly text-purple-600 mr-2"></i>
                Putaway Task Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $putawayTask->task_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('inbound.putaway-tasks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            
            @if(in_array($putawayTask->status, ['pending', 'assigned']))
                <a href="{{ route('inbound.putaway-tasks.edit', $putawayTask) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endif

            @if(in_array($putawayTask->status, ['assigned', 'in_progress']))
                <a href="{{ route('inbound.putaway-tasks.execute', $putawayTask) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-play-circle mr-2"></i>Execute Task
                </a>
            @endif
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
        
        {{-- Main Information --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Task Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Task Information
                    </h2>
                    <div class="flex space-x-2">
                        {!! $putawayTask->status_badge !!}
                        {!! $putawayTask->priority_badge !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Task Number</label>
                        <p class="mt-1 text-lg font-mono font-bold text-gray-900">{{ $putawayTask->task_number }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Good Receiving</label>
                        <p class="mt-1 text-base font-semibold text-gray-900">
                            <a href="{{ route('inbound.good-receivings.show', $putawayTask->goodReceiving) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $putawayTask->goodReceiving->gr_number }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Warehouse</label>
                        <p class="mt-1 text-base font-semibold text-gray-900">
                            {{ $putawayTask->warehouse->name }} ({{ $putawayTask->warehouse->code }})
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Created At</label>
                        <p class="mt-1 text-base text-gray-900">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>
                            {{ $putawayTask->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>

                    @if($putawayTask->suggested_by_system)
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-robot mr-2"></i>
                                    This task was automatically suggested by the system
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">
                    <i class="fas fa-box text-indigo-600 mr-2"></i>
                    Product Information
                </h2>

                <div class="flex items-start space-x-4 mb-6">
                    <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-2xl text-indigo-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">{{ $putawayTask->product->name }}</h3>
                        <p class="text-sm text-gray-600 mt-1">SKU: {{ $putawayTask->product->sku ?? '-' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Quantity</label>
                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($putawayTask->quantity) }}
                        </p>
                        <p class="text-sm text-gray-600">{{ $putawayTask->unit_of_measure }}</p>
                    </div>

                    @if($putawayTask->batch_number)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Batch Number</label>
                            <p class="mt-1 text-base font-mono font-semibold text-gray-900">{{ $putawayTask->batch_number }}</p>
                        </div>
                    @endif

                    @if($putawayTask->serial_number)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Serial Number</label>
                            <p class="mt-1 text-base font-mono font-semibold text-gray-900">{{ $putawayTask->serial_number }}</p>
                        </div>
                    @endif

                    @if($putawayTask->packaging_type)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Packaging Type</label>
                            <p class="mt-1 text-base text-gray-900">{{ $putawayTask->packaging_type }}</p>
                        </div>
                    @endif

                    @if($putawayTask->pallet)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Pallet</label>
                            <p class="mt-1 text-base font-semibold text-gray-900">{{ $putawayTask->pallet->pallet_code }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Location Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">
                    <i class="fas fa-map-marked-alt text-green-600 mr-2"></i>
                    Location Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <label class="text-sm font-medium text-orange-700 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            From Location
                        </label>
                        <p class="mt-2 text-xl font-bold text-orange-900">{{ $putawayTask->from_location }}</p>
                        <p class="text-sm text-orange-700 mt-1">Staging Area</p>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <label class="text-sm font-medium text-green-700 flex items-center">
                            <i class="fas fa-layer-group mr-2"></i>
                            To Storage Bin
                        </label>
                        <p class="mt-2 text-xl font-bold text-green-900">
                            {{ $putawayTask->storageBin->bin_code ?? 'Not Assigned Yet' }}
                        </p>
                        @if($putawayTask->storageBin)
                            <p class="text-sm text-green-700 mt-1">
                                {{ $putawayTask->storageBin->aisle }} - {{ $putawayTask->storageBin->rack }} - {{ $putawayTask->storageBin->level }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($putawayTask->notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                        Notes
                    </h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $putawayTask->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            
            {{-- Assignment Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-check text-blue-600 mr-2"></i>
                    Assignment
                </h2>

                @if($putawayTask->assignedUser)
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $putawayTask->assignedUser->name }}</p>
                            <p class="text-sm text-gray-600">{{ $putawayTask->assignedUser->email }}</p>
                        </div>
                    </div>
                    @if($putawayTask->assigned_at)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-clock mr-1"></i>
                            Assigned: {{ $putawayTask->assigned_at->format('d M Y, H:i') }}
                        </p>
                    @endif
                @else
                    <p class="text-gray-500 mb-4">Not assigned yet</p>
                @endif

                @if($putawayTask->status === 'pending')
                    <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>Assign Task
                    </button>
                @endif
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
                            @if($putawayTask->creator)
                                <p class="text-xs text-gray-500">by {{ $putawayTask->creator->name }}</p>
                            @endif
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
                                @if($putawayTask->assignedUser)
                                    <p class="text-xs text-gray-500">to {{ $putawayTask->assignedUser->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($putawayTask->started_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                <div class="w-0.5 h-full bg-gray-300"></div>
                            </div>
                            <div class="pb-4">
                                <p class="text-sm font-semibold text-gray-900">Started</p>
                                <p class="text-xs text-gray-600">{{ $putawayTask->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($putawayTask->completed_at)
                        <div class="flex">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Completed</p>
                                <p class="text-xs text-gray-600">{{ $putawayTask->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            @if(!in_array($putawayTask->status, ['completed', 'cancelled']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-tasks text-green-600 mr-2"></i>
                        Actions
                    </h2>

                    <div class="space-y-3">
                        @if($putawayTask->status === 'pending' || $putawayTask->status === 'assigned')
                            <form action="{{ route('inbound.putaway-tasks.start', $putawayTask) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-play mr-2"></i>Start Task
                                </button>
                            </form>
                        @endif

                        @if(!in_array($putawayTask->status, ['completed', 'cancelled']))
                            <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-times mr-2"></i>Cancel Task
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Assign Modal --}}
<div id="assignModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Assign Task</h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('inbound.putaway-tasks.assign', $putawayTask) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                <select name="assigned_to" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select User</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Assign
                </button>
            </div>
        </form>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation</label>
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