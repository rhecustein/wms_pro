<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Dynamic Title from Settings --}}
    <title>Lupa Password - {{ setting('site_name', 'WMS Pro') }}</title>
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ setting('site_description', 'Professional Warehouse Management System') }}">
    
    {{-- Favicon --}}
    @if(setting('site_favicon'))
        <link rel="icon" type="image/x-icon" href="{{ Storage::url(setting('site_favicon')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        {{-- Dynamic Theme Colors --}}
        :root {
            --color-primary: {{ setting('theme_primary_color', '#3b82f6') }};
            --color-secondary: {{ setting('theme_secondary_color', '#6366f1') }};
        }
        
        /* Grid Pattern Background */
        .grid-background {
            background-color: #f9fafb;
            background-image: 
                linear-gradient(rgba(209, 213, 219, 0.3) 1px, transparent 1px),
                linear-gradient(90deg, rgba(209, 213, 219, 0.3) 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: center center;
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.5;
            }
            100% {
                transform: scale(0.95);
                opacity: 1;
            }
        }
        
        .animate-pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }

        /* Dynamic gradient colors */
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary));
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .text-primary {
            color: var(--color-primary);
        }

        .text-primary:hover {
            opacity: 0.8;
        }

        .border-primary {
            border-color: var(--color-primary);
        }

        .bg-primary-light {
            background-color: color-mix(in srgb, var(--color-primary) 10%, white);
        }

        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 20%, white);
            border-color: var(--color-primary);
        }
    </style>
</head>
<body class="antialiased bg-gray-50 grid-background">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 relative">
        <!-- Decorative Elements with Dynamic Colors -->
        <div class="absolute top-20 right-20 w-72 h-72 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="background-color: var(--color-primary);"></div>
        <div class="absolute bottom-20 left-20 w-72 h-72 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="background-color: var(--color-secondary); animation-delay: 1s;"></div>

        <div class="w-full max-w-md space-y-8 relative z-10">
            <!-- Icon & Header -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center mb-8 group">
                    @if(setting('site_logo'))
                        <img src="{{ Storage::url(setting('site_logo')) }}" 
                             alt="{{ setting('site_name', 'WMS Pro') }}" 
                             class="h-16 w-auto group-hover:scale-105 transition">
                    @else
                        <div class="w-16 h-16 bg-gradient-primary rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    @endif
                </a>

                <!-- Animated Lock Icon -->
                <div class="mb-6 inline-flex items-center justify-center">
                    <div class="relative">
                        <div class="w-24 h-24 rounded-full flex items-center justify-center bg-primary-light">
                            <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div class="absolute inset-0 w-24 h-24 rounded-full animate-pulse-ring opacity-50 bg-primary-light"></div>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Lupa Password?
                </h2>
                <p class="text-gray-600">
                    Tidak masalah! Masukkan email Anda dan kami akan mengirimkan link reset password.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm text-green-800">
                                <p class="font-medium mb-1">Email Terkirim!</p>
                                <p>{{ session('status') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Terjadi Kesalahan!</span>
                        </div>
                        <ul class="text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autofocus
                                class="focus-ring block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg transition @error('email') border-red-500 @enderror"
                                placeholder="nama@{{ setting('company_email') ? explode('@', setting('company_email'))[1] : 'company.com' }}"
                            >
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">
                            Pastikan menggunakan email yang terdaftar di sistem
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center px-6 py-3 btn-primary text-white font-semibold rounded-lg hover:shadow-lg transform transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="focus:ring-color: var(--color-primary);"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Kirim Link Reset Password
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau</span>
                    </div>
                </div>

                <!-- Back to Login -->
                <a 
                    href="{{ route('login') }}" 
                    class="w-full flex justify-center items-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Login
                </a>
            </div>

            <!-- Help Section -->
            <div class="bg-primary-light border border-primary rounded-2xl p-6 text-center space-y-3" style="border-color: color-mix(in srgb, var(--color-primary) 30%, white);">
                <div class="flex justify-center">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: color-mix(in srgb, var(--color-primary) 20%, white);">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Butuh Bantuan?</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Jika Anda mengalami kesulitan reset password, silakan hubungi IT Support kami
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 text-sm">
                        @if(setting('company_email'))
                        <a href="mailto:{{ setting('company_email') }}" class="inline-flex items-center text-primary hover:opacity-80 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ setting('company_email') }}
                        </a>
                        @endif
                        
                        @if(setting('company_phone'))
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <a href="tel:{{ setting('company_phone') }}" class="inline-flex items-center text-primary hover:opacity-80 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ setting('company_phone') }}
                        </a>
                        @endif

                        @if(setting('company_whatsapp'))
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('company_whatsapp')) }}" target="_blank" class="inline-flex items-center text-primary hover:opacity-80 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            WhatsApp
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                <div class="flex items-start justify-center text-sm text-gray-600">
                    <svg class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div class="text-left">
                        <p class="font-medium text-gray-900 mb-1">Keamanan Terjamin</p>
                        <p class="text-xs">Link reset password hanya berlaku selama 60 menit dan hanya dapat digunakan satu kali.</p>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>
</body>
</html>