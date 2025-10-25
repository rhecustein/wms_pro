{{-- resources/views/roles/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid px-4 py-6 max-w-4xl mx-auto">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-edit text-blue-600 mr-2"></i>
                Edit Role
            </h1>
            <p class="text-sm text-gray-600 mt-1">Update role information and permissions</p>
        </div>
        <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                
                {{-- Role Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" placeholder="e.g., Administrator, Manager, Staff">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Current slug: <span class="font-mono font-semibold">{{ $role->slug }}</span></p>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror" placeholder="Brief description of this role and its responsibilities">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Permissions --}}
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Permissions</h3>
                            <p class="text-sm text-gray-600">Select permissions for this role</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="selectAllPermissions()" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" onclick="deselectAllPermissions()" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                <i class="fas fa-times mr-1"></i>Deselect All
                            </button>
                        </div>
                    </div>

                    @error('permissions')
                        <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                    @enderror

                    <div class="space-y-4">
                        @foreach($permissions as $module => $modulePermissions)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                                        <i class="fas fa-folder text-blue-600 mr-2"></i>
                                        {{ $module }}
                                    </h4>
                                    <button type="button" onclick="toggleModule('{{ strtolower($module) }}')" class="px-2 py-1 text-xs bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition">
                                        <i class="fas fa-check mr-1"></i>Toggle All
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($modulePermissions as $key => $label)
                                        <label class="flex items-center p-3 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $key }}" {{ in_array($key, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }} class="permission-checkbox {{ strtolower($module) }}-permission rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-3">
                                            <div class="flex-1">
                                                <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                                <p class="text-xs text-gray-500">{{ $key }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Meta Information --}}
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Meta Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Created:</span>
                            <span class="text-gray-900 font-medium">{{ $role->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="text-gray-900 font-medium">{{ $role->updated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="{{ route('roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Update Role
                </button>
            </div>

        </form>
    </div>

</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function toggleModule(module) {
    const checkboxes = document.querySelectorAll('.' + module + '-permission');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}
</script>

@endsection