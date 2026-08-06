@extends('layouts.app')

@section('title', 'My Dispatch Orders')
@section('header', 'My Dispatch Orders')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
    <h3 class="text-lg font-bold text-gray-800">
        <i class="fas fa-truck text-orange-500 mr-2"></i> My Dispatch Orders
    </h3>
    <a href="{{ route('dispatch.direct-create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
        <i class="fas fa-plus mr-2"></i> Create New Dispatch
    </a>
</div>


    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @php
        $dispatches = \App\Models\DispatchOrder::where('client_id', auth()->id())
            ->with(['driver', 'warehouseRequest'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    @endphp

    @if($dispatches->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispatch ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Distance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($dispatches as $dispatch)
                <tr>
                    <td class="px-6 py-4 font-mono text-sm">#{{ $dispatch->id }}</td>
                    <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($dispatch->pickup_address ?? 'N/A', 30) }}</td>
                    <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($dispatch->delivery_address ?? ($dispatch->deliveryStops->first()->address ?? 'N/A'), 30) }}</td>
                    <td class="px-6 py-4">{{ $dispatch->distance_km ?? 'N/A' }} km</td>
                    <td class="px-6 py-4 font-semibold text-green-600">रु {{ number_format($dispatch->base_price ?? 0) }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'assigned' => 'bg-blue-100 text-blue-800',
                                'picked_up' => 'bg-purple-100 text-purple-800',
                                'on_the_way' => 'bg-orange-100 text-orange-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusColor = $statusColors[$dispatch->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $dispatch->status ?? 'Pending')) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $dispatch->driver->name ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $dispatch->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('dispatch.show', $dispatch->id) }}" class="text-blue-500 hover:text-blue-700" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($dispatch->status == 'assigned')
                        <a href="{{ route('tracking.shipment', $dispatch->id) }}" class="text-orange-500 hover:text-orange-700 ml-2" title="Track">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $dispatches->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-truck text-4xl text-gray-400"></i>
        </div>
        <p class="text-gray-500 text-lg">No dispatch orders found</p>
        <p class="text-gray-400 mt-2">Create a warehouse request, get it approved, then create a dispatch order.</p>
        <div class="mt-6 space-x-3">
            <a href="{{ route('my-requests.index') }}" class="inline-block bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                <i class="fas fa-warehouse mr-2"></i> Go to Warehouse Requests
            </a>
        </div>
    </div>
    @endif
</div>
@endsection