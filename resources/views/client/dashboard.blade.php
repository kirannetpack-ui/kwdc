@extends('layouts.app')

@section('title', 'Client Dashboard')
@section('header', 'Client Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-600 text-sm mb-1">Namaste, {{ Auth::user()->name }} <span class="text-orange-600 text-sm">(Client)</span></p>
            <h2 class="text-3xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name }}</h2>
            <p class="text-gray-500 mt-1">Here's what's happening with your logistics today.</p>
        </div>
        <div class="text-right">
            <p class="text-gray-600 text-sm">System Online</p>
            <p class="text-xs text-gray-400 mt-1">{{ now()->format('F j, Y') }}</p>
        </div>
    </div>
</div>

<!-- Quick Action Buttons -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('my-requests.create') }}" class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-600 font-semibold text-sm">New Request</p>
                <p class="text-gray-500 text-xs">Start a new request</p>
            </div>
            <i class="fas fa-plus text-blue-500 text-2xl"></i>
        </div>
    </a>

    <a href="{{ route('dispatch.direct-create') }}" class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-600 font-semibold text-sm">Create Dispatch</p>
                <p class="text-gray-500 text-xs">New dispatch order</p>
            </div>
            <i class="fas fa-truck text-green-500 text-2xl"></i>
        </div>
    </a>

    <a href="{{ route('pickup.create') }}" class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-600 font-semibold text-sm">Request Pickup</p>
                <p class="text-gray-500 text-xs">Schedule pickup</p>
            </div>
            <i class="fas fa-box text-orange-500 text-2xl"></i>
        </div>
    </a>

    <a href="{{ route('client.reports') }}" class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-600 font-semibold text-sm">View Reports</p>
                <p class="text-gray-500 text-xs">Analytics & reports</p>
            </div>
            <i class="fas fa-chart-bar text-purple-500 text-2xl"></i>
        </div>
    </a>
</div>

<!-- Metrics Grid - First Row (4 columns) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Active Requests Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">ACTIVE REQUESTS</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['active_requests'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-clipboard-list text-2xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <!-- Dispatches Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">DISPATCHES</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['total_orders'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-truck text-2xl text-blue-500"></i>
            </div>
        </div>
    </div>

    <!-- Pending Dispatches Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">PENDING DISPATCHES</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['pending_dispatches'] ?? 0 }}</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-lg">
                <i class="fas fa-hourglass-half text-2xl text-orange-500"></i>
            </div>
        </div>
    </div>

    <!-- Completed Dispatches Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">COMPLETED DISPATCHES</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['completed_dispatches'] ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-lg">
                <i class="fas fa-check-circle text-2xl text-green-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Grid - Second Row (4 columns) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Pickups Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">PICKUPS</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['pickups'] ?? 0 }}</p>
            </div>
            <div class="bg-teal-100 p-3 rounded-lg">
                <i class="fas fa-box-open text-2xl text-teal-500"></i>
            </div>
        </div>
    </div>

    <!-- Stock Items Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">STOCK ITEMS</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['stock_items'] ?? 0 }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-lg">
                <i class="fas fa-cube text-2xl text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- Pending Invoices Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">PENDING INVOICES</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['pending_invoices'] ?? 0 }}</p>
            </div>
            <div class="bg-red-100 p-3 rounded-lg">
                <i class="fas fa-file-invoice text-2xl text-red-500"></i>
            </div>
        </div>
    </div>

    <!-- Total Spent Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-start justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">TOTAL SPENT</p>
                <p class="text-4xl font-bold text-gray-800 mt-2">{{ $stats['total_spent'] ?? 0 }}</p>
            </div>
            <div class="bg-pink-100 p-3 rounded-lg">
                <i class="fas fa-wallet text-2xl text-pink-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Sections -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Recent Requests -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Requests</h3>
            <a href="{{ route('my-requests.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentRequests ?? [] as $request)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Request #{{ $request->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ ucfirst($request->status) }}</span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No requests found</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Dispatches -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Dispatches</h3>
            <a href="{{ route('dispatch.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($activeOrders ?? [] as $order)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Dispatch #{{ $order->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">Driver: {{ $order->driver->name ?? 'Unassigned' }}</p>
                    </div>
                    <a href="{{ route('tracking.shipment', $order->id) }}" class="text-orange-500 text-sm hover:text-orange-600">Track →</a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No dispatches found</p>
            @endforelse
        </div>
    </div>
</div>
@endsection