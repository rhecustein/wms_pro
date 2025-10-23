<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - WMS Cakraindo</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Grid Pattern Background */
        .grid-pattern {
            background-image: 
                linear-gradient(to right, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
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
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12 relative">
        <!-- Grid Pattern Background -->
        <div class="absolute inset-0 grid-pattern opacity-50"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-20 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-20 left-20 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 1s"></div>

        <div class="w-full max-w-md space-y-8 relative z-10">
            <!-- Icon & Header -->
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center mb-8 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </a>

                <!-- Animated Lock Icon -->
                <div class="mb-6 inline-flex items-center justify-center">
                    <div class="relative">
                        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <div class="absolute inset-0 w-24 h-24 bg-blue-200 rounded-full animate-pulse-ring opacity-50"></div>
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
                                class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                                placeholder="nama@cakraindo.com"
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
                        class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 text-center space-y-3">
                <div class="flex justify-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <a href="mailto:it.support@cakraindo.com" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            it.support@cakraindo.com
                        </a>
                        <span class="hidden sm:inline text-gray-300">|</span>
                        <a href="tel:+6221xxxxxxx" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            (021) XXXX-XXXX
                        </a>
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
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition">
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