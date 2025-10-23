{{-- resources/views/master/customers/orders.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Orders')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-shopping-cart text-blue-600 mr-2"></i>
                Customer Orders
            </h1>
            <p class="text-sm text-gray-600 mt-1">{{ $customer->name }} - {{ $customer->code }}</p>
        </div>
        <a href="{{ route('master.customers.show', $customer) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customer
        </a>
    </div>

    {{-- Customer Summary Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4 {{ $customer->customer_type === 'vip' ? 'bg-yellow-100' : ($customer->customer_type === 'wholesale' ? 'bg-purple-100' : 'bg-blue-100') }}">
                    <i class="fas {{ $customer->customer_type === 'vip' ? 'fa-crown text-yellow-600' : ($customer->customer_type === 'wholesale' ? 'fa-boxes text-purple-600' : 'fa-user text-blue-600') }}"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $customer->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $customer->company_name ?? $customer->email }}</p>
                </div>
            </div>
            
            <div class="text-right">
                @if($customer->credit_limit)
                    <p class="text-xs text-gray-500">Credit Limit</p>
                    <p class="text-lg font-bold text-green-600">Rp {{ number_format($customer->credit_limit, 0, ',', '.') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Orders List Placeholder --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-cart text-4xl text-blue-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Orders Yet</h3>
            <p class="text-gray-600 mb-4">This customer hasn't placed any orders yet.</p>
            <p class="text-sm text-gray-500">Orders will appear here once the customer makes a purchase.</p>
        </div>
    </div>

</div>
@endsection