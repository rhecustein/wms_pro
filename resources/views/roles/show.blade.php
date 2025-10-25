{{-- resources/views/roles/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid px-4 py-6 max-w-6xl mx-auto">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                Role Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete role information and permissions</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('roles.edit', $role) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit Role
            </a>
            <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Role Info Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-4xl"></i>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-center">{{ $role->name }}</h2>
                    <p class="text-sm text-center text-blue-100 mt-1">{{ $role->slug }}</p>
                </div>
                
                {{-- Stats --}}
                <div class="p-6 space-y-4">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-purple-600 mb-1">Permissions</p>
                                <p class="text-2xl font-bold text-purple-700">{{ $role->permissions_count }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-key text-xl text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-blue-600 mb-1">Users</p>
                                <p class="text-2xl font-bold text-blue-700">{{ $role->users_count }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-xl text-blue-600"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($role->description)
                    <div class="border-t border-gray-200 pt-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-sm text-gray-600">{{ $role->description }}</p>
                    </div>
                    @endif

                    {{-- Meta --}}
                    <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="text-gray-900 font-medium">{{ $role->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Updated:</span>
                            <span class="text-gray-900 font-medium">{{ $role->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Section --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Permissions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-key text-blue-600 mr-2"></i>
                    Permissions ({{ $role->permissions_count }})
                </h3>

                @if($role->permissions && count($role->permissions) > 0)
                    <div class="space-y-4">
                        @foreach($permissions as $module => $modulePermissions)
                            @php
                                $modulePerms = array_intersect($role->permissions, array_keys($modulePermissions));
                            @endphp
                            @if(count($modulePerms) > 0)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-folder text-blue-600 mr-2"></i>
                                        {{ $module }}
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">
                                            {{ count($modulePerms) }}
                                        </span>
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($modulePerms as $perm)
                                            <div class="flex items-center p-2 bg-white border border-gray-200 rounded-lg">
                                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-900">{{ $modulePermissions[$perm] }}</span>
                                                    <p class="text-xs text-gray-500">{{ $perm }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-key text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600">No permissions assigned to this role</p>
                    </div>
                @endif
            </div>

            {{-- Assigned Users --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-users text-blue-600 mr-2"></i>
                    Assigned Users ({{ $role->users_count }})
                </h3>

                @if($role->users && $role->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($role->users as $user)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    {!! $user->status_badge !!}
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="View User">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-users text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-600">No users assigned to this role</p>
                    </div>
                @endif
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Activity Timeline
                </h3>
                <div class="space-y-4">
                    {{-- Created --}}
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-plus text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">Role Created</h4>
                                <span class="text-xs text-gray-500">{{ $role->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Role was created in the system
                            </p>
                        </div>
                    </div>

                    {{-- Last Update --}}
                    @if($role->updated_at != $role->created_at)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-edit text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">Role Updated</h4>
                                <span class="text-xs text-gray-500">{{ $role->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                Role information or permissions were updated
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection