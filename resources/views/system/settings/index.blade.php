{{-- resources/views/system/settings/index.blade.php --}}
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
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            <a href="{{ route('system.settings.export') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-file-export mr-2"></i>Export
            </a>
            <form action="{{ route('system.settings.clear-cache') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition" onclick="return confirm('Clear all cache?')">
                    <i class="fas fa-broom mr-2"></i>Clear Cache
                </button>
            </form>
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

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
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
                    'identity' => 'blue',
                    'company' => 'indigo',
                    'appearance' => 'purple',
                    'social' => 'pink',
                    'warehouse' => 'orange',
                    'inventory' => 'green',
                    'notifications' => 'yellow',
                    'report' => 'teal',
                    'security' => 'red',
                    'system' => 'gray',
                    'general' => 'blue',
                ];
                $color = $colors[$group] ?? 'blue';
            @endphp

            <a href="{{ route('system.settings.show', $group) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition group">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-{{ $color }}-200 transition">
                        <i class="fas {{ $icon }} text-2xl text-{{ $color }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-{{ $color }}-600 transition">
                            {{ $label }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $groupSettings->count() }} settings</p>
                    </div>
                </div>
                
                <div class="space-y-2">
                    @foreach($groupSettings->take(3) as $setting)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', str_replace($group . '_', '', $setting->key))) }}</span>
                            @if($setting->type === 'boolean')
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $setting->actual_value ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $setting->actual_value ? 'Enabled' : 'Disabled' }}
                                </span>
                            @elseif(in_array($setting->type, ['file', 'image']))
                                <span class="text-xs text-gray-500">
                                    {{ $setting->value ? '✓' : '—' }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500 truncate max-w-[150px]">
                                    {{ Str::limit($setting->value ?? '—', 20) }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                    
                    @if($groupSettings->count() > 3)
                        <p class="text-xs text-gray-500 pt-2">
                            + {{ $groupSettings->count() - 3 }} more settings
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm text-gray-600">Last updated</span>
                    <span class="text-sm font-medium text-gray-800">
                        {{ $groupSettings->sortByDesc('updated_at')->first()->updated_at->diffForHumans() }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>

</div>

{{-- Import Modal --}}
<div id="importModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-file-import text-green-600 mr-2"></i>
                    Import Settings
                </h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('system.settings.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select JSON File
                    </label>
                    <input type="file" name="file" accept=".json" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-2">
                        Only JSON files exported from this system are supported
                    </p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
    /* Fix for dynamic Tailwind classes */
    .bg-blue-100, .text-blue-600, .text-blue-800, .bg-blue-200,
    .bg-indigo-100, .text-indigo-600, .text-indigo-800, .bg-indigo-200,
    .bg-purple-100, .text-purple-600, .text-purple-800, .bg-purple-200,
    .bg-pink-100, .text-pink-600, .text-pink-800, .bg-pink-200,
    .bg-orange-100, .text-orange-600, .text-orange-800, .bg-orange-200,
    .bg-green-100, .text-green-600, .text-green-800, .bg-green-200,
    .bg-yellow-100, .text-yellow-600, .text-yellow-800, .bg-yellow-200,
    .bg-teal-100, .text-teal-600, .text-teal-800, .bg-teal-200,
    .bg-red-100, .text-red-600, .text-red-800, .bg-red-200,
    .bg-gray-100, .text-gray-600, .text-gray-800, .bg-gray-200 {
        /* Ensure these classes are generated */
    }
</style>
@endpush