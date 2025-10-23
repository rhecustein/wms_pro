<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - WMS Cakraindo</title>
    
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
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Login Form -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="w-full max-w-md space-y-8">
                <!-- Logo & Header -->
                <div class="text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center space-x-3 mb-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                                WMS Cakraindo
                            </div>
                            <div class="text-xs text-gray-500">Warehouse Management System</div>
                        </div>
                    </a>
                    
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        Selamat Datang Kembali
                    </h2>
                    <p class="text-gray-600">
                        Silakan login dengan akun karyawan Anda
                    </p>
                </div>

                <!-- Login Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">Login Gagal!</span>
                            </div>
                            <ul class="text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    class="block w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                                    placeholder="nama@cakraindo.com"
                                >
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative" x-data="{ showPassword: false }">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input 
                                    id="password" 
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    class="block w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                                    placeholder="••••••••"
                                >
                                <button 
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600"
                                >
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-semibold rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Butuh bantuan?</span>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="text-center space-y-2">
                        <p class="text-sm text-gray-600">
                            Hubungi IT Support untuk bantuan login atau reset password
                        </p>
                        <div class="flex items-center justify-center space-x-4 text-sm">
                            <a href="mailto:it.support@cakraindo.com" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="tel:+6221xxxxxxx" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Phone
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-700 relative overflow-hidden">
            <!-- Grid Pattern -->
            <div class="absolute inset-0 grid-pattern opacity-20"></div>
            
            <!-- Decorative Circles -->
            <div class="absolute top-20 right-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 left-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center justify-center w-full px-12 text-white">
                <div class="max-w-lg space-y-8">
                    <!-- Animated Warehouse Icon -->
                    <div class="flex justify-center mb-8">
                        <div class="relative">
                            <div class="w-48 h-48 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center animate-float border border-white/20">
                                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <!-- Floating Dots -->
                            <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full animate-pulse"></div>
                            <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-green-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-4">
                            Warehouse Management System
                        </h2>
                        <p class="text-xl text-blue-100">
                            PT Cakraindo Mitra Internasional
                        </p>
                    </div>

                    <!-- Features List -->
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Real-Time Inventory Tracking</div>
                                <div class="text-sm text-blue-100">Monitor stok secara real-time di semua lokasi</div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Automated Workflows</div>
                                <div class="text-sm text-blue-100">Otomasi proses inbound, outbound, dan operasional</div>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3 bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-semibold">Multi-Location Management</div>
                                <div class="text-sm text-blue-100">Kelola Jakarta, Medan, dan Surabaya dalam satu sistem</div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold">3+</div>
                            <div class="text-sm text-blue-100">Warehouses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">50K+</div>
                            <div class="text-sm text-blue-100">Items</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">24/7</div>
                            <div class="text-sm text-blue-100">Operations</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>