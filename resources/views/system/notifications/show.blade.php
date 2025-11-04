@extends('layouts.app')
@section('title', 'Notification Detail')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-50">
    <div class="container-fluid px-4 py-8 max-w-[1400px] mx-auto">
        
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
                            <a href="{{ route('system.notifications.index') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                Notifications
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-900">Detail</span>
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
                        Notification Detail
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">View notification information</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('system.notifications.index') }}" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span class="hidden sm:inline">Back to List</span>
                    </a>
                    <form action="{{ route('system.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40 flex items-center gap-2 font-medium">
                            <i class="fas fa-trash"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Notification Content Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
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
                            
                            <div class="w-14 h-14 bg-gradient-to-br from-{{ $iconColor }}-500 to-{{ $iconColor }}-600 rounded-xl flex items-center justify-center shadow-lg shadow-{{ $iconColor }}-500/30">
                                <i class="fas {{ $iconClass }} text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h2>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $notification->created_at->format('l, d F Y - H:i') }}
                                </p>
                            </div>
                            @if(is_null($notification->read_at))
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    <i class="fas fa-circle text-red-600 mr-1 text-[8px]"></i>
                                    Unread
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Read
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Message Content --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-align-left text-blue-500 mr-2"></i>
                                Message
                            </label>
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                                <p class="text-gray-800 text-base leading-relaxed">
                                    {{ $notification->data['message'] ?? 'No message available' }}
                                </p>
                            </div>
                        </div>

                        {{-- Additional Data --}}
                        @if(count($notification->data) > 2)
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Additional Information
                                </label>
                                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                    <dl class="grid grid-cols-1 gap-4">
                                        @foreach($notification->data as $key => $value)
                                            @if(!in_array($key, ['title', 'message', 'url']))
                                                <div class="flex items-start">
                                                    <dt class="text-sm font-semibold text-gray-600 w-1/3">
                                                        {{ ucwords(str_replace('_', ' ', $key)) }}:
                                                    </dt>
                                                    <dd class="text-sm text-gray-900 w-2/3">
                                                        @if(is_array($value))
                                                            <pre class="bg-white p-3 rounded-lg border border-gray-200 text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                        @else
                                                            <span class="font-medium">{{ $value }}</span>
                                                        @endif
                                                    </dd>
                                                </div>
                                            @endif
                                        @endforeach
                                    </dl>
                                </div>
                            </div>
                        @endif

                        {{-- Action URL --}}
                        @if(isset($notification->data['url']))
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-3">
                                    <i class="fas fa-link text-blue-500 mr-2"></i>
                                    Quick Action
                                </label>
                                <a href="{{ $notification->data['url'] }}" class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 font-medium">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>View Related Item</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endif

                        {{-- Raw Data (for debugging, optional) --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button onclick="toggleRawData()" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-2 transition-colors">
                                <i class="fas fa-code"></i>
                                <span>Show Raw Data</span>
                                <i class="fas fa-chevron-down" id="rawDataIcon"></i>
                            </button>
                            <div id="rawDataSection" class="hidden mt-3">
                                <pre class="bg-gray-900 text-green-400 p-4 rounded-xl text-xs overflow-x-auto">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Related Notifications --}}
                @if($relatedNotifications->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-layer-group text-purple-600"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">Related Notifications</h2>
                                    <p class="text-xs text-gray-500">Similar notifications you might want to check</p>
                                </div>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach($relatedNotifications as $related)
                                <a href="{{ route('system.notifications.show', $related->id) }}" class="block p-4 hover:bg-blue-50/30 transition-all duration-200">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-bell text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <h4 class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $related->data['title'] ?? 'Notification' }}
                                                    @if(is_null($related->read_at))
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            New
                                                        </span>
                                                    @endif
                                                </h4>
                                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $related->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                {{ $related->data['message'] ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                            <a href="{{ route('system.notifications.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-2 transition-colors">
                                <span>View All Notifications</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Notification Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Information</h2>
                                <p class="text-xs text-gray-500">Notification details</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- ID --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hashtag text-gray-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">ID</p>
                                <p class="text-sm font-mono font-bold text-gray-900 truncate">{{ $notification->id }}</p>
                            </div>
                        </div>

                        {{-- Type --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-purple-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Type</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ str_replace(['App\\Notifications\\', 'Notification'], '', $notification->type) }}
                                </p>
                            </div>
                        </div>

                        {{-- Created At --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-plus text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Created At</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $notification->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $notification->created_at->format('H:i:s') }}</p>
                            </div>
                        </div>

                        {{-- Read At --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 {{ is_null($notification->read_at) ? 'bg-red-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ is_null($notification->read_at) ? 'fa-envelope' : 'fa-envelope-open' }} {{ is_null($notification->read_at) ? 'text-red-600' : 'text-blue-600' }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Read At</p>
                                @if(is_null($notification->read_at))
                                    <p class="text-sm font-semibold text-red-600">Not read yet</p>
                                @else
                                    <p class="text-sm font-semibold text-gray-900">{{ $notification->read_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $notification->read_at->format('H:i:s') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bolt text-orange-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Quick Actions</h2>
                                <p class="text-xs text-gray-500">Manage notification</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-3">
                        @if(is_null($notification->read_at))
                            <form action="{{ route('system.notifications.mark-as-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg shadow-blue-500/30 flex items-center justify-center gap-2 font-medium">
                                    <i class="fas fa-check"></i>
                                    Mark as Read
                                </button>
                            </form>
                        @endif

                        @if(isset($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg shadow-green-500/30 flex items-center justify-center gap-2 font-medium">
                                <i class="fas fa-external-link-alt"></i>
                                View Related
                            </a>
                        @endif

                        <form action="{{ route('system.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 flex items-center justify-center gap-2 font-medium">
                                <i class="fas fa-trash"></i>
                                Delete Notification
                            </button>
                        </form>

                        <a href="{{ route('system.notifications.index') }}" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                            <i class="fas fa-list"></i>
                            Back to List
                        </a>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-blue-100 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lightbulb text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Pro Tip</h3>
                                <p class="text-xs text-gray-600 mt-1">
                                    Keep your notifications organized by marking them as read after reviewing.
                                </p>
                            </div>
                        </div>
                        <div class="bg-white/50 rounded-lg p-3 mt-3">
                            <p class="text-xs text-gray-700">
                                <i class="fas fa-check-circle text-green-600 mr-1"></i>
                                You can filter notifications by type and status on the main page.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRawData() {
    const section = document.getElementById('rawDataSection');
    const icon = document.getElementById('rawDataIcon');
    
    section.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}
</script>
@endsection