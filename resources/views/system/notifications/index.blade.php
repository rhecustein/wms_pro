@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="container-fluid px-4 py-8 max-w-[1600px] mx-auto">
        
        {{-- Page Header with Breadcrumb --}}
        <div class="mb-8">
            {{-- Breadcrumb --}}
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-500">System</span>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-900">Notifications</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-blue-500/30">
                            <i class="fas fa-bell text-white text-xl"></i>
                        </div>
                        Notifications
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">Stay updated with your system activities</p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('system.notifications.delete-all-read') }}" method="POST" onsubmit="return confirm('Delete all read notifications?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-red-500 hover:text-red-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                            <i class="fas fa-trash"></i>
                            <span class="hidden sm:inline">Delete Read</span>
                        </button>
                    </form>
                    <form action="{{ route('system.notifications.mark-all-as-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 flex items-center gap-2 font-medium">
                            <i class="fas fa-check-double"></i>
                            <span>Mark All Read</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Notifications --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-bell text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">All notifications</p>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>

            {{-- Unread --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unread</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['unread'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Need attention</p>
                        </div>
                        <div class="flex items-center text-red-600 text-sm font-medium">
                            <i class="fas fa-exclamation-circle text-xs mr-1"></i>
                            <span>New</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
            </div>

            {{-- Read --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-envelope-open text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Read</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['read'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Already viewed</p>
                        </div>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <i class="fas fa-check text-xs mr-1"></i>
                            <span>Done</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
            </div>

            {{-- Today --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Today</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['today'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Today's activity</p>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-purple-500 to-purple-600"></div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl shadow-sm animate-slideInRight">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-green-900">Success!</p>
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button onclick="this.closest('.animate-slideInRight').remove()" class="text-green-600 hover:text-green-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl shadow-sm animate-slideInRight">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-circle text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-red-900">Error!</p>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button onclick="this.closest('.animate-slideInRight').remove()" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Filters Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-filter text-blue-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                            <p class="text-xs text-gray-500">Refine your notifications</p>
                        </div>
                    </div>
                    <button onclick="toggleFilters()" class="lg:hidden px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chevron-down" id="filterIcon"></i>
                    </button>
                </div>
            </div>
            
            <div id="filterSection" class="p-6">
                <form method="GET" action="{{ route('system.notifications.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>
                                Search
                            </label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notifications..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        {{-- Status Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-flag text-gray-400 mr-1"></i>
                                Status
                            </label>
                            <select name="status" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Status</option>
                                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                            </select>
                        </div>

                        {{-- Type Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                Type
                            </label>
                            <select name="type" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type['value'] }}" {{ request('type') === $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                                Date From
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                                Date To
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-200">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 flex items-center gap-2 font-medium">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('system.notifications.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center gap-2 font-medium">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                        <div class="ml-auto text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing <span class="font-semibold text-gray-900">{{ $notifications->count() }}</span> of <span class="font-semibold text-gray-900">{{ $notifications->total() }}</span> results
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($notifications as $notification)
                    <div class="p-6 hover:bg-blue-50/30 transition-all duration-200 {{ is_null($notification->read_at) ? 'bg-blue-50/20' : '' }}">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                @php
                                    $iconClass = 'fa-bell';
                                    $iconColor = 'blue';
                                    
                                    if (str_contains($notification->type, 'Order')) {
                                        $iconClass = 'fa-shopping-cart';
                                        $iconColor = 'green';
                                    } elseif (str_contains($notification->type, 'Payment')) {
                                        $iconClass = 'fa-dollar-sign';
                                        $iconColor = 'yellow';
                                    } elseif (str_contains($notification->type, 'User')) {
                                        $iconClass = 'fa-user';
                                        $iconColor = 'purple';
                                    } elseif (str_contains($notification->type, 'Stock')) {
                                        $iconClass = 'fa-box';
                                        $iconColor = 'red';
                                    }
                                @endphp
                                
                                <div class="w-12 h-12 bg-gradient-to-br from-{{ $iconColor }}-500 to-{{ $iconColor }}-600 rounded-xl flex items-center justify-center shadow-lg shadow-{{ $iconColor }}-500/30">
                                    <i class="fas {{ $iconClass }} text-white"></i>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-gray-900 mb-1">
                                            {{ $notification->data['title'] ?? 'Notification' }}
                                            @if(is_null($notification->read_at))
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    New
                                                </span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ $notification->data['message'] ?? 'No message available' }}
                                        </p>
                                    </div>
                                    
                                    {{-- Time --}}
                                    <div class="flex-shrink-0 text-right">
                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $notification->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex items-center gap-2 mt-3">
                                    @if(is_null($notification->read_at))
                                        <form action="{{ route('system.notifications.mark-as-read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg text-xs font-medium transition-all duration-200 flex items-center gap-1">
                                                <i class="fas fa-check text-xs"></i>
                                                Mark as Read
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('system.notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg text-xs font-medium transition-all duration-200 flex items-center gap-1">
                                            <i class="fas fa-trash text-xs"></i>
                                            Delete
                                        </button>
                                    </form>

                                    @if(isset($notification->data['url']))
                                        <a href="{{ $notification->data['url'] }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-medium transition-all duration-200 flex items-center gap-1">
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                            View Details
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-20">
                        <div class="flex flex-col items-center">
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-bell-slash text-5xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Notifications</h3>
                            <p class="text-gray-600 mb-6 text-center max-w-md">You're all caught up! There are no notifications to display at the moment.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slideInRight {
    animation: slideInRight 0.5s ease-out;
}
</style>

<script>
function toggleFilters() {
    const section = document.getElementById('filterSection');
    const icon = document.getElementById('filterIcon');
    
    section.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

// Auto-submit form on select change
document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        if (window.innerWidth >= 1024) {
            document.getElementById('filterForm').submit();
        }
    });
});

// Auto refresh unread count every 30 seconds
setInterval(function() {
    fetch('{{ route("system.notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            // Update badge in navbar if exists
            const badge = document.getElementById('notification-badge');
            if (badge && data.count > 0) {
                badge.textContent = data.count;
                badge.classList.remove('hidden');
            } else if (badge) {
                badge.classList.add('hidden');
            }
        });
}, 30000);
</script>
@endsection