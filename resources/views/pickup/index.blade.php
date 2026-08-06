@extends('layouts.app')

@section('title', 'My Pickup Requests')
@section('header', 'My Pickup Requests')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">My Pickup Requests</h3>
        <a href="{{ route('pickup.direct-create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
            <i class="fas fa-plus mr-2"></i> New Pickup Request
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(isset($pickups) && $pickups->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stops</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Distance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pickups as $pickup)
                <tr>
                    <td class="px-6 py-4">#{{ $pickup->id }}</td>
                    <td class="px-6 py-4 font-mono text-sm">{{ $pickup->pickup_number }}</td>
                    <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($pickup->destination_address, 30) }}</td>
                    <td class="px-6 py-4">{{ $pickup->pickupStops->count() }}</td>
                    <td class="px-6 py-4">{{ $pickup->distance_km ?? 'N/A' }} km</td>
                    <td class="px-6 py-4">रु {{ number_format($pickup->base_price ?? 0) }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $pickup->status }}">
                            {{ ucfirst($pickup->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $pickup->driver->name ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('pickup.show', $pickup->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $pickups->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">No pickup requests found</p>
        <a href="{{ route('pickup.direct-create') }}" class="inline-block mt-4 text-orange-500 hover:text-orange-600">
            Create New Pickup Request →
        </a>
    </div>
    @endif
</div>

<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #d97706; }
    .status-assigned { background: #dbeafe; color: #2563eb; }
    .status-in_progress { background: #fed7aa; color: #ea580c; }
    .status-completed { background: #d1fae5; color: #059669; }
</style>
@endsection