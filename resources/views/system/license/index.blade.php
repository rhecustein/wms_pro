<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('License Verification') }}
        </2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if($isVerified)
                {{-- License sudah verified --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <svg class="w-12 h-12 text-green-500 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">License Verified</h3>
                                <p class="text-gray-600">Your purchase code has been successfully verified.</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 mt-6 pt-6">
                            <dl class="divide-y divide-gray-200">
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Purchase Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ substr($license['purchase_code']->value ?? '', 0, 8) }}****
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Buyer Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $license['buyer_email']->value ?? '-' }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">License Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($license['license_type']->value ?? 'Regular') }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Installed At</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $license['installed_at']->value ?? '-' }}
                                    </dd>
                                </div>
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Domain</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $license['installed_domain']->value ?? '-' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            @else
                {{-- Form verify license --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Purchase</h3>
                            <p class="text-gray-600">Please enter your CodeCanyon purchase code to activate all features.</p>
                        </div>

                        <form action="{{ route('system.license.verify') }}" method="POST" class="space-y-6">
                            @csrf

                            {{-- Purchase Code --}}
                            <div>
                                <label for="purchase_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Purchase Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="purchase_code" 
                                       id="purchase_code" 
                                       value="{{ old('purchase_code') }}"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('purchase_code') border-red-500 @enderror" 
                                       placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                       required>
                                @error('purchase_code')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Buyer Email --}}
                            <div>
                                <label for="buyer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Buyer Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="buyer_email" 
                                       id="buyer_email" 
                                       value="{{ old('buyer_email') }}"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('buyer_email') border-red-500 @enderror" 
                                       placeholder="your@email.com"
                                       required>
                                @error('buyer_email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Help Text --}}
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-blue-900 mb-2">Where to find your purchase code?</h4>
                                <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                                    <li>Log in to your Envato account</li>
                                    <li>Go to <strong>Downloads</strong> page</li>
                                    <li>Click <strong>"License certificate & purchase code"</strong></li>
                                    <li>Your purchase code will be displayed</li>
                                </ol>
                            </div>

                            {{-- Submit Button --}}
                            <div>
                                <button type="submit" 
                                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Verify Purchase Code
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>