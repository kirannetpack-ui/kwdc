@extends('layouts.app')

@section('title', 'Stock Management')
@section('header', 'Stock Management')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">All Stock Items</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($stocks ?? [] as $stock)
                <tr>
                    <td class="px-6 py-4">{{ $stock->product_name }}</td>
                    <td class="px-6 py-4">{{ $stock->quantity }}</td>
                    <td class="px-6 py-4">{{ $stock->warehouse->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $stock->status }}">{{ ucfirst($stock->status ?? 'Pending') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <button class="text-green-500 hover:text-green-700"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No stocks found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection