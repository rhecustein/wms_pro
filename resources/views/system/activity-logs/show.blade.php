@extends('layouts.app')
@section('title', 'Activity Log Details')
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
                            <a href="{{ route('system.activity-logs.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600">Activity Logs</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2 text-xs"></i>
                            <span class="text-sm font-medium text-gray-900">Details</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-indigo-500/30">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        Activity Log Details
                    </h1>
                    <p class="text-sm text-gray-600 mt-2 ml-16">View detailed information about this activity</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('system.activity-logs.index') }}" class="px-6 py-2.5 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-indigo-500 hover:text-indigo-600 transition-all duration-200 shadow-sm hover:shadow-md flex items-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to List</span>
                    </a>
                    <form action="{{ route('system.activity-logs.destroy', $activityLog) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this log?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg shadow-red-500/30 flex items-center gap-2 font-medium">
                            <i class="fas fa-trash"></i>
                            <span>Delete Log</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Information --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-info-circle text-indigo-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Basic Information</h2>
                                <p class="text-xs text-gray-500">Core activity details</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        {{-- Description --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-alt text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Description</label>
                                <p class="text-base font-medium text-gray-900">{{ $activityLog->description }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        {{-- Log Name/Type --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Log Type</label>
                                @if($activityLog->log_name)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-700">
                                        <i class="fas fa-tag mr-1.5"></i>
                                        {{ ucfirst($activityLog->log_name) }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400 italic">No type specified</span>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        {{-- Timestamp --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Timestamp</label>
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-sm text-gray-900">
                                        <i class="fas fa-calendar-alt text-green-500 text-xs"></i>
                                        <span class="font-semibold">{{ $activityLog->created_at->format('l, d F Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fas fa-clock text-green-500 text-xs"></i>
                                        <span>{{ $activityLog->created_at->format('H:i:s') }}</span>
                                        <span class="text-xs text-gray-400">({{ $activityLog->created_at->diffForHumans() }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        {{-- Log ID --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-hashtag text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Log ID</label>
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ $activityLog->id }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subject Information Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cube text-teal-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Subject Information</h2>
                                <p class="text-xs text-gray-500">Entity affected by this activity</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($activityLog->subject_type)
                            {{-- Subject Type --}}
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-cube text-teal-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Subject Type</label>
                                    <p class="text-sm font-semibold text-gray-900">{{ class_basename($activityLog->subject_type) }}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-1">{{ $activityLog->subject_type }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-100"></div>

                            {{-- Subject ID --}}
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-fingerprint text-teal-600"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Subject ID</label>
                                    <p class="text-sm font-mono font-semibold text-gray-900">{{ $activityLog->subject_id }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center py-8">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                    <i class="fas fa-cube text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-500 italic">No subject information available</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Properties/Data Card --}}
                @if($activityLog->properties && count($activityLog->properties) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-database text-orange-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Properties & Data</h2>
                                <p class="text-xs text-gray-500">Additional information and changes</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <pre class="text-xs text-gray-800 font-mono overflow-x-auto whitespace-pre-wrap">{{ json_encode($activityLog->properties, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Causer Information Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">User Information</h2>
                                <p class="text-xs text-gray-500">Who performed this action</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($activityLog->causer)
                            <div class="flex flex-col items-center text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                                    <i class="fas fa-user text-white text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $activityLog->causer->name ?? 'Unknown User' }}</h3>
                                @if($activityLog->causer->email)
                                    <p class="text-sm text-gray-600 mb-3">{{ $activityLog->causer->email }}</p>
                                @endif
                                <div class="w-full space-y-2 mt-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="text-xs font-semibold text-gray-500 uppercase">User ID</span>
                                        <span class="text-sm font-mono font-semibold text-gray-900">{{ $activityLog->causer_id }}</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="text-xs font-semibold text-gray-500 uppercase">User Type</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ class_basename($activityLog->causer_type) }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-center py-4">
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                                    <i class="fas fa-robot text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">System Action</h3>
                                <p class="text-sm text-gray-500">Performed automatically</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Quick Stats Card --}}
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold">Quick Stats</h2>
                                <p class="text-xs text-indigo-100">Activity metrics</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-indigo-100">Total Logs</span>
                                    <i class="fas fa-list text-indigo-200"></i>
                                </div>
                                <p class="text-2xl font-bold">{{ number_format(\Spatie\Activitylog\Models\Activity::count()) }}</p>
                            </div>
                            
                            @if($activityLog->causer)
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-indigo-100">User's Activities</span>
                                    <i class="fas fa-user-clock text-indigo-200"></i>
                                </div>
                                <p class="text-2xl font-bold">
                                    {{ number_format(\Spatie\Activitylog\Models\Activity::where('causer_id', $activityLog->causer_id)->where('causer_type', $activityLog->causer_type)->count()) }}
                                </p>
                            </div>
                            @endif
                            
                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-indigo-100">Today's Logs</span>
                                    <i class="fas fa-calendar-day text-indigo-200"></i>
                                </div>
                                <p class="text-2xl font-bold">
                                    {{ number_format(\Spatie\Activitylog\Models\Activity::whereDate('created_at', today())->count()) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cog text-gray-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Actions</h2>
                                <p class="text-xs text-gray-500">Available operations</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-2">
                        <a href="{{ route('system.activity-logs.index') }}" class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-list text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-sm font-medium">View All Logs</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </a>
                        
                        @if($activityLog->causer)
                        <a href="{{ route('system.activity-logs.index', ['causer_id' => $activityLog->causer_id]) }}" class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-user text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-sm font-medium">User's Activity</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </a>
                        @endif
                        
                        @if($activityLog->log_name)
                        <a href="{{ route('system.activity-logs.index', ['log_name' => $activityLog->log_name]) }}" class="w-full flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors group">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-tag text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-sm font-medium">Similar Type</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection