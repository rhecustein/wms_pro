{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid px-4 py-6 max-w-6xl mx-auto">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user text-blue-600 mr-2"></i>
                User Details
            </h1>
            <p class="text-sm text-gray-600 mt-1">View complete user information</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('master.users.edit', $user) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit User
            </a>
            <a href="{{ route('master.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Profile Header --}}
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-32"></div>
                
                {{-- Profile Content --}}
                <div class="px-6 pb-6 -mt-16">
                    <div class="flex flex-col items-center">
                        {{-- Avatar --}}
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-lg">
                        
                        {{-- Name & Status --}}
                        <h2 class="text-xl font-bold text-gray-800 mt-4">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-600 mb-3">ID: #{{ $user->id }}</p>
                        
                        <div class="mb-4">
                            {!! $user->status_badge !!}
                        </div>

                        {{-- Quick Actions --}}
                        <div class="flex items-center space-x-2 w-full">
                            <a href="mailto:{{ $user->email }}" class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition text-sm">
                                <i class="fas fa-envelope mr-1"></i>Email
                            </a>
                            @if($user->phone)
                            <a href="tel:{{ $user->phone }}" class="flex-1 px-4 py-2 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition text-sm">
                                <i class="fas fa-phone mr-1"></i>Call
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div class="mt-6 space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-envelope text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Email</p>
                                <p class="text-sm text-gray-900 font-medium break-all">{{ $user->email }}</p>
                            </div>
                        </div>

                        @if($user->phone)
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-phone text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500">Phone</p>
                                <p class="text-sm text-gray-900 font-medium">{{ $user->phone }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Section --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Account Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Account Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Email Status</p>
                        @if($user->email_verified_at)
                            <p class="text-sm font-semibold text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Verified
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->email_verified_at->format('d M Y H:i') }}</p>
                        @else
                            <p class="text-sm font-semibold text-yellow-600">
                                <i class="fas fa-clock mr-1"></i>Not Verified
                            </p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Last Login</p>
                        @if($user->last_login_at)
                            <p class="text-sm font-semibold text-gray-900">{{ $user->last_login_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->last_login_at->format('H:i') }}</p>
                        @else
                            <p class="text-sm font-semibold text-gray-400">Never logged in</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Account Status</p>
                        @if($user->is_active)
                            <p class="text-sm font-semibold text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Can access system</p>
                        @else
                            <p class="text-sm font-semibold text-red-600">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Cannot access system</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500 mb-1">Member Since</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
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
                            <i class="fas fa-user-plus text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">Account Created</h4>
                                <span class="text-xs text-gray-500">{{ $user->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                User account was created
                                @if($user->creator)
                                    by <span class="font-medium">{{ $user->creator->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Last Update --}}
                    @if($user->updated_at != $user->created_at)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-edit text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">Profile Updated</h4>
                                <span class="text-xs text-gray-500">{{ $user->updated_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                User information was updated
                                @if($user->updater)
                                    by <span class="font-medium">{{ $user->updater->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif

                    {{-- Last Login --}}
                    @if($user->last_login_at)
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-sign-in-alt text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-900">Last Login</h4>
                                <span class="text-xs text-gray-500">{{ $user->last_login_at->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                                User logged in to the system
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- System Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-cog text-blue-600 mr-2"></i>
                    System Information
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">User ID:</span>
                        <span class="text-gray-900 font-medium ml-2">{{ $user->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Created At:</span>
                        <span class="text-gray-900 font-medium ml-2">{{ $user->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Updated At:</span>
                        <span class="text-gray-900 font-medium ml-2">{{ $user->updated_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($user->creator)
                    <div>
                        <span class="text-gray-600">Created By:</span>
                        <span class="text-gray-900 font-medium ml-2">{{ $user->creator->name }}</span>
                    </div>
                    @endif
                    @if($user->updater)
                    <div>
                        <span class="text-gray-600">Updated By:</span>
                        <span class="text-gray-900 font-medium ml-2">{{ $user->updater->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection