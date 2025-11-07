@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-cogs text-blue-600 mr-2"></i>
                System Settings
            </h1>
            <p class="text-sm text-gray-600 mt-1">Configure your system preferences and options</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="openImportModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <a href="{{ route('system.settings.export') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-file-export mr-2"></i>Export
            </a>
            <button onclick="clearCache()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200">
                <i class="fas fa-broom mr-2"></i>Clear Cache
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

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between animate-fade-in">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-blue-700 hover:text-blue-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Settings Groups Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($settings as $group => $groupSettings)
            @php
                $label = $groupLabels[$group] ?? ucfirst($group);
                $icon = $groupIcons[$group] ?? 'fa-cog';
                $colors = [
                    'identity' => ['bg' => 'bg-blue-100', 'hover' => 'group-hover:bg-blue-200', 'text' => 'text-blue-600', 'hover-text' => 'group-hover:text-blue-600'],
                    'company' => ['bg' => 'bg-indigo-100', 'hover' => 'group-hover:bg-indigo-200', 'text' => 'text-indigo-600', 'hover-text' => 'group-hover:text-indigo-600'],
                    'appearance' => ['bg' => 'bg-purple-100', 'hover' => 'group-hover:bg-purple-200', 'text' => 'text-purple-600', 'hover-text' => 'group-hover:text-purple-600'],
                    'social' => ['bg' => 'bg-pink-100', 'hover' => 'group-hover:bg-pink-200', 'text' => 'text-pink-600', 'hover-text' => 'group-hover:text-pink-600'],
                    'email' => ['bg' => 'bg-cyan-100', 'hover' => 'group-hover:bg-cyan-200', 'text' => 'text-cyan-600', 'hover-text' => 'group-hover:text-cyan-600'],
                    'email_notifications' => ['bg' => 'bg-amber-100', 'hover' => 'group-hover:bg-amber-200', 'text' => 'text-amber-600', 'hover-text' => 'group-hover:text-amber-600'],
                    'email_templates' => ['bg' => 'bg-lime-100', 'hover' => 'group-hover:bg-lime-200', 'text' => 'text-lime-600', 'hover-text' => 'group-hover:text-lime-600'],
                    'warehouse' => ['bg' => 'bg-orange-100', 'hover' => 'group-hover:bg-orange-200', 'text' => 'text-orange-600', 'hover-text' => 'group-hover:text-orange-600'],
                    'inventory' => ['bg' => 'bg-green-100', 'hover' => 'group-hover:bg-green-200', 'text' => 'text-green-600', 'hover-text' => 'group-hover:text-green-600'],
                    'notifications' => ['bg' => 'bg-yellow-100', 'hover' => 'group-hover:bg-yellow-200', 'text' => 'text-yellow-600', 'hover-text' => 'group-hover:text-yellow-600'],
                    'report' => ['bg' => 'bg-teal-100', 'hover' => 'group-hover:bg-teal-200', 'text' => 'text-teal-600', 'hover-text' => 'group-hover:text-teal-600'],
                    'security' => ['bg' => 'bg-red-100', 'hover' => 'group-hover:bg-red-200', 'text' => 'text-red-600', 'hover-text' => 'group-hover:text-red-600'],
                    'system' => ['bg' => 'bg-gray-100', 'hover' => 'group-hover:bg-gray-200', 'text' => 'text-gray-600', 'hover-text' => 'group-hover:text-gray-600'],
                    'general' => ['bg' => 'bg-blue-100', 'hover' => 'group-hover:bg-blue-200', 'text' => 'text-blue-600', 'hover-text' => 'group-hover:text-blue-600'],
                ];
                $color = $colors[$group] ?? $colors['general'];
            @endphp

            <a href="{{ route('system.settings.show', $group) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 {{ $color['bg'] }} rounded-lg flex items-center justify-center mr-4 {{ $color['hover'] }} transition-all duration-300">
                        <i class="fas {{ $icon }} text-2xl {{ $color['text'] }}"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 {{ $color['hover-text'] }} transition-colors duration-300">
                            {{ $label }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $groupSettings->count() }} settings</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    @foreach($groupSettings->take(3) as $setting)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 truncate mr-2">
                                {{ ucfirst(str_replace('_', ' ', str_replace($group . '_', '', $setting->key))) }}
                            </span>
                            @if($setting->type === 'boolean')
                                <span class="px-2 py-1 text-xs font-semibold rounded whitespace-nowrap {{ $setting->actual_value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $setting->actual_value ? 'Enabled' : 'Disabled' }}
                                </span>
                            @elseif($setting->type === 'password')
                                <span class="text-xs text-gray-400">
                                    {{ $setting->value ? '••••••••' : 'Not set' }}
                                </span>
                            @elseif(in_array($setting->type, ['file', 'image']))
                                <span class="text-xs {{ $setting->value ? 'text-green-600' : 'text-gray-400' }}">
                                    <i class="fas {{ $setting->value ? 'fa-check-circle' : 'fa-minus-circle' }}"></i>
                                </span>
                            @else
                                <span class="text-xs text-gray-500 truncate max-w-[150px]">
                                    {{ Str::limit($setting->value ?? '—', 20) }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($groupSettings->count() > 3)
                        <p class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                            <i class="fas fa-plus-circle mr-1"></i>{{ $groupSettings->count() - 3 }} more settings
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Last updated</span>
                    <span class="text-xs font-medium text-gray-700">
                        {{ $groupSettings->sortByDesc('updated_at')->first()->updated_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($settings->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-cogs text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Settings Found</h3>
            <p class="text-gray-600">Please run database seeder to initialize system settings.</p>
        </div>
    @endif

</div>

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 animate-modal-up">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Import Settings
                </h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('system.settings.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select JSON File
                    </label>
                    <input 
                        type="file" 
                        name="file" 
                        accept=".json" 
                        required 
                        class="block w-full text-sm text-gray-500 
                            file:mr-4 file:py-2 file:px-4 
                            file:rounded-lg file:border-0 
                            file:text-sm file:font-semibold 
                            file:bg-green-50 file:text-green-700 
                            hover:file:bg-green-100
                            cursor-pointer border border-gray-300 rounded-lg
                            focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Only JSON files exported from this system are supported
                    </p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="closeImportModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-upload mr-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
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

    @keyframes modal-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }

    .animate-modal-up {
        animation: modal-up 0.3s ease-out;
    }
</style>
@endpush

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
        document.getElementById('importModal').classList.add('flex');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
        document.getElementById('importModal').classList.remove('flex');
        document.getElementById('importForm').reset();
    }

    function clearCache() {
        if (!confirm('Are you sure you want to clear all cache?')) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("system.settings.cache.clear") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }

    // Auto-hide alert messages after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.animate-fade-in');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImportModal();
        }
    });

    // Close modal on outside click
    document.getElementById('importModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeImportModal();
        }
    });
</script>
@endpush