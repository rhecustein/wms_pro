@extends('layouts.app')

@section('title', 'Pending Putaway Tasks')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-clock text-yellow-600 mr-2"></i>
                Pending Putaway Tasks
            </h1>
            <p class="text-sm text-gray-600 mt-1">Tasks awaiting assignment or execution</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('inbound.putaway-tasks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-list mr-2"></i>All Tasks
            </a>
            <a href="{{ route('inbound.putaway-tasks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>New Task
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

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $putawayTasks->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Assigned</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ $putawayTasks->where('status', 'assigned')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-check text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">High Priority</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $putawayTasks->where('priority', 'high')->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Items</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ number_format($putawayTasks->sum('quantity')) }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('inbound.putaway-tasks.pending') }}">
            <div class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('inbound.putaway-tasks.pending') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Pending Tasks List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($putawayTasks as $task)
                        <tr class="hover:bg-gray-50 transition {{ $task->priority === 'high' ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $task->priority_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900">{{ $task->task_number }}</span>
                                <div class="text-xs text-gray-500">{{ $task->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $task->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $task->product->sku ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $task->warehouse->name }}</div>
                                <div class="text-xs text-gray-500">{{ $task->warehouse->code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <div class="text-gray-600">
                                        <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                                        {{ Str::limit($task->from_location, 15) }}
                                    </div>
                                    <div class="text-gray-400 text-xs">→</div>
                                    <div class="text-gray-900 font-semibold">
                                        <i class="fas fa-layer-group text-green-500 mr-1"></i>
                                        {{ $task->storageBin ? Str::limit($task->storageBin->bin_code, 15) : 'Not Set' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($task->quantity) }} {{ $task->unit_of_measure }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $task->status_badge !!}
                            </td>
                            <td class="px-6 py-4">
                                @if($task->assignedUser)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                            <i class="fas fa-user text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $task->assignedUser->name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <button onclick="openAssignModal({{ $task->id }}, '{{ $task->task_number }}')" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                        <i class="fas fa-user-plus mr-1"></i>Assign
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('inbound.putaway-tasks.execute', $task) }}" class="text-green-600 hover:text-green-900" title="Execute">
                                        <i class="fas fa-play-circle"></i>
                                    </a>
                                    <a href="{{ route('inbound.putaway-tasks.show', $task) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('inbound.putaway-tasks.edit', $task) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check-circle text-4xl text-green-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">All Caught Up!</h3>
                                    <p class="text-gray-600 mb-4">There are no pending putaway tasks at the moment</p>
                                    <a href="{{ route('inbound.putaway-tasks.index') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-list mr-2"></i>View All Tasks
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($putawayTasks->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $putawayTasks->links() }}
            </div>
        @endif
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
        <form id="assignForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Task Number</label>
                <input type="text" id="modalTaskNumber" readonly class="w-full rounded-lg border-gray-300 bg-gray-50">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign To <span class="text-red-500">*</span></label>
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
                    Assign Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignModal(taskId, taskNumber) {
    document.getElementById('assignForm').action = `/inbound/putaway-tasks/${taskId}/assign`;
    document.getElementById('modalTaskNumber').value = taskNumber;
    document.getElementById('assignModal').classList.remove('hidden');
}
</script>

@endsection