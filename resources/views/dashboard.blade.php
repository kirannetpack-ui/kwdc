@extends('layouts.app')

@section('title', 'Logistics Overview')
@section('header', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Apple-style Hero Banner -->
    <div class="kwdc-dashboard-hero flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                    Operations Portal
                </span>
                <span class="text-xs text-gray-400 font-medium">KTM-WDC Logistics</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                Namaste, {{ Auth::user()->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1 max-w-xl">
                Unified logistics control center. Track real-time distribution across Kathmandu Valley hubs, pending dispatches, and warehouse operations.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="kwdc-beacon-container">
                <span class="kwdc-beacon-dot"></span>
                <span>Live System</span>
            </div>
            <a href="{{ route('tracking.index') }}" class="px-4 py-2 rounded-full bg-orange-50 hover:bg-orange-100 border border-orange-200 text-xs font-bold text-orange-700 transition flex items-center gap-1.5">
                <i class="fas fa-location-dot"></i> Live Fleet Tracking
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Orders</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $totalOrders ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-truck-fast"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Dispatches Managed</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pickups</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $totalPickups ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #ecfdf5; color: #059669;">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Scheduled Transfers</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Warehouses</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $totalWarehouses ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #fffbeb; color: #d97706;">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Storage Hubs</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Drivers</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $totalDrivers ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #fdf2f8; color: #db2777;">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Logistics Personnel</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>
    </div>

    <!-- Operational Feeds -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck text-orange-500"></i>
                    <h2 class="font-bold text-gray-900 text-base">Recent Logistics Orders</h2>
                </div>
                <span class="text-xs text-gray-400">Live Activity</span>
            </div>
            <div class="space-y-3">
                @forelse($recentOrders ?? [] as $order)
                <div class="kwdc-feed-item flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-gray-900 text-sm">{{ $order->dispatch_number ?? ('Order #' . $order->id) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ optional($order->created_at)->diffForHumans() }}</p>
                    </div>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        {{ ucfirst($order->status ?? 'pending') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">No recent orders found.</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Operations -->
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-bolt text-amber-500"></i>
                    <h2 class="font-bold text-gray-900 text-base">Fast Actions</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @if(Auth::user()->is_client || Auth::user()->role == 'client')
                <a href="{{ route('my-requests.create') }}" class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 hover:bg-blue-100/70 transition space-y-1">
                    <p class="font-bold text-blue-900 text-sm"><i class="fas fa-warehouse mr-1.5"></i> Request Storage</p>
                    <p class="text-xs text-blue-700">Book warehouse capacity</p>
                </a>
                <a href="{{ route('dispatch.direct-create') }}" class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 hover:bg-emerald-100/70 transition space-y-1">
                    <p class="font-bold text-emerald-900 text-sm"><i class="fas fa-truck mr-1.5"></i> Book Dispatch</p>
                    <p class="text-xs text-emerald-700">Dispatch cargo to destination</p>
                </a>
                <a href="{{ route('pickup.direct-create') }}" class="p-4 rounded-2xl bg-orange-50/70 border border-orange-100 hover:bg-orange-100/70 transition space-y-1">
                    <p class="font-bold text-orange-900 text-sm"><i class="fas fa-box-open mr-1.5"></i> Request Pickup</p>
                    <p class="text-xs text-orange-700">Schedule freight collection</p>
                </a>
                @endif
                @if(Auth::user()->is_property_owner || Auth::user()->role == 'property_owner')
                <a href="{{ route('warehouses.create') }}" class="p-4 rounded-2xl bg-purple-50/70 border border-purple-100 hover:bg-purple-100/70 transition space-y-1">
                    <p class="font-bold text-purple-900 text-sm"><i class="fas fa-plus-circle mr-1.5"></i> List Property</p>
                    <p class="text-xs text-purple-700">Register new facility</p>
                </a>
                @endif
                @if(Auth::user()->is_driver || Auth::user()->role == 'driver')
                <a href="{{ route('driver.available-jobs') }}" class="p-4 rounded-2xl bg-amber-50/70 border border-amber-100 hover:bg-amber-100/70 transition space-y-1">
                    <p class="font-bold text-amber-900 text-sm"><i class="fas fa-route mr-1.5"></i> Available Deliveries</p>
                    <p class="text-xs text-amber-700">Browse delivery jobs</p>
                </a>
                @endif
                @if(Auth::user()->is_equipment_owner || Auth::user()->role == 'equipment_owner')
                <a href="{{ route('equipment.register') }}" class="p-4 rounded-2xl bg-teal-50/70 border border-teal-100 hover:bg-teal-100/70 transition space-y-1">
                    <p class="font-bold text-teal-900 text-sm"><i class="fas fa-gear mr-1.5"></i> Add Equipment</p>
                    <p class="text-xs text-teal-700">Register machinery</p>
                </a>
                @endif
                @if(Auth::user()->is_admin || Auth::user()->role == 'admin')
                <a href="{{ route('admin.pending') }}" class="p-4 rounded-2xl bg-red-50/70 border border-red-100 hover:bg-red-100/70 transition space-y-1">
                    <p class="font-bold text-red-900 text-sm"><i class="fas fa-clock mr-1.5"></i> Review Queue</p>
                    <p class="text-xs text-red-700">Pending authorizations</p>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
