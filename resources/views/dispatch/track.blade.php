@extends('layouts.app')

@section('title', 'Track Dispatch')
@section('header', 'Live Dispatch Tracking')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Dispatch #{{ $dispatch->id }}</h3>
            <p class="text-gray-500">Tracking ID: {{ $dispatch->tracking_id ?? 'N/A' }}</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
            {{ ucfirst(str_replace('_', ' ', $dispatch->status ?? 'pending')) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="font-semibold text-gray-700">Driver</p>
            <p class="text-gray-800">{{ $dispatch->driver?->name ?? 'Not assigned yet' }}</p>
        </div>
        <div>
            <p class="font-semibold text-gray-700">Last Location</p>
            @if($dispatch->current_latitude && $dispatch->current_longitude)
                <p class="text-gray-800">{{ $dispatch->current_latitude }}, {{ $dispatch->current_longitude }}</p>
                <a
                    href="https://www.google.com/maps?q={{ $dispatch->current_latitude }},{{ $dispatch->current_longitude }}"
                    target="_blank"
                    class="text-sm text-orange-600 hover:text-orange-700"
                >
                    Open in Maps
                </a>
            @else
                <p class="text-gray-500">Location will appear when the driver starts sharing updates.</p>
            @endif
        </div>
    </div>

    @if($dispatch->deliveryStops->count())
        <div class="mt-6">
            <h4 class="font-semibold text-gray-700 mb-2">Delivery Stops</h4>
            <div class="space-y-2">
                @foreach($dispatch->deliveryStops as $stop)
                    <div class="border rounded-lg p-3 bg-gray-50">
                        <p><strong>Stop {{ $stop->stop_order }}:</strong> {{ $stop->address }}</p>
                        <p class="text-sm text-gray-600">Recipient: {{ $stop->recipient_name }} ({{ $stop->recipient_phone }})</p>
                        <p class="text-sm text-gray-600">Status: {{ ucfirst($stop->status ?? 'pending') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
