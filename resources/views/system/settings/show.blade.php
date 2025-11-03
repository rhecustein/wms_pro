@extends('layouts.app')

@section('title', ($groupLabels[$group] ?? ucfirst($group)) . ' Settings')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('system.settings.index') }}" class="mr-4 text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas {{ $groupIcons[$group] ?? 'fa-cog' }} text-blue-600 mr-2"></i>
                    {{ $groupLabels[$group] ?? ucfirst($group) }}
                </h1>
                <p class="text-sm text-gray-600 mt-1">Configure {{ strtolower($groupLabels[$group] ?? $group) }}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <button onclick="resetGroup()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition duration-200">
                <i class="fas fa-undo mr-2"></i>Reset to Default
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between animate-fade-in">
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
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 animate-fade-in">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Validation Errors:</strong>
            </div>
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Settings Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('system.settings.update-group', $group) }}" 
              method="POST" 
              enctype="multipart/form-data"
              id="settingsForm">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                @forelse($settings as $setting)
                    <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-800 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', str_replace($group . '_', '', $setting->key))) }}
                                </label>
                                @if($setting->description)
                                    <p class="text-xs text-gray-500 mb-3">{{ $setting->description }}</p>
                                @endif
                            </div>
                            @if(!$setting->is_editable)
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-600 ml-3">
                                    <i class="fas fa-lock mr-1"></i>Read Only
                                </span>
                            @endif
                        </div>

                        @if($setting->is_editable)
                            {{-- Text Input --}}
                            @if($setting->type === 'string')
                                <input 
                                    type="text" 
                                    name="{{ $setting->key }}" 
                                    id="{{ $setting->key }}"
                                    value="{{ old($setting->key, $setting->value) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Enter {{ strtolower(str_replace('_', ' ', $setting->key)) }}"
                                >
                            @endif

                            {{-- Email Input --}}
                            @if($setting->type === 'email')
                                <input 
                                    type="email" 
                                    name="{{ $setting->key }}" 
                                    id="{{ $setting->key }}"
                                    value="{{ old($setting->key, $setting->value) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="email@example.com"
                                >
                            @endif

                            {{-- URL Input --}}
                            @if($setting->type === 'url')
                                <input 
                                    type="url" 
                                    name="{{ $setting->key }}" 
                                    id="{{ $setting->key }}"
                                    value="{{ old($setting->key, $setting->value) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="https://example.com"
                                >
                            @endif

                            {{-- Integer Input --}}
                            @if($setting->type === 'integer')
                                <input 
                                    type="number" 
                                    name="{{ $setting->key }}" 
                                    id="{{ $setting->key }}"
                                    value="{{ old($setting->key, $setting->value) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Enter number"
                                >
                            @endif

                            {{-- Textarea Input --}}
                            @if($setting->type === 'text')
                                <textarea 
                                    name="{{ $setting->key }}" 
                                    id="{{ $setting->key }}"
                                    rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    placeholder="Enter {{ strtolower(str_replace('_', ' ', $setting->key)) }}"
                                >{{ old($setting->key, $setting->value) }}</textarea>
                            @endif

                            {{-- Boolean Toggle --}}
                            @if($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="{{ $setting->key }}" 
                                        id="{{ $setting->key }}"
                                        value="true"
                                        {{ old($setting->key, $setting->value) === 'true' ? 'checked' : '' }}
                                        class="sr-only peer"
                                    >
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700" id="{{ $setting->key }}_label">
                                        {{ old($setting->key, $setting->value) === 'true' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            @endif

                            {{-- Color Picker --}}
                            @if($setting->type === 'color')
                                <div class="flex items-center space-x-3">
                                    <input 
                                        type="color" 
                                        name="{{ $setting->key }}" 
                                        id="{{ $setting->key }}"
                                        value="{{ old($setting->key, $setting->value ?? '#3b82f6') }}"
                                        class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                    >
                                    <input 
                                        type="text" 
                                        value="{{ old($setting->key, $setting->value ?? '#3b82f6') }}"
                                        readonly
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                        id="{{ $setting->key }}_display"
                                    >
                                </div>
                            @endif

                            {{-- File/Image Upload --}}
                            @if(in_array($setting->type, ['file', 'image']))
                                <div class="space-y-3" id="file-container-{{ $setting->key }}">
                                    {{-- Show existing file if exists --}}
                                    @if($setting->value && Storage::disk('public')->exists($setting->value))
                                        <div class="flex items-start space-x-4 p-3 bg-gray-50 rounded-lg border border-gray-200" id="existing-file-{{ $setting->key }}">
                                            @if($setting->type === 'image')
                                                <img src="{{ Storage::url($setting->value) }}" alt="{{ $setting->key }}" class="h-20 w-auto rounded border border-gray-300 shadow-sm">
                                            @else
                                                <div class="flex items-center">
                                                    <i class="fas fa-file text-gray-400 text-2xl mr-3"></i>
                                                    <span class="text-sm text-gray-600">{{ basename($setting->value) }}</span>
                                                </div>
                                            @endif
                                            <button type="button" onclick="deleteFile('{{ $setting->key }}')" class="text-red-600 hover:text-red-800 text-sm font-medium transition">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex items-center text-sm text-gray-500 mb-2 p-2 bg-gray-50 rounded" id="no-file-{{ $setting->key }}">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <span>No file uploaded yet</span>
                                        </div>
                                    @endif
                                    
                                    {{-- File input wrapper - ALWAYS SHOW --}}
                                    <div id="file-input-wrapper-{{ $setting->key }}">
                                        <label for="{{ $setting->key }}_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ $setting->value ? 'Replace file' : 'Upload file' }}
                                        </label>
                                        <input 
                                            type="file" 
                                            name="{{ $setting->key }}_file" 
                                            id="{{ $setting->key }}_file"
                                            accept="{{ $setting->type === 'image' ? 'image/*' : '*' }}"
                                            data-setting-key="{{ $setting->key }}"
                                            class="block w-full text-sm text-gray-500 
                                                file:mr-4 file:py-2 file:px-4 
                                                file:rounded-lg file:border-0 
                                                file:text-sm file:font-semibold 
                                                file:bg-blue-50 file:text-blue-700 
                                                hover:file:bg-blue-100
                                                cursor-pointer border border-gray-300 rounded-lg
                                                focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                        >
                                    </div>
                                    
                                    {{-- Upload status message container --}}
                                    <div id="upload-status-{{ $setting->key }}"></div>
                                    
                                    @if($setting->type === 'image')
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-image mr-1"></i>
                                            Accepted: JPG, PNG, GIF, SVG (Max: 2MB)
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-file mr-1"></i>
                                            Max file size: 5MB
                                        </p>
                                    @endif
                                </div>
                            @endif
                        @else
                            {{-- Read Only Display --}}
                            <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                                @if($setting->type === 'boolean')
                                    <span class="px-2 py-1 text-xs font-semibold rounded {{ $setting->actual_value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $setting->actual_value ? 'Enabled' : 'Disabled' }}
                                    </span>
                                @elseif(in_array($setting->type, ['file', 'image']))
                                    @if($setting->value && Storage::disk('public')->exists($setting->value))
                                        @if($setting->type === 'image')
                                            <img src="{{ Storage::url($setting->value) }}" alt="{{ $setting->key }}" class="h-20 w-auto rounded">
                                        @else
                                            <span class="text-sm text-gray-600">{{ basename($setting->value) }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500 italic">No file uploaded</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-600">{{ $setting->value ?? '—' }}</span>
                                @endif
                            </div>
                        @endif

                        @if($setting->updated_by)
                            <p class="text-xs text-gray-400 mt-2">
                                <i class="fas fa-clock mr-1"></i>
                                Last updated by {{ $setting->updatedByUser->name ?? 'System' }} on {{ $setting->updated_at->format('d M Y H:i') }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                        <p class="text-gray-600">No settings found in this group</p>
                    </div>
                @endforelse
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between rounded-b-xl">
                <a href="{{ route('system.settings.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div class="flex space-x-2">
                    <button type="reset" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }

    /* Custom file input styling */
    input[type="file"] {
        cursor: pointer;
    }
    
    input[type="file"]::file-selector-button {
        margin-right: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: none;
        font-size: 0.875rem;
        font-weight: 600;
        background-color: #dbeafe;
        color: #1d4ed8;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    input[type="file"]::file-selector-button:hover {
        background-color: #bfdbfe;
    }
</style>
@endpush

@push('scripts')
<script>
    // Color picker sync
    document.querySelectorAll('input[type="color"]').forEach(input => {
        const display = document.getElementById(input.id + '_display');
        if (display) {
            input.addEventListener('input', (e) => {
                display.value = e.target.value;
            });
        }
    });

    // Boolean toggle text update
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = document.getElementById(this.id + '_label');
            if (label) {
                label.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    });

    // AJAX File Upload - FIXED VERSION
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const settingKey = this.dataset.settingKey;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('setting_key', settingKey);

            // Get status container
            const statusContainer = document.getElementById(`upload-status-${settingKey}`);
            
            // Clear previous status
            statusContainer.innerHTML = '';
            
            // Show loading in status container
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'mt-2 text-blue-600 text-sm';
            loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
            statusContainer.appendChild(loadingDiv);

            try {
                const response = await fetch('{{ route("system.settings.upload-file") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    loadingDiv.className = 'mt-2 text-green-600 text-sm';
                    loadingDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Uploaded successfully! Reloading...';
                    
                    // Reset file input value to allow re-upload
                    this.value = '';
                    
                    // Reload page after 1 second
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.error || 'Upload failed');
                }

            } catch (error) {
                console.error('Upload error:', error);
                loadingDiv.className = 'mt-2 text-red-600 text-sm';
                loadingDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + error.message;
                
                // Reset file input
                this.value = '';
                
                // Remove error message after 5 seconds
                setTimeout(() => {
                    statusContainer.innerHTML = '';
                }, 5000);
            }
        });
    });

    // Delete file function - FIXED VERSION
    async function deleteFile(key) {
        if (!confirm('Are you sure you want to delete this file?')) return;
        
        // Show loading in status container
        const statusContainer = document.getElementById(`upload-status-${key}`);
        statusContainer.innerHTML = '<div class="mt-2 text-blue-600 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Deleting...</div>';
        
        try {
            const response = await fetch(`/system/settings/files/${key}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                statusContainer.innerHTML = '<div class="mt-2 text-green-600 text-sm"><i class="fas fa-check-circle mr-2"></i>Deleted successfully! Reloading...</div>';
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                const data = await response.json();
                throw new Error(data.error || 'Failed to delete file');
            }
        } catch (error) {
            console.error('Delete error:', error);
            statusContainer.innerHTML = `<div class="mt-2 text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>${error.message}</div>`;
            
            // Remove error message after 5 seconds
            setTimeout(() => {
                statusContainer.innerHTML = '';
            }, 5000);
        }
    }

    // Reset group function
    function resetGroup() {
        if (!confirm('Are you sure you want to reset all settings in this group to default values?')) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("system.settings.reset", $group) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }

    // Auto-hide success messages after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.animate-fade-in');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush