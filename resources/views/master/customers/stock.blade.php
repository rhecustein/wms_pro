{{-- resources/views/master/customers/stock.blade.php --}}
@extends('layouts.app')

@section('title', 'Customer Stock')

@section('content')
<div class="container-fluid px-4 py-6">
    
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-boxes text-blue-600 mr-2"></i>
                Customer Stock
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
            
            @if($customer->customer_type === 'vip')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-crown mr-2"></i>VIP Customer
                </span>
            @elseif($customer->customer_type === 'wholesale')
                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-purple-100 text-purple-800">
                    <i class="fas fa-boxes mr-2"></i>Wholesale
                </span>
            @endif
        </div>
    </div>

    {{-- Stock List Placeholder --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-boxes text-4xl text-purple-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Stock Assigned</h3>
            <p class="text-gray-600 mb-4">This customer doesn't have any stock assigned yet.</p>
            <p class="text-sm text-gray-500">Stock information will appear here when inventory is allocated to this customer.</p>
        </div>
    </div>

</div>
@endsection