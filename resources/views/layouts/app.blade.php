<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WMS Pro') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false, profileDropdownOpen: false, notificationOpen: false }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               @click.away="sidebarOpen = false">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-800">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="text-white">
                        <div class="font-bold text-lg">WMS Pro</div>
                        <div class="text-xs text-gray-400">Warehouse System</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Master Data -->
                <div x-data="{ open: {{ request()->is('master*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Master Data</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('master.warehouses.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Warehouses</a>
                        <a href="{{ route('master.storage-areas.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Storage Areas</a>
                        <a href="{{ route('master.storage-bins.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Storage Bins</a>
                        <a href="{{ route('master.products.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Products</a>
                        <a href="{{ route('master.customers.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Customers</a>
                        <a href="{{ route('master.vendors.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Vendors</a>
                    </div>
                </div>

                <!-- Inventory -->
                <div x-data="{ open: {{ request()->is('inventory*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Inventory</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('inventory.stocks.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Stock List</a>
                        <a href="{{ route('inventory.movements.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Movements</a>
                        <a href="{{ route('inventory.adjustments.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Adjustments</a>
                        <a href="{{ route('inventory.opnames.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Stock Opname</a>
                        <a href="{{ route('inventory.pallets.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Pallets</a>
                    </div>
                </div>

                <!-- Inbound -->
                <div x-data="{ open: {{ request()->is('inbound*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                            </svg>
                            <span>Inbound</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('inbound.purchase-orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Purchase Orders</a>
                        <a href="{{ route('inbound.shipments.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Shipments</a>
                        <a href="{{ route('inbound.good-receivings.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Good Receiving</a>
                        <a href="{{ route('inbound.putaway-tasks.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Putaway Tasks</a>
                    </div>
                </div>

                <!-- Outbound -->
                <div x-data="{ open: {{ request()->is('outbound*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span>Outbound</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('outbound.sales-orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Sales Orders</a>
                        <a href="{{ route('outbound.picking-orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Picking Orders</a>
                        <a href="{{ route('outbound.packing-orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Packing Orders</a>
                        <a href="{{ route('outbound.delivery-orders.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Delivery Orders</a>
                        <a href="{{ route('outbound.returns.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Returns</a>
                    </div>
                </div>

                <!-- Operations -->
                <div x-data="{ open: {{ request()->is('operations*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Operations</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('operations.replenishments.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Replenishment</a>
                        <a href="{{ route('operations.transfers.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Transfers</a>
                        <a href="{{ route('operations.cross-docking.index') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Cross Docking</a>
                    </div>
                </div>

                <!-- Reports -->
                <div x-data="{ open: {{ request()->is('reports*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Reports</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-8 mt-2 space-y-1">
                        <a href="{{ route('reports.inventory.stock-summary') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Inventory Reports</a>
                        <a href="{{ route('reports.inbound.receiving-report') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Inbound Reports</a>
                        <a href="{{ route('reports.outbound.picking-report') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">Outbound Reports</a>
                        <a href="{{ route('reports.kpi.dashboard') }}" class="block px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-gray-800 hover:text-white transition">KPI Dashboard</a>
                    </div>
                </div>

                <!-- Mobile App -->
                <a href="{{ route('mobile.good-receiving.index') }}" class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-gray-800 hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Mobile App</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-gray-800">
                <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-800">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200 z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Page Title -->
                    <div class="hidden lg:block">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="search" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                                <div class="p-4 border-b border-gray-200">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="block p-4 hover:bg-gray-50 transition">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">New order received</p>
                                                <p class="text-xs text-gray-500">Order #12345 needs processing</p>
                                                <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-3 border-t border-gray-200 text-center">
                                    <a href="{{ route('system.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">Admin</div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="font-medium text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Profile Settings
                                    </a>
                                    <a href="{{ route('system.settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        System Settings
                                    </a>
                                </div>
                                <div class="border-t border-gray-200 py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>