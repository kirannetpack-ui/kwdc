@extends('layouts.app')

@section('title', 'My Earnings')

@section('header', 'My Earnings')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <p class="text-sm opacity-90">Total Earnings</p>
            <p class="text-2xl font-bold">रु {{ number_format($totalEarnings ?? 0, 2) }}</p>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <p class="text-sm opacity-90">Completed Jobs</p>
            <p class="text-2xl font-bold">{{ $completedJobsCount ?? 0 }}</p>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <p class="text-sm opacity-90">Average per Job</p>
            <p class="text-2xl font-bold">रु {{ number_format($averageEarning ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Earnings History Table -->
    <h3 class="text-lg font-bold text-gray-800 mb-4">Earnings History</h3>
    
    @if(isset($transactions) && $transactions->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Location</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Distance</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Earnings</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivered On</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-mono">
                        {{ $transaction->request_number ?? '#' . $transaction->id }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ Str::limit($transaction->delivery_location ?? 'N/A', 30) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($transaction->distance_km)
                            {{ number_format($transaction->distance_km, 2) }} km
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-green-600">
                        रु {{ number_format($transaction->driver_earning ?? 0, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $transaction->delivered_at ? \Carbon\Carbon::parse($transaction->delivered_at)->format('M d, Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="status-badge status-approved">Delivered</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>
    @else
    <div class="text-center py-8 bg-gray-50 rounded-lg">
        <i class="fas fa-chart-line text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-500">No earnings yet</p>
        <p class="text-gray-400 text-sm mt-1">Complete deliveries to see your earnings here</p>
    </div>
    @endif
</div>
@endsection