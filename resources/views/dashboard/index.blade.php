@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-[#1e293b] to-[#0f172a] rounded-xl shadow-md p-6 text-white mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="opacity-75">
                    <i class="fas fa-user-tag me-2"></i>Role: {{ ucfirst($role ?? Auth::user()->role) }}
                </p>
            </div>
            <div class="text-right">
                <i class="fas fa-calendar-day text-3xl opacity-75 mb-1"></i>
                <div class="opacity-75">{{ now()->format('l, F j, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ADMIN DASHBOARD -->
    <!-- ============================================================ -->
    @if(isset($role) && $role === 'admin')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Warehouses</p>
                        <p class="text-2xl font-bold text-gray-700">{{ $stats['warehouses'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-warehouse text-blue-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Users</p>
                        <p class="text-2xl font-bold text-gray-700">{{ ($stats['clients'] ?? 0) + ($stats['drivers'] ?? 0) + ($stats['property_owners'] ?? 0) + ($stats['equipment_owners'] ?? 0) }}</p>
                    </div>
                    <i class="fas fa-users text-green-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Dispatches</p>
                        <p class="text-2xl font-bold text-gray-700">{{ $stats['total_dispatches'] ?? 0 }}</p>
                    </div>
                    <i class="fas fa-truck text-orange-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Revenue</p>
                        <p class="text-2xl font-bold text-gray-700">रू {{ number_format($stats['total_revenue'] ?? 0) }}</p>
                    </div>
                    <i class="fas fa-wallet text-red-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Recent Warehouses -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-warehouse text-blue-500 mr-2"></i>Recent Warehouses</h4>
                    <a href="{{ route('admin.all-warehouses') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Location</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Added</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentWarehouses ?? [] as $warehouse)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $warehouse->name }}</td>
                                <td class="px-4 py-2">{{ $warehouse->city ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($warehouse->status == 'approved') bg-green-100 text-green-800 
                                        @elseif($warehouse->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($warehouse->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $warehouse->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No warehouses found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Dispatches -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-truck text-orange-500 mr-2"></i>Recent Dispatches</h4>
                    <a href="{{ route('admin.dispatch') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Client</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentDispatches ?? [] as $dispatch)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $dispatch->id }}</td>
                                <td class="px-4 py-2">{{ optional($dispatch->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($dispatch->status == 'delivered') bg-green-100 text-green-800 
                                        @elseif($dispatch->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($dispatch->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($dispatch->base_price ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No dispatches found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h4 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-purple-500 mr-2"></i>Recent Warehouse Requests</h4>
                <a href="{{ route('admin.requests') }}" class="text-xs text-blue-500 hover:underline">View All</a>
            </div>
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Client</th><th class="px-4 py-2">Warehouse</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Created</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentRequests ?? [] as $request)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900">#{{ $request->id }}</td>
                            <td class="px-4 py-2">{{ optional($request->client)->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ optional($request->warehouse)->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($request->status == 'approved') bg-green-100 text-green-800 
                                    @elseif($request->status == 'pending') bg-yellow-100 text-yellow-800 
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $request->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">No requests found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================ -->
    <!-- CLIENT DASHBOARD -->
    <!-- ============================================================ -->
    @if(isset($role) && $role === 'client')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Active Requests</p><p class="text-2xl font-bold text-gray-700">{{ $stats['active_requests'] ?? 0 }}</p></div>
                    <i class="fas fa-clipboard-list text-blue-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Completed Dispatches</p><p class="text-2xl font-bold text-gray-700">{{ $stats['completed_dispatches'] ?? 0 }}</p></div>
                    <i class="fas fa-check-circle text-green-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Stock Items</p><p class="text-2xl font-bold text-gray-700">{{ $stats['stock_items'] ?? 0 }}</p></div>
                    <i class="fas fa-boxes text-orange-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Total Spent</p><p class="text-2xl font-bold text-gray-700">रू {{ number_format($stats['total_spent'] ?? 0) }}</p></div>
                    <i class="fas fa-wallet text-red-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Recent Requests -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-purple-500 mr-2"></i>Recent Requests</h4>
                    <a href="{{ route('my-requests.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Created</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentRequests ?? [] as $request)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $request->id }}</td>
                                <td class="px-4 py-2">{{ $request->type ?? 'Warehouse' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($request->status == 'approved') bg-green-100 text-green-800 
                                        @elseif($request->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $request->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No requests found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Dispatches -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-truck text-orange-500 mr-2"></i>Recent Dispatches</h4>
                    <a href="{{ route('dispatch.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Driver</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentDispatches ?? [] as $dispatch)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $dispatch->id }}</td>
                                <td class="px-4 py-2">{{ optional($dispatch->driver)->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($dispatch->status == 'delivered') bg-green-100 text-green-800 
                                        @elseif($dispatch->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($dispatch->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($dispatch->base_price ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No dispatches found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Recent Pickups -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-box-open text-green-500 mr-2"></i>Recent Pickups</h4>
                    <a href="{{ route('pickup.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Pickup Address</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Created</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentPickups ?? [] as $pickup)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $pickup->id }}</td>
                                <td class="px-4 py-2">{{ Str::limit($pickup->pickup_address ?? 'N/A', 30) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($pickup->status == 'completed') bg-green-100 text-green-800 
                                        @elseif($pickup->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($pickup->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $pickup->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No pickups found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-file-invoice text-red-500 mr-2"></i>Recent Invoices</h4>
                    <a href="{{ route('invoices.client-index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Invoice #</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Date</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentInvoices ?? [] as $invoice)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $invoice->invoice_number ?? '#' . $invoice->id }}</td>
                                <td class="px-4 py-2">रू {{ number_format($invoice->total_amount ?? 0) }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($invoice->status == 'paid') bg-green-100 text-green-800 
                                        @elseif($invoice->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $invoice->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No invoices found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Available Warehouses -->
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-warehouse text-blue-500 mr-2"></i>Available Warehouses</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($availableWarehouses ?? [] as $warehouse)
                <div class="border rounded p-3 hover:bg-gray-50 transition">
                    <div class="font-medium text-gray-800">{{ $warehouse->name }}</div>
                    <div class="text-sm text-gray-500">{{ $warehouse->city ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $warehouse->area_sqft ?? 0 }} sq ft | रू {{ number_format($warehouse->price_per_sqft ?? 0, 2) }}/sqft</div>
                    <a href="{{ route('my-requests.create', ['warehouse' => $warehouse->id]) }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Request This</a>
                </div>
                @empty <div class="col-span-3 text-center text-gray-500 py-4">No warehouses available</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Stock -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h4 class="font-bold text-gray-800"><i class="fas fa-boxes text-orange-500 mr-2"></i>Recent Stock Items</h4>
                <a href="{{ route('stock.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
            </div>
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-2">Item Name</th><th class="px-4 py-2">Quantity</th><th class="px-4 py-2">Location</th><th class="px-4 py-2">Added</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentStocks ?? [] as $stock)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $stock->item_name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $stock->quantity ?? 0 }}</td>
                            <td class="px-4 py-2">{{ $stock->location ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $stock->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No stock items found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================ -->
    <!-- DRIVER DASHBOARD -->
    <!-- ============================================================ -->
    @if(isset($role) && $role === 'driver')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Available Jobs</p><p class="text-2xl font-bold text-gray-700">{{ $stats['available_jobs'] ?? 0 }}</p></div>
                    <i class="fas fa-clipboard-list text-blue-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Active Jobs</p><p class="text-2xl font-bold text-gray-700">{{ $stats['active_jobs'] ?? 0 }}</p></div>
                    <i class="fas fa-spinner text-orange-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Completed Jobs</p><p class="text-2xl font-bold text-gray-700">{{ $stats['completed_jobs'] ?? 0 }}</p></div>
                    <i class="fas fa-check-circle text-green-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Total Earnings</p><p class="text-2xl font-bold text-gray-700">रू {{ number_format($stats['total_earnings'] ?? 0) }}</p></div>
                    <i class="fas fa-wallet text-red-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Vehicles</p><p class="text-2xl font-bold text-gray-700">{{ $stats['vehicles'] ?? 0 }}</p></div>
                    <i class="fas fa-truck text-purple-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Rating</p><p class="text-2xl font-bold text-gray-700">{{ number_format($stats['rating'] ?? 0, 1) }} <i class="fas fa-star text-yellow-400 text-sm"></i></p></div>
                    <i class="fas fa-star text-yellow-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-cyan-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Total Distance</p><p class="text-2xl font-bold text-gray-700">{{ number_format($stats['total_distance'] ?? 0) }} km</p></div>
                    <i class="fas fa-road text-cyan-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Active Jobs -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-spinner text-orange-500 mr-2"></i>Active Jobs</h4>
                    <a href="{{ route('driver.jobs') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Job ID</th><th class="px-4 py-2">Client</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($activeJobs ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $job->id }}</td>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($job->status) }}</span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($job->driver_earning ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No active jobs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Available Jobs -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-blue-500 mr-2"></i>Available Jobs</h4>
                    <a href="{{ route('driver.available-jobs') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Job ID</th><th class="px-4 py-2">Client</th><th class="px-4 py-2">Pickup Address</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($availableJobs ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $job->id }}</td>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ Str::limit($job->pickup_address ?? 'N/A', 20) }}</td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($job->base_price ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No available jobs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Completed Jobs -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-check-circle text-green-500 mr-2"></i>Completed Jobs</h4>
                    <a href="{{ route('driver.jobs') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Job ID</th><th class="px-4 py-2">Client</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Completed At</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($completedJobs ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $job->id }}</td>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($job->driver_earning ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $job->delivered_at ? $job->delivered_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No completed jobs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Earnings -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-wallet text-orange-500 mr-2"></i>Recent Earnings</h4>
                    <a href="{{ route('driver.earnings') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Job ID</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Date</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentEarnings ?? [] as $earning)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">#{{ $earning->id }}</td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($earning->amount ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $earning->created_at ? $earning->created_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            @empty <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">No earnings yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Weekly Earnings Chart (If data exists) -->
        @if(isset($weeklyEarnings) && count($weeklyEarnings) > 0)
        <div class="bg-white rounded-xl shadow p-4 mb-6">
            <h4 class="font-bold text-gray-800 mb-3"><i class="fas fa-chart-bar text-purple-500 mr-2"></i>Weekly Earnings</h4>
            <div class="flex justify-between items-end h-32 space-x-2">
                @foreach($weeklyEarnings as $day)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-blue-100 rounded-t relative" style="height: {{ $day['amount'] > 0 ? max(10, ($day['amount'] / (max(array_column($weeklyEarnings, 'amount')) ?: 1)) * 100) : 5 }}%">
                        <div class="absolute -top-6 w-full text-center text-xs font-medium text-blue-800">रू {{ number_format($day['amount'], 0) }}</div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">{{ $day['day'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif

    <!-- ============================================================ -->
    <!-- PROPERTY OWNER DASHBOARD -->
    <!-- ============================================================ -->
    @if(isset($role) && $role === 'property_owner')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">My Properties</p><p class="text-2xl font-bold text-gray-700">{{ $stats['my_properties'] ?? 0 }}</p></div>
                    <i class="fas fa-warehouse text-blue-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Approved</p><p class="text-2xl font-bold text-gray-700">{{ $stats['approved_properties'] ?? 0 }}</p></div>
                    <i class="fas fa-check-circle text-green-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Pending</p><p class="text-2xl font-bold text-gray-700">{{ $stats['pending_properties'] ?? 0 }}</p></div>
                    <i class="fas fa-clock text-orange-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Total Revenue</p><p class="text-2xl font-bold text-gray-700">रू {{ number_format($stats['total_revenue'] ?? 0) }}</p></div>
                    <i class="fas fa-rupee-sign text-red-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- My Properties -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h4 class="font-bold text-gray-800"><i class="fas fa-warehouse text-blue-500 mr-2"></i>My Properties</h4>
                <a href="{{ route('warehouses.create') }}" class="text-xs bg-orange-500 text-white px-3 py-1 rounded hover:bg-orange-600 transition">+ Add</a>
            </div>
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Location</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($myWarehouses ?? [] as $warehouse)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $warehouse->name }}</td>
                            <td class="px-4 py-2">{{ $warehouse->city ?? 'N/A' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($warehouse->status == 'approved') bg-green-100 text-green-800 
                                    @elseif($warehouse->status == 'pending') bg-yellow-100 text-yellow-800 
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($warehouse->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                <a href="{{ route('property.warehouses.show', $warehouse->id) }}" class="text-blue-600 hover:underline mr-2">View</a>
                                <a href="{{ route('property.warehouses.edit', $warehouse->id) }}" class="text-indigo-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                        @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No properties found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Requests & Dispatches -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Recent Requests -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-purple-500 mr-2"></i>Recent Requests</h4>
                    <a href="{{ route('property.requests.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Client</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Date</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentRequests ?? [] as $request)
                            <tr>
                                <td class="px-4 py-2">{{ optional($request->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($request->status == 'approved') bg-green-100 text-green-800 
                                        @elseif($request->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $request->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">No recent requests</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Dispatches -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-truck text-orange-500 mr-2"></i>Recent Dispatches</h4>
                    <a href="{{ route('admin.dispatch') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Client</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentDispatches ?? [] as $dispatch)
                            <tr>
                                <td class="px-4 py-2">{{ optional($dispatch->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($dispatch->status == 'delivered') bg-green-100 text-green-800 
                                        @elseif($dispatch->status == 'pending') bg-yellow-100 text-yellow-800 
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($dispatch->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($dispatch->base_price ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">No recent dispatches</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ============================================================ -->
    <!-- EQUIPMENT OWNER DASHBOARD -->
    <!-- ============================================================ -->
    @if(isset($role) && $role === 'equipment_owner')
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">My Equipment</p><p class="text-2xl font-bold text-gray-700">{{ $stats['my_equipment'] ?? 0 }}</p></div>
                    <i class="fas fa-tools text-blue-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Available</p><p class="text-2xl font-bold text-gray-700">{{ $stats['available_equipment'] ?? 0 }}</p></div>
                    <i class="fas fa-check-circle text-green-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Job Requests</p><p class="text-2xl font-bold text-gray-700">{{ $stats['job_requests'] ?? 0 }}</p></div>
                    <i class="fas fa-clipboard-list text-orange-400 text-2xl opacity-50"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-red-500">
                <div class="flex justify-between items-center">
                    <div><p class="text-xs text-gray-500 uppercase tracking-wide">Total Earnings</p><p class="text-2xl font-bold text-gray-700">रू {{ number_format($stats['total_earnings'] ?? 0) }}</p></div>
                    <i class="fas fa-wallet text-red-400 text-2xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- My Equipment -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-tools text-blue-500 mr-2"></i>My Equipment</h4>
                    <a href="{{ route('equipment.list') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Rate</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($myEquipment ?? [] as $equip)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $equip->name }}</td>
                                <td class="px-4 py-2">{{ $equip->type ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($equip->status == 'available') bg-green-100 text-green-800 
                                        @elseif($equip->status == 'rented') bg-yellow-100 text-yellow-800 
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($equip->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($equip->rate ?? 0, 2) }}/day</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No equipment found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Job Requests -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-clipboard-list text-orange-500 mr-2"></i>Job Requests</h4>
                    <a href="{{ route('equipment.jobs.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Client</th><th class="px-4 py-2">Equipment</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Created</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($jobRequests ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ optional($job->equipment)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                                <td class="px-4 py-2 text-gray-500">{{ $job->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No job requests</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Active Jobs -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-spinner text-blue-500 mr-2"></i>Active Jobs</h4>
                    <a href="{{ route('equipment.jobs.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Client</th><th class="px-4 py-2">Equipment</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($activeJobs ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ optional($job->equipment)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($job->status) }}</span>
                                </td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($job->price ?? 0, 2) }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No active jobs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Completed Jobs -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h4 class="font-bold text-gray-800"><i class="fas fa-check-circle text-green-500 mr-2"></i>Completed Jobs</h4>
                    <a href="{{ route('equipment.jobs.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="px-4 py-2">Client</th><th class="px-4 py-2">Equipment</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Completed</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($completedJobs ?? [] as $job)
                            <tr>
                                <td class="px-4 py-2">{{ optional($job->client)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ optional($job->equipment)->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($job->price ?? 0, 2) }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $job->completed_at ? $job->completed_at->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            @empty <tr><td colspan="4" class="px-4 py-4 text-center text-gray-500">No completed jobs</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Earnings -->
        <div class="bg-white rounded-xl shadow overflow-hidden mb-6">
            <div class="px-4 py-3 border-b flex justify-between items-center">
                <h4 class="font-bold text-gray-800"><i class="fas fa-wallet text-green-500 mr-2"></i>Recent Earnings</h4>
                
                {{-- ✅ STRICT ROLE CHECK FIX (Prevents Blank Screen!) --}}
                <div>
                    @if(Auth::user()->role === 'equipment_owner')
                        <a href="{{ route('equipment.jobs.earnings') }}" class="btn btn-sm btn-primary">View Earnings</a>
                    @elseif(Auth::user()->role === 'driver')
                        <a href="{{ route('driver.earnings') }}" class="btn btn-sm btn-primary">View Earnings</a>
                    @endif
                </div>

            </div>
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-2">Job ID</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Date</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentEarnings ?? [] as $earning)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-900">#{{ $earning->id }}</td>
                            <td class="px-4 py-2 font-medium text-orange-600">रू {{ number_format($earning->amount ?? 0, 2) }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $earning->created_at ? $earning->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                        @empty <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">No earnings yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ============================================================ -->
    <!-- NOTIFICATIONS SECTION (Common for all Roles) -->
    <!-- ============================================================ -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="bg-white rounded-xl shadow p-4 mt-6">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-bold text-gray-800"><i class="fas fa-bell mr-2 text-orange-500"></i>Recent Notifications</h4>
            <a href="{{ route('notifications.index') }}" class="text-xs text-blue-500 hover:underline">View All</a>
        </div>
        <div class="space-y-2">
            @foreach($notifications as $notification)
                <div class="flex justify-between items-center border-b pb-2 last:border-0">
                    <div>
                        <p class="text-sm font-medium">{{ $notification->title }}</p>
                        <p class="text-xs text-gray-500">{{ $notification->message }}</p>
                    </div>
                    <small class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection