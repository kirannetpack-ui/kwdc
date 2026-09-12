@extends('layouts.app')

@section('title', 'My Pickup Requests')
@section('header', 'My Pickup Requests')

@section('content')
@php
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-800',
        'assigned' => 'bg-blue-100 text-blue-800',
        'picked_up' => 'bg-purple-100 text-purple-800',
        'on_the_way' => 'bg-orange-100 text-orange-800',
        'in_progress' => 'bg-orange-100 text-orange-800',
        'delivered' => 'bg-green-100 text-green-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Pickup Requests</h3>
                <p class="text-sm text-gray-500 mt-1">Book pickups from shops, homes, suppliers, and market hubs across Nepal.</p>
            </div>
            <a href="{{ route('pickup.direct-create') }}" class="inline-flex items-center justify-center bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-plus mr-2"></i> New Pickup
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(isset($pickups) && $pickups->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Tracking</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Pickup</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Destination</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Stops</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Amount</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left font-semibold uppercase tracking-wide">Driver</th>
                            <th class="px-5 py-3 text-right font-semibold uppercase tracking-wide">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pickups as $pickup)
                            @php
                                $firstStop = $pickup->pickupStops->first();
                                $pickupAddress = $pickup->pickup_address ?? optional($firstStop)->address ?? 'Pickup location not set';
                                $destination = $pickup->destination_address
                                    ?? optional($pickup->warehouse)->name
                                    ?? optional($pickup->warehouse)->location
                                    ?? 'Destination not set';
                                $statusClass = $statusColors[$pickup->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <tr class="hover:bg-orange-50/40">
                                <td class="px-5 py-4">
                                    <div class="font-mono font-semibold text-gray-900">{{ $pickup->tracking_id ?? ('#' . $pickup->id) }}</div>
                                    <div class="text-xs text-gray-500">{{ $pickup->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="px-5 py-4 min-w-64">
                                    <div class="font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($pickupAddress, 48) }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ number_format((float) $pickup->total_distance, 1) }} km route</div>
                                </td>
                                <td class="px-5 py-4 min-w-56 text-gray-700">{{ \Illuminate\Support\Str::limit($destination, 48) }}</td>
                                <td class="px-5 py-4 text-gray-700">{{ $pickup->pickupStops->count() }}</td>
                                <td class="px-5 py-4 font-semibold text-green-700">रु {{ number_format((float) $pickup->total_price, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $pickup->status ?? 'pending')) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ $pickup->driver->name ?? 'Awaiting assignment' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('pickup.show', $pickup->id) }}" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-semibold">
                                        View <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $pickups->links() }}
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 text-center py-14 px-6">
            <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-box-open text-3xl text-orange-500"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900">No pickup requests yet</h3>
            <p class="text-gray-500 mt-2">Start with a pickup from Kalimati, New Road, Patan, Bhaktapur, or your supplier location.</p>
            <a href="{{ route('pickup.direct-create') }}" class="inline-flex items-center mt-5 bg-orange-500 text-white px-5 py-2 rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-plus mr-2"></i> Create Pickup
            </a>
        </div>
    @endif
</div>
@endsection
