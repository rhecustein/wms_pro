<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'WMS Pro') }} - Professional Warehouse Management System</title>
    
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
                linear-gradient(to right, rgba(229, 231, 235, 0.3) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(229, 231, 235, 0.3) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        .grid-pattern-hero {
            background-image: 
                linear-gradient(to right, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        
        @keyframes blob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(20px, -50px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(50px, 50px) scale(1.05); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        .animate-bounce-slow { animation: bounce 3s infinite; }
    </style>
</head>
<body class="antialiased bg-white" x-data="{ mobileMenuOpen: false, scrolled: false }" 
      @scroll.window="scrolled = window.pageYOffset > 50">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300" 
         :class="scrolled ? 'bg-white shadow-lg' : 'bg-white/90 backdrop-blur-sm'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & App Name -->
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                            WMS Pro
                        </h1>
                        <p class="text-xs text-gray-500">Warehouse Management System</p>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-blue-600 font-medium transition">Features</a>
                    <a href="#modules" class="text-gray-700 hover:text-blue-600 font-medium transition">Modules</a>
                    <a href="#benefits" class="text-gray-700 hover:text-blue-600 font-medium transition">Benefits</a>
                    <a href="#pricing" class="text-gray-700 hover:text-blue-600 font-medium transition">Pricing</a>
                    
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition font-medium">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg hover:shadow-lg transform hover:-translate-y-0.5 transition font-medium">
                            Login
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-cloak x-transition class="md:hidden bg-white border-t">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-gray-700 hover:text-blue-600 font-medium">Features</a>
                <a href="#modules" class="block text-gray-700 hover:text-blue-600 font-medium">Modules</a>
                <a href="#benefits" class="block text-gray-700 hover:text-blue-600 font-medium">Benefits</a>
                <a href="#pricing" class="block text-gray-700 hover:text-blue-600 font-medium">Pricing</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg text-center font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="block w-full px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg text-center font-medium">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <!-- Grid Pattern Background -->
        <div class="absolute inset-0 grid-pattern-hero"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 via-indigo-50/50 to-purple-50/50"></div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-20 right-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-40 left-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center space-x-2 bg-blue-100 text-blue-700 px-4 py-2 rounded-full text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span>Enterprise-Grade Solution</span>
                    </div>

                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                        Complete
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                            Warehouse
                        </span>
                        Management System
                    </h1>

                    <p class="text-xl text-gray-600 leading-relaxed">
                        Streamline your warehouse operations with real-time inventory tracking, efficient workflows, and comprehensive reporting. Perfect for single company with multiple warehouse locations.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition font-semibold text-center">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition font-semibold text-center">
                                Get Started
                            </a>
                        @endauth
                        <a href="#features" class="px-8 py-4 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:border-blue-600 hover:text-blue-600 transition font-semibold text-center">
                            Explore Features
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8">
                        <div>
                            <div class="text-3xl font-bold text-blue-600">100%</div>
                            <div class="text-sm text-gray-600">Customizable</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-blue-600">Multi</div>
                            <div class="text-sm text-gray-600">Warehouse</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-blue-600">24/7</div>
                            <div class="text-sm text-gray-600">Ready</div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Dashboard Preview -->
                <div class="relative">
                    <div class="relative z-10 bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">Total Stock</div>
                                        <div class="text-sm text-gray-500">Real-time tracking</div>
                                    </div>
                                </div>
                                <div class="text-2xl font-bold text-blue-600">12,458</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <div class="text-sm text-gray-600">Inbound Today</div>
                                    <div class="text-2xl font-bold text-green-600">+245</div>
                                </div>
                                <div class="p-4 bg-orange-50 rounded-lg">
                                    <div class="text-sm text-gray-600">Outbound Today</div>
                                    <div class="text-2xl font-bold text-orange-600">-189</div>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg text-white">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm">Warehouse Utilization</span>
                                    <span class="font-bold">87%</span>
                                </div>
                                <div class="w-full bg-blue-400 rounded-full h-2">
                                    <div class="bg-white rounded-full h-2" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Card -->
                    <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-lg p-4 animate-bounce-slow border border-gray-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <span class="font-semibold text-gray-900">Order Picked</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 relative">
        <div class="absolute inset-0 grid-pattern"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Everything you need to manage your warehouse operations efficiently
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach([
                    ['title' => 'Real-Time Inventory', 'desc' => 'Track stock levels in real-time with automatic updates and alert notifications', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'blue'],
                    ['title' => 'Smart Put-Away', 'desc' => 'Automated location suggestions based on product type and FEFO/FIFO rules', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'color' => 'green'],
                    ['title' => 'Mobile Scanning', 'desc' => 'Barcode and QR code scanning using mobile app for warehouse operators', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'color' => 'purple'],
                    ['title' => 'Advanced Analytics', 'desc' => 'Comprehensive reports, KPIs, and insights for warehouse performance optimization', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'orange'],
                    ['title' => 'Auto Replenishment', 'desc' => 'Automatic replenishment suggestions from high rack to pick face area', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'cyan'],
                    ['title' => 'Multi-Warehouse', 'desc' => 'Manage multiple warehouses and inter-warehouse transfers in one platform', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'yellow']
                ] as $feature)
                <div class="group p-8 bg-white rounded-2xl hover:shadow-xl transition transform hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 bg-{{ $feature['color'] }}-100 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-7 h-7 text-{{ $feature['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
        <div class="absolute inset-0 grid-pattern opacity-50"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Complete WMS Modules</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Comprehensive module suite covering all warehouse operations
                </p>
            </div>

            <div class="grid lg:grid-cols-4 gap-6">
                @foreach([
                    ['name' => 'Master Data', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'color' => 'blue', 'items' => ['Warehouses', 'Storage Locations', 'Products', 'Customers & Vendors']],
                    ['name' => 'Inbound', 'icon' => 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4', 'color' => 'green', 'items' => ['Purchase Orders', 'Good Receiving', 'Quality Check', 'Put-Away Tasks']],
                    ['name' => 'Outbound', 'icon' => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1', 'color' => 'orange', 'items' => ['Sales Orders', 'Picking Orders', 'Packing & Dispatch', 'Delivery Tracking']],
                    ['name' => 'Inventory', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'color' => 'purple', 'items' => ['Real-time Stock', 'Stock Movements', 'Cycle Count', 'Adjustments']],
                    ['name' => 'Operations', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'color' => 'cyan', 'items' => ['Replenishment', 'Transfers', 'Cross Docking', 'Task Management']],
                    ['name' => 'Equipment', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'yellow', 'items' => ['Vehicle Management', 'Equipment Tracking', 'Maintenance Log', 'Utilization Report']],
                    ['name' => 'Reports', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'indigo', 'items' => ['KPI Dashboard', 'Performance Metrics', 'Inventory Reports', 'Export to Excel/PDF']],
                    ['name' => 'Mobile App', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'color' => 'pink', 'items' => ['Barcode Scanner', 'Mobile Picking', 'Mobile Receiving', 'Offline Mode']]
                ] as $module)
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-2xl transition border border-gray-100">
                    <div class="w-12 h-12 bg-{{ $module['color'] }}-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-{{ $module['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $module['name'] }}</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        @foreach($module['items'] as $item)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50 relative">
        <div class="absolute inset-0 grid-pattern"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Key Benefits</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Transform your warehouse operations and boost productivity
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['stat' => '40%', 'title' => 'Cost Reduction', 'desc' => 'Optimize workforce, reduce errors, and minimize waste with automated processes'],
                    ['stat' => '98%', 'title' => 'Inventory Accuracy', 'desc' => 'Real-time tracking and cycle counting ensure maximum accuracy'],
                    ['stat' => '3x', 'title' => 'Faster Fulfillment', 'desc' => 'Efficient picking and packing processes speed up delivery'],
                    ['stat' => '⚡', 'title' => 'Real-Time Visibility', 'desc' => 'Track every item, movement, and transaction in real-time'],
                    ['stat' => '🔒', 'title' => 'Data Security', 'desc' => 'Enterprise-grade security with role-based access control'],
                    ['stat' => '🔄', 'title' => 'Easy Integration', 'desc' => 'Seamless integration with ERP and logistics systems']
                ] as $benefit)
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <span class="text-3xl font-bold text-white">{{ $benefit['stat'] }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $benefit['title'] }}</h3>
                    <p class="text-gray-600">{{ $benefit['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 px-4 sm:px-6 lg:px-8 bg-white relative">
        <div class="absolute inset-0 grid-pattern opacity-50"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Simple Pricing</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    One-time purchase with lifetime updates
                </p>
            </div>

            <div class="max-w-3xl mx-auto">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl shadow-2xl p-12 border-2 border-blue-200">
                    <div class="text-center mb-8">
                        <h3 class="text-3xl font-bold text-gray-900 mb-4">Extended License</h3>
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-5xl font-extrabold text-blue-600">$49</span>
                            <span class="text-gray-600">one-time</span>
                        </div>
                    </div>

                    <ul class="space-y-4 mb-8">
                        @foreach([
                            'Full Source Code Access',
                            'Lifetime Free Updates',
                            'Multi-Warehouse Support',
                            'Mobile App Included',
                            'Complete Documentation',
                            '6 Months Support',
                            'Commercial Use License',
                            'No Monthly Fees'
                        ] as $feature)
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 font-medium">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    <div class="text-center">
                        <a href="https://codecanyon.net" target="_blank" class="inline-block px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition font-semibold">
                            Purchase on CodeCanyon
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-indigo-700 relative">
        <div class="absolute inset-0 grid-pattern-hero opacity-20"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h2 class="text-4xl font-bold text-white mb-6">
                Ready to Optimize Your Warehouse?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Get started with WMS Pro today and transform your operations
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-blue-600 rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition font-semibold">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-blue-600 rounded-xl hover:shadow-2xl transform hover:-translate-y-1 transition font-semibold">
                        Get Started Now
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 sm:px-6 lg:px-8 relative">
        <div class="absolute inset-0 grid-pattern opacity-10"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <!-- Product Info -->
                <div class="col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">WMS Pro</span>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">
                        Professional Warehouse Management System<br>
                        Built with Laravel 12 & Tailwind CSS
                    </p>
                    <p class="text-gray-400 text-sm">
                        Complete integrated solution for efficient, accurate, and real-time warehouse management.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#modules" class="hover:text-white transition">Modules</a></li>
                        <li><a href="#benefits" class="hover:text-white transition">Benefits</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Documentation</li>
                        <li>Video Tutorials</li>
                        <li>Support Ticket</li>
                        <li>FAQ</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-400">
                    © {{ date('Y') }} WMS Pro. All rights reserved.
                </p>
                <p class="text-sm text-gray-400 mt-4 md:mt-0">
                    Version 1.0 | Laravel 12 | Tailwind CSS
                </p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>