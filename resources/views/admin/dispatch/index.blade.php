@extends('layouts.app')

@section('title', 'Dispatch Orders')
@section('header', 'Dispatch Orders')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">All Dispatch Orders</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders ?? [] as $order)
                <tr>
                    <td class="px-6 py-4">#{{ $order->id }}</td>
                    <td class="px-6 py-4">{{ $order->warehouseRequest->client->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $order->driver->name ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status ?? 'Pending') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('dispatch.show', $order->id) }}" aria-label="View dispatch #{{ $order->id }}"><i class="fas fa-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
