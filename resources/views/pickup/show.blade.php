@extends('layouts.app')

@section('title', 'Pickup Request Details')
@section('header', 'Pickup Request Details')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-800">Pickup Request Details</h3>
        <p class="text-gray-500">View your pickup request information</p>
    </div>
    
    @if(isset($pickup))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="font-semibold">Pickup Number:</p>
            <p class="text-gray-800">{{ $pickup->pickup_number ?? 'N/A' }}</p>
            
            <p class="font-semibold mt-4">Destination Address:</p>
            <p class="text-gray-800">{{ $pickup->destination_address ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="font-semibold">Status:</p>
            <p class="text-gray-800">
                <span class="status-badge status-{{ $pickup->status ?? 'pending' }}">
                    {{ ucfirst($pickup->status ?? 'Pending') }}
                </span>
            </p>
            
            <p class="font-semibold mt-4">Total Boxes:</p>
            <p class="text-gray-800">{{ $pickup->total_boxes ?? 0 }}</p>
        </div>
    </div>
    
    @if($pickup->pickupStops && $pickup->pickupStops->count() > 0)
    <div class="mt-6">
        <h4 class="font-semibold mb-2">Pickup Stops</h4>
        @foreach($pickup->pickupStops as $stop)
        <div class="border rounded-lg p-3 mb-2">
            <p><strong>Stop {{ $stop->stop_order }}:</strong> {{ $stop->address }}</p>
            <p>Contact: {{ $stop->contact_name }} ({{ $stop->contact_phone }})</p>
            <p>Boxes: {{ $stop->boxes_count }}</p>
        </div>
        @endforeach
    </div>
    @endif
    
    @else
    <p class="text-gray-500">Pickup request not found.</p>
    @endif
    
    <div class="mt-6">
        <a href="{{ route('pickup.index') }}" class="px-6 py-2 bg-gray-300 rounded-lg">Back to Pickups</a>
    </div>
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
    .status-completed { background: #d1fae5; color: #059669; }
</style>
@endsection