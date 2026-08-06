@extends('layouts.app')

@section('title', 'Live Tracking')
@section('header', 'Live Shipment Tracking')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">
            @if(Auth::user()->role === 'admin')
                All Active & Historical Shipments
            @elseif(Auth::user()->role === 'client')
                My Shipments
            @elseif(Auth::user()->role === 'driver')
                My Assigned Jobs
            @endif
        </h3>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pickup Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Updated</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($dispatches as $dispatch)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900 text-sm">
                        {{ $dispatch->tracking_id ?? '#' . $dispatch->id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ optional($dispatch->client)->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ optional($dispatch->driver)->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ Str::limit($dispatch->pickup_address ?? 'N/A', 25) }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($dispatch->status == 'delivered') bg-green-100 text-green-800 
                            @elseif($dispatch->status == 'pending') bg-yellow-100 text-yellow-800 
                            @elseif($dispatch->status == 'cancelled') bg-red-100 text-red-800 
                            @else bg-blue-100 text-blue-800 @endif">
                            {{ ucfirst($dispatch->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $dispatch->last_location_update ? $dispatch->last_location_update->diffForHumans() : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <a href="{{ route('dispatch.show', $dispatch->id) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-map-marker-alt mr-1"></i> View Map
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-map-marked-alt text-4xl mb-3 text-gray-300"></i>
                        <p>No shipments found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $dispatches->links() }}
    </div>
</div>
@endsection