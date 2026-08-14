@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@include('components.birthday-wish')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Orders -->
    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalOrders ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pickups -->
    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pickups</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalPickups ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box-open text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Warehouses -->
    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Warehouses</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalWarehouses ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-warehouse text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Drivers -->
    <div class="bg-white rounded-xl shadow-md p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Drivers</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalDrivers ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-truck text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📦 Recent Orders</h3>
        @if(isset($recentOrders) && count($recentOrders) > 0)
            @foreach($recentOrders as $order)
            <div class="border-b border-gray-100 py-3 last:border-0">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $order->dispatch_number ?? 'Order' }}</p>
                        <p class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="status-badge status-{{ $order->status ?? 'pending' }}">
                        {{ ucfirst($order->status ?? 'Pending') }}
                    </span>
                </div>
            </div>
            @endforeach
        @else
            <p class="text-gray-500">No recent orders.</p>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">⚡ Quick Actions</h3>
        <div class="grid grid-cols-1 gap-3">
            @if(Auth::user()->is_client || Auth::user()->role == 'client')
            <a href="{{ route('my-requests.create') }}" class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i> New Warehouse Request
            </a>
            <a href="{{ route('dispatch.direct-create') }}" class="block p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-truck text-green-600 mr-2"></i> Create Dispatch Order
            </a>
            <a href="{{ route('pickup.direct-create') }}" class="block p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
                <i class="fas fa-box-open text-orange-600 mr-2"></i> Create Pickup Request
            </a>
            @endif
            @if(Auth::user()->is_property_owner || Auth::user()->role == 'property_owner')
            <a href="{{ route('warehouses.create') }}" class="block p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                <i class="fas fa-plus-circle text-purple-600 mr-2"></i> Register Warehouse
            </a>
            @endif
            @if(Auth::user()->is_driver || Auth::user()->role == 'driver')
            <a href="{{ route('driver.available-jobs') }}" class="block p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                <i class="fas fa-search text-yellow-600 mr-2"></i> Find Available Jobs
            </a>
            @endif
            @if(Auth::user()->is_equipment_owner || Auth::user()->role == 'equipment_owner')
            <a href="{{ route('equipment.register') }}" class="block p-3 bg-teal-50 rounded-lg hover:bg-teal-100 transition">
                <i class="fas fa-plus-circle text-teal-600 mr-2"></i> Register Equipment
            </a>
            @endif
            @if(Auth::user()->is_admin || Auth::user()->role == 'admin')
            <a href="{{ route('admin.pending') }}" class="block p-3 bg-red-50 rounded-lg hover:bg-red-100 transition">
                <i class="fas fa-clock text-red-600 mr-2"></i> Pending Approvals
            </a>
            @endif
        </div>
    </div>
</div>

<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #d1fae5; color: #059669; }
    .status-rejected { background: #fee2e2; color: #dc2626; }
    .status-assigned { background: #dbeafe; color: #2563eb; }
    .status-delivered { background: #d1fae5; color: #059669; }
    .status-on_the_way { background: #fed7aa; color: #ea580c; }
</style>
@endsection