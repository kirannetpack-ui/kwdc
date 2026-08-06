@extends('layouts.app')

@section('title', 'Client Dashboard')
@section('header', 'Client Dashboard')

@section('content')
<div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg p-4 mb-6 border-l-4 border-orange-500">
    <div class="flex items-center">
        <div class="text-3xl mr-3">🙏</div>
        <div>
            <p class="text-gray-800">
                <span class="font-bold">Namaste Client</span>,
                <span class="text-orange-600 font-bold ml-1">{{ Auth::user()->name }}</span>
            </p>
            <p class="text-sm text-gray-500">Welcome to your KTM-WDC client portal</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Requests</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_requests'] ?? 0 }}</p>
            </div>
            <i class="fas fa-clipboard-list text-3xl text-blue-500"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Active Requests</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['active_requests'] ?? 0 }}</p>
            </div>
            <i class="fas fa-clock text-3xl text-orange-500"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_orders'] ?? 0 }}</p>
            </div>
            <i class="fas fa-truck text-3xl text-green-500"></i>
        </div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Spent</p>
                <p class="text-3xl font-bold text-gray-800">रु {{ number_format($stats['total_spent'] ?? 0) }}</p>
            </div>
            <i class="fas fa-rupee-sign text-3xl text-green-500"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Requests</h3>
        <div class="space-y-3">
            @forelse($recentRequests ?? [] as $request)
            <div class="border rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold">Request #{{ $request->id }}</p>
                        <p class="text-sm text-gray-600">{{ $request->warehouse->name ?? 'N/A' }}</p>
                    </div>
                    <span class="status-badge status-{{ $request->status }}">{{ ucfirst($request->status) }}</span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center">No requests yet</p>
            @endforelse
        </div>
        <a href="{{ route('my-requests.index') }}" class="mt-4 inline-block text-orange-500">View All →</a>
    </div>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Active Orders</h3>
        <div class="space-y-3">
            @forelse($activeOrders ?? [] as $order)
            <div class="border rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-semibold">Order #{{ $order->id }}</p>
                        <p class="text-sm text-gray-600">Driver: {{ $order->driver->name ?? 'N/A' }}</p>
                    </div>
                    <a href="{{ route('tracking.shipment', $order->id) }}" class="text-orange-500">Track →</a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center">No active orders</p>
            @endforelse
        </div>
    </div>
</div>
@endsection