@extends('layouts.app')

@section('title', 'Pickup Request Details')
@section('header', 'Pickup Request Details')

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
    $statusClass = $statusColors[$pickup->status ?? 'pending'] ?? 'bg-gray-100 text-gray-800';
    $firstStop = $pickup->pickupStops->first();
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Tracking</p>
                <h3 class="text-2xl font-bold text-gray-900 font-mono">{{ $pickup->tracking_id ?? ('#' . $pickup->id) }}</h3>
                <p class="text-sm text-gray-500 mt-1">Created {{ $pickup->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <span class="inline-flex w-fit px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $pickup->status ?? 'pending')) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-900 mb-4">Route</h4>
            <div class="space-y-4">
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm text-gray-500">Pickup location</p>
                    <p class="font-semibold text-gray-900">{{ $pickup->pickup_address ?? optional($firstStop)->address ?? 'Not set' }}</p>
                </div>
                <div class="border-l-4 border-orange-500 pl-4">
                    <p class="text-sm text-gray-500">Destination</p>
                    <p class="font-semibold text-gray-900">{{ $pickup->destination_address ?? optional($pickup->warehouse)->name ?? 'Awaiting destination' }}</p>
                </div>
            </div>

            @if($pickup->pickupStops->count() > 0)
                <div class="mt-6">
                    <h4 class="font-bold text-gray-900 mb-3">Stops</h4>
                    <div class="space-y-3">
                        @foreach($pickup->pickupStops as $stop)
                            <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-gray-900">Stop {{ $stop->stop_number }}</p>
                                        <p class="text-gray-700 mt-1">{{ $stop->address }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ $stop->contact_name }} · {{ $stop->contact_phone }}</p>
                                    </div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-white text-gray-600 border">{{ ucfirst($stop->status ?? 'pending') }}</span>
                                </div>
                                @if($stop->items_description)
                                    <p class="text-sm text-gray-500 mt-3">{{ $stop->items_description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-900 mb-4">Summary</h4>
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">Driver</dt>
                    <dd class="font-semibold text-gray-900">{{ $pickup->driver->name ?? 'Awaiting assignment' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Distance</dt>
                    <dd class="font-semibold text-gray-900">{{ number_format((float) $pickup->total_distance, 1) }} km</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Amount</dt>
                    <dd class="font-semibold text-green-700 text-lg">रु {{ number_format((float) $pickup->total_price, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Payment</dt>
                    <dd class="font-semibold text-gray-900">{{ ucfirst($pickup->payment_status ?? 'pending') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <a href="{{ route('pickup.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
        <i class="fas fa-arrow-left mr-2"></i> Back to Pickups
    </a>
</div>
@endsection
