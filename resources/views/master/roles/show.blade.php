@extends('layouts.app')

@section('title', 'Role Details')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-shield text-blue-600 mr-2"></i>
                Role Details: {{ $role->name }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">Complete information about this role</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('master.roles.edit', $role) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit Role
            </a>
            <a href="{{ route('master.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
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
        
        {{-- Left Column - Role Information --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Basic Information
                </h3>

                <div class="space-y-4">
                    {{-- Role ID --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Role ID</label>
                        <p class="text-gray-800 font-semibold">#{{ $role->id }}</p>
                    </div>

                    {{-- Role Name --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Role Name</label>
                        <p class="text-gray-800 font-semibold text-lg">{{ $role->name }}</p>
                    </div>

                    {{-- Guard Name --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Guard Name</label>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded {{ $role->guard_name === 'web' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                            <i class="fas {{ $role->guard_name === 'web' ? 'fa-globe' : 'fa-code' }} mr-2"></i>
                            {{ $role->guard_name }}
                        </span>
                    </div>

                    {{-- Created At --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Created At</label>
                        <p class="text-gray-800">{{ $role->created_at->format('d F Y, H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $role->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Updated At --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Last Updated</label>
                        <p class="text-gray-800">{{ $role->updated_at->format('d F Y, H:i') }}</p>
                        <p class="text-xs text-gray-500">{{ $role->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl shadow-sm border border-blue-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                    Statistics
                </h3>

                <div class="space-y-4">
                    {{-- Total Permissions --}}
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-key text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total Permissions</p>
                                <p class="text-lg font-bold text-gray-800">{{ $role->permissions->count() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Assigned Users --}}
                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Assigned Users</p>
                                <p class="text-lg font-bold text-gray-800">{{ $role->users->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    Quick Actions
                </h3>

                <div class="flex flex-col space-y-2">
                    <a href="{{ route('master.roles.edit', $role) }}" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition text-center">
                        <i class="fas fa-edit mr-2"></i>Edit Role
                    </a>
                    
                    <form action="{{ route('master.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition {{ $role->users->count() > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $role->users->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash mr-2"></i>Delete Role
                        </button>
                    </form>
                    
                    @if($role->users->count() > 0)
                        <p class="text-xs text-red-600 text-center mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Cannot delete role with assigned users
                        </p>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right Column - Permissions & Users --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Permissions Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-key text-green-600 mr-2"></i>
                        Assigned Permissions ({{ $role->permissions->count() }})
                    </h3>
                    <a href="{{ route('master.roles.edit', $role) }}" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        <i class="fas fa-edit mr-1"></i>Manage
                    </a>
                </div>

                @if($permissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-800 capitalize">
                                        <i class="fas fa-folder text-yellow-600 mr-2"></i>
                                        {{ str_replace('_', ' ', $group) }}
                                    </h4>
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded">
                                        {{ $groupPermissions->count() }} permissions
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center p-2 bg-green-50 rounded-lg border border-green-200">
                                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-key text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Permissions Assigned</h3>
                        <p class="text-gray-600 mb-4">This role doesn't have any permissions yet.</p>
                        <a href="{{ route('master.roles.edit', $role) }}" class="inline-flex px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-2"></i>Assign Permissions
                        </a>
                    </div>
                @endif
            </div>

            {{-- Users Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-users text-blue-600 mr-2"></i>
                        Assigned Users ({{ $role->users->count() }})
                    </h3>
                </div>

                @if($role->users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($role->users as $user)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-blue-600 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-500">ID: #{{ $user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm text-gray-700">{{ $user->email }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-900">{{ $user->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-4xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Users Assigned</h3>
                        <p class="text-gray-600">This role hasn't been assigned to any users yet.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection