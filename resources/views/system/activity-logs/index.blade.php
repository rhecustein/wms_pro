@extends('layouts.app')
@section('title', 'Activity Logs')
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
                            <span class="text-sm font-medium text-gray-900">Activity Logs</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-history text-white text-xl"></i>
                        </div>
                        Activity Logs
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">Track and monitor all system activities and changes</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="showCleanupModal()" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-red-500 hover:text-red-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-trash-alt"></i>
                        <span class="hidden sm:inline">Cleanup Logs</span>
                    </button>
                    <button onclick="exportData()" class="px-4 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-download"></i>
                        <span class="hidden sm:inline">Export</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Total Logs --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-list text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Logs</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">All activities recorded</p>
                        </div>
                        <div class="flex items-center text-blue-600 text-sm font-medium">
                            <i class="fas fa-database text-xs mr-1"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
            </div>

            {{-- Today's Activity --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Today</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['today'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Activities today</p>
                        </div>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up text-xs mr-1"></i>
                            <span>Live</span>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            </div>

            {{-- This Week --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-week text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">This Week</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['this_week'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Weekly activities</p>
                        </div>
                        <div class="flex items-center text-green-600 text-sm font-medium">
                            <i class="fas fa-chart-line text-xs mr-1"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-green-500 to-emerald-600"></div>
            </div>

            {{-- This Month --}}
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">This Month</span>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['this_month'] ?? 0) }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Monthly activities</p>
                        </div>
                        <div class="flex items-center text-orange-600 text-sm font-medium">
                            <i class="fas fa-chart-bar text-xs mr-1"></i>
                        </div>
                    </div>
                </div>
                <div class="h-1 bg-gradient-to-r from-orange-500 to-red-600"></div>
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
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-filter text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Filters</h2>
                            <p class="text-xs text-gray-500">Refine your search results</p>
                        </div>
                    </div>
                    <button onclick="toggleFilters()" class="lg:hidden px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chevron-down" id="filterIcon"></i>
                    </button>
                </div>
            </div>
            
            <div id="filterSection" class="p-6">
                <form method="GET" action="{{ route('system.activity-logs.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Search --}}
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>
                                Search
                            </label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Description, User..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        {{-- Log Name Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag text-gray-400 mr-1"></i>
                                Log Type
                            </label>
                            <select name="log_name" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                                <option value="">All Types</option>
                                @foreach($logNames as $logName)
                                    <option value="{{ $logName }}" {{ request('log_name') === $logName ? 'selected' : '' }}>
                                        {{ ucfirst($logName) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subject Type Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-cube text-gray-400 mr-1"></i>
                                Subject Type
                            </label>
                            <select name="subject_type" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                                <option value="">All Types</option>
                                @foreach($subjectTypes as $type)
                                    <option value="{{ $type['value'] }}" {{ request('subject_type') === $type['value'] ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- User Filter --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-user text-gray-400 mr-1"></i>
                                User
                            </label>
                            <select name="causer_id" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
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
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-gray-400 mr-1"></i>
                                Date To
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200">
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg shadow-indigo-500/30 flex items-center gap-2 font-medium">
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('system.activity-logs.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center gap-2 font-medium">
                            <i class="fas fa-redo"></i>
                            Reset
                        </a>
                        <div class="ml-auto text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing <span class="font-semibold text-gray-900">{{ $activityLogs->count() }}</span> of <span class="font-semibold text-gray-900">{{ $activityLogs->total() }}</span> results
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Activity Logs Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-gray-400"></i>
                                    Timestamp
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-user text-gray-400"></i>
                                    User
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-alt text-gray-400"></i>
                                    Description
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    Type
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-cube text-gray-400"></i>
                                    Subject
                                </div>
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <div class="flex items-center justify-end gap-2">
                                    <i class="fas fa-cog text-gray-400"></i>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($activityLogs as $log)
                            <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                                            <i class="fas fa-calendar-alt text-indigo-500 text-xs"></i>
                                            {{ $log->created_at->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center gap-2 mt-1">
                                            <i class="fas fa-clock text-gray-400"></i>
                                            {{ $log->created_at->format('H:i:s') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            @if($log->causer)
                                                <div class="text-sm font-bold text-gray-900 truncate">{{ $log->causer->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->causer->email ?? '' }}</div>
                                            @else
                                                <div class="text-sm text-gray-500 italic">System</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-md">
                                        <p class="text-sm text-gray-900 font-medium">{{ $log->description }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($log->log_name)
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700">
                                            <i class="fas fa-tag mr-1.5 text-xs"></i>
                                            {{ ucfirst($log->log_name) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No type</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->subject_type)
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-cube text-purple-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold text-gray-900">{{ class_basename($log->subject_type) }}</div>
                                                @if($log->subject_id)
                                                    <div class="text-xs text-gray-500">ID: {{ $log->subject_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No subject</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('system.activity-logs.show', $log) }}" class="w-9 h-9 bg-indigo-100 hover:bg-indigo-200 text-indigo-600 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" title="View Details">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        
                                        <form action="{{ route('system.activity-logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this log?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-all duration-200 hover:scale-110" title="Delete">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-history text-5xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Activity Logs Found</h3>
                                        <p class="text-gray-600 mb-6 text-center max-w-md">No activities have been recorded yet or try adjusting your filters</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($activityLogs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $activityLogs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Cleanup Modal --}}
<div id="cleanupModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Cleanup Old Logs</h3>
                    <p class="text-sm text-gray-500">Delete activity logs older than specified days</p>
                </div>
            </div>
        </div>
        
        <form action="{{ route('system.activity-logs.bulk-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                    Delete logs older than (days)
                </label>
                <input type="number" name="days" value="30" min="1" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:border-red-500 focus:ring-4 focus:ring-red-500/10 transition-all duration-200" required>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    This action cannot be undone
                </p>
            </div>
            
            <div class="p-6 bg-gray-50 rounded-b-2xl flex gap-3">
                <button type="button" onclick="hideCleanupModal()" class="flex-1 px-6 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 font-medium">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete Logs
                </button>
            </div>
        </form>
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

function showCleanupModal() {
    document.getElementById('cleanupModal').classList.remove('hidden');
}

function hideCleanupModal() {
    document.getElementById('cleanupModal').classList.add('hidden');
}

function exportData() {
    alert('Export functionality will be implemented');
}

// Auto-submit form on select change
document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        if (window.innerWidth >= 1024) {
            document.getElementById('filterForm').submit();
        }
    });
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideCleanupModal();
    }
});

// Close modal on outside click
document.getElementById('cleanupModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        hideCleanupModal();
    }
});
</script>
@endsection