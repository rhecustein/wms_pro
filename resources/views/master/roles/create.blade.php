@extends('layouts.app')

@section('title', 'Create New Role')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                Create New Role
            </h1>
            <p class="text-sm text-gray-600 mt-1">Add a new role with permissions</p>
        </div>
        <a href="{{ route('master.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Roles
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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

    {{-- Create Form --}}
    <form action="{{ route('master.roles.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column - Role Information --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Role Information
                    </h3>

                    {{-- Role Name --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Role Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                               placeholder="e.g., Admin, Manager" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Guard Name --}}
                    <div class="mb-4">
                        <label for="guard_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Guard Name <span class="text-red-500">*</span>
                        </label>
                        <select name="guard_name" id="guard_name" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('guard_name') border-red-500 @enderror" required>
                            <option value="">Select Guard</option>
                            <option value="web" {{ old('guard_name') === 'web' ? 'selected' : '' }}>Web</option>
                            <option value="api" {{ old('guard_name') === 'api' ? 'selected' : '' }}>API</option>
                        </select>
                        @error('guard_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-blue-600 mt-1 mr-2"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-semibold mb-1">Tip:</p>
                                <p>Choose 'web' for dashboard users and 'api' for API authentication.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                    <div class="flex flex-col space-y-3">
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-save mr-2"></i>Create Role
                        </button>
                        <a href="{{ route('master.roles.index') }}" class="w-full px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-center font-semibold">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Column - Permissions --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-key text-green-600 mr-2"></i>
                            Assign Permissions
                        </h3>
                        <div class="flex space-x-2">
                            <button type="button" onclick="selectAll()" class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                <i class="fas fa-times-circle mr-1"></i>Deselect All
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @forelse($permissions as $group => $groupPermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-800 capitalize">
                                        <i class="fas fa-folder text-yellow-600 mr-2"></i>
                                        {{ str_replace('_', ' ', $group) }}
                                    </h4>
                                    <span class="text-xs text-gray-500">{{ $groupPermissions->count() }} permissions</span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-key text-4xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Permissions Available</h3>
                                <p class="text-gray-600">Please create permissions first before assigning to roles.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
function selectAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection