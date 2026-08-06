@extends('layouts.app')

@section('title', 'Reports')
@section('header', 'Analytics & Reports')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Warehouses</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalWarehouses ?? \App\Models\Warehouse::count() }}</p>
                </div>
                <i class="fas fa-warehouse text-3xl text-orange-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Approved Warehouses</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $approvedWarehouses ?? \App\Models\Warehouse::where('status', 'approved')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Vehicles</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalVehicles ?? \App\Models\Vehicle::count() }}</p>
                </div>
                <i class="fas fa-truck text-3xl text-blue-500"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalUsers ?? \App\Models\User::count() }}</p>
                </div>
                <i class="fas fa-users text-3xl text-purple-500"></i>
            </div>
        </div>
    </div>
    
    <!-- Monthly Stats Chart -->
    <div class="bg-white rounded-xl p-6 shadow-md">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Monthly Dispatch Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Month</th>
                        <th class="px-4 py-2 text-left">Orders Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyStats ?? [] as $stat)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $stat->month }}</td>
                        <td class="px-4 py-2">{{ $stat->count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-center text-gray-500">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Warehouses</h3>
            <ul class="space-y-2">
                @forelse($recentWarehouses ?? \App\Models\Warehouse::latest()->take(5)->get() as $warehouse)
                <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
                    <span>{{ $warehouse->name }}</span>
                    <span class="text-sm text-gray-500">{{ $warehouse->created_at->diffForHumans() }}</span>
                </li>
                @empty
                <li class="text-gray-500">No warehouses found</li>
                @endforelse
            </ul>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Orders</h3>
            <ul class="space-y-2">
                @forelse($recentOrders ?? \App\Models\DispatchOrder::latest()->take(5)->get() as $order)
                <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
                    <span>Order #{{ $order->id }}</span>
                    <span class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</span>
                </li>
                @empty
                <li class="text-gray-500">No orders found</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection