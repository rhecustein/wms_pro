{{-- resources/views/layouts/app.blade.php --}}
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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Grid Background Pattern */
        .grid-background {
            background-color: #f9fafb;
            background-image: 
                linear-gradient(rgba(156, 163, 175, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(156, 163, 175, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Menu Active State */
        .menu-active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }

        /* Submenu Active State */
        .submenu-active {
            background-color: #374151;
            color: #60a5fa;
            border-left: 3px solid #3b82f6;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-gray-100">
        
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             x-cloak
             @click="sidebarOpen = false"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800 shadow-2xl transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 custom-scrollbar overflow-y-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-800 bg-gray-900">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                        <i class="fas fa-warehouse text-white text-xl"></i>
                    </div>
                    <div class="text-white">
                        <div class="font-bold text-xl tracking-tight">WMS Pro</div>
                        <div class="text-xs text-gray-400 font-medium">Warehouse Management</div>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="px-3 py-6 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'menu-active text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                    <i class="fas fa-home w-5 text-center mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Master Data -->
                <div x-data="{ open: {{ request()->is('master*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('master*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-database w-5 text-center mr-3 {{ request()->is('master*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Master Data</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('master.users.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.users.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-users-cog w-4 mr-2"></i> Users
                        </a>
                        <a href="{{ route('master.roles.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.roles.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-user-shield w-4 mr-2"></i> Roles
                        </a>
                        <a href="{{ route('master.warehouses.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.warehouses.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-warehouse w-4 mr-2"></i> Warehouses
                        </a>
                        <a href="{{ route('master.storage-areas.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.storage-areas.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-map-marked-alt w-4 mr-2"></i> Storage Areas
                        </a>
                        <a href="{{ route('master.storage-bins.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.storage-bins.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-th w-4 mr-2"></i> Storage Bins
                        </a>
                        <a href="{{ route('master.product-categories.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.product-categories.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-tags w-4 mr-2"></i> Product Categories
                        </a>
                        <a href="{{ route('master.products.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.products.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-box w-4 mr-2"></i> Products
                        </a>
                        <a href="{{ route('master.customers.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.customers.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-users w-4 mr-2"></i> Customers
                        </a>
                        <a href="{{ route('master.vendors.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('master.vendors.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-truck w-4 mr-2"></i> Vendors
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <div x-data="{ open: {{ request()->is('inventory*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('inventory*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cubes w-5 text-center mr-3 {{ request()->is('inventory*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Inventory</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('inventory.stocks.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inventory.stocks.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-list w-4 mr-2"></i> Stock List
                        </a>
                        <a href="{{ route('inventory.pallets.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inventory.pallets.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-pallet w-4 mr-2"></i> Pallets
                        </a>
                        <a href="{{ route('inventory.movements.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inventory.movements.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-exchange-alt w-4 mr-2"></i> Movements
                        </a>
                        <a href="{{ route('inventory.adjustments.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inventory.adjustments.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-edit w-4 mr-2"></i> Adjustments
                        </a>
                        <a href="{{ route('inventory.opnames.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inventory.opnames.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-clipboard-check w-4 mr-2"></i> Stock Opname
                        </a>
                    </div>
                </div>

                <!-- Inbound -->
                <div x-data="{ open: {{ request()->is('inbound*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('inbound*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-arrow-down w-5 text-center mr-3 {{ request()->is('inbound*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Inbound</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('inbound.purchase-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inbound.purchase-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-shopping-cart w-4 mr-2"></i> Purchase Orders
                        </a>
                        <a href="{{ route('inbound.shipments.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inbound.shipments.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-ship w-4 mr-2"></i> Shipments
                        </a>
                        <a href="{{ route('inbound.good-receivings.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inbound.good-receivings.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-inbox w-4 mr-2"></i> Good Receiving
                        </a>
                        <a href="{{ route('inbound.putaway-tasks.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('inbound.putaway-tasks.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-dolly w-4 mr-2"></i> Putaway Tasks
                        </a>
                    </div>
                </div>

                <!-- Outbound -->
                <div x-data="{ open: {{ request()->is('outbound*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('outbound*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-arrow-up w-5 text-center mr-3 {{ request()->is('outbound*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Outbound</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('outbound.sales-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('outbound.sales-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-file-invoice w-4 mr-2"></i> Sales Orders
                        </a>
                        <a href="{{ route('outbound.picking-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('outbound.picking-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-hand-paper w-4 mr-2"></i> Picking Orders
                        </a>
                        <a href="{{ route('outbound.packing-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('outbound.packing-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-box-open w-4 mr-2"></i> Packing Orders
                        </a>
                        <a href="{{ route('outbound.delivery-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('outbound.delivery-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-shipping-fast w-4 mr-2"></i> Delivery Orders
                        </a>
                        <a href="{{ route('outbound.returns.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('outbound.returns.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-undo w-4 mr-2"></i> Returns
                        </a>
                    </div>
                </div>

                <!-- Operations -->
                <div x-data="{ open: {{ request()->is('operations*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('operations*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cogs w-5 text-center mr-3 {{ request()->is('operations*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Operations</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('operations.replenishments.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('operations.replenishments.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-sync-alt w-4 mr-2"></i> Replenishment
                        </a>
                        <a href="{{ route('operations.transfer-orders.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('operations.transfer-orders.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-exchange-alt w-4 mr-2"></i> Transfers
                        </a>
                        <a href="{{ route('operations.cross-docking.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('operations.cross-docking.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-random w-4 mr-2"></i> Cross Docking
                        </a>
                    </div>
                </div>

                <!-- Equipment -->
                <div x-data="{ open: {{ request()->is('equipment*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('equipment*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-tools w-5 text-center mr-3 {{ request()->is('equipment*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Equipment</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('equipment.vehicles.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.vehicles.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-truck-moving w-4 mr-2"></i> Vehicles
                        </a>
                        <a href="{{ route('equipment.equipments.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('equipment.equipments.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-tools w-4 mr-2"></i> Equipments
                        </a>
                    </div>
                </div>

                <!-- Reports -->
                <!-- <div x-data="{ open: {{ request()->is('reports*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('reports*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar w-5 text-center mr-3 {{ request()->is('reports*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>Reports</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('reports.inventory.stock-summary') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('reports.inventory.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-file-alt w-4 mr-2"></i> Inventory Reports
                        </a>
                        <a href="{{ route('reports.inbound.receiving-report') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('reports.inbound.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-file-download w-4 mr-2"></i> Inbound Reports
                        </a>
                        <a href="{{ route('reports.outbound.picking-report') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('reports.outbound.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-file-upload w-4 mr-2"></i> Outbound Reports
                        </a>
                        <a href="{{ route('reports.operations.daily-summary') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('reports.operations.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-calendar-day w-4 mr-2"></i> Operations Reports
                        </a>
                        <a href="{{ route('reports.kpi.dashboard') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('reports.kpi.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-tachometer-alt w-4 mr-2"></i> KPI Dashboard
                        </a>
                    </div>
                </div> -->

                <!-- Mobile App -->
                <!-- <a href="{{ route('mobile.good-receiving.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('mobile*') ? 'menu-active text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                    <i class="fas fa-mobile-alt w-5 text-center mr-3 {{ request()->is('mobile*') ? 'text-white' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                    <span>Mobile App</span>
                </a> -->

                <!-- System -->
                <!-- <div x-data="{ open: {{ request()->is('system*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group {{ request()->is('system*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <i class="fas fa-cog w-5 text-center mr-3 {{ request()->is('system*') ? 'text-blue-400' : 'text-gray-400 group-hover:text-blue-400' }}"></i>
                            <span>System</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak x-transition class="ml-4 mt-2 space-y-1">
                        <a href="{{ route('system.settings.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('system.settings.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-sliders-h w-4 mr-2"></i> Settings
                        </a>
                        <a href="{{ route('system.activity-logs.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('system.activity-logs.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-history w-4 mr-2"></i> Activity Logs
                        </a>
                        <a href="{{ route('system.notifications.index') }}" 
                           class="flex items-center px-4 py-2 text-sm rounded-lg transition-all duration-200 {{ request()->routeIs('system.notifications.*') ? 'submenu-active' : 'text-gray-400 hover:bg-gray-800 hover:text-white hover:translate-x-1' }}">
                            <i class="fas fa-bell w-4 mr-2"></i> Notifications
                        </a>
                    </div>
                </div> -->
            </nav>

            <!-- User Info -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800 bg-gray-900">
                <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-gray-800 hover:bg-gray-750 transition-all cursor-pointer group">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Navigation -->
            <header class="bg-white border-b border-gray-200 shadow-sm z-10">
                <div class="flex items-center justify-between h-16 px-6">
                    <!-- Mobile Menu Button & Page Title -->
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-3">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <input type="search" placeholder="Search..." class="w-64 pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <i class="fas fa-search text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden">
                                <div class="p-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                    <a href="#" class="block p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-blue-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">New order received</p>
                                                <p class="text-xs text-gray-500">Order #12345 needs processing</p>
                                                <p class="text-xs text-gray-400 mt-1">2 minutes ago</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-3 border-t border-gray-200 text-center bg-gray-50">
                                    <a href="{{ route('system.notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">Administrator</div>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200">
                                <div class="p-4 border-b border-gray-200">
                                    <div class="font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                                    <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="py-2">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-user w-4 mr-3 text-gray-500"></i>
                                        Profile Settings
                                    </a>
                                    <a href="{{ route('system.settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-cog w-4 mr-3 text-gray-500"></i>
                                        System Settings
                                    </a>
                                </div>
                                <div class="border-t border-gray-200 py-2">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content with Grid Background -->
            <main class="flex-1 overflow-y-auto custom-scrollbar grid-background">
                <div class="p-6">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-6 px-4 py-3 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg flex items-center justify-between shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 px-4 py-3 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg flex items-center justify-between shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 px-4 py-3 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-3 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="font-semibold mb-2">Please fix the following errors:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li class="text-sm">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Main Content --}}
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
                <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-600">
                    <div>
                        © {{ date('Y') }} <span class="font-semibold text-gray-900">WMS Pro</span>. All rights reserved.
                    </div>
                    <div class="flex items-center space-x-4 mt-2 md:mt-0">
                        <a href="#" class="hover:text-blue-600 transition-colors">Documentation</a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-blue-600 transition-colors">Support</a>
                        <span class="text-gray-300">|</span>
                        <a href="#" class="hover:text-blue-600 transition-colors">Version 1.0.0</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>