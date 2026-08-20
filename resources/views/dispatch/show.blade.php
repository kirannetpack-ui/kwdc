@extends('layouts.app')

@section('title', 'Dispatch Details')
@section('header', 'Dispatch Order Details')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Dispatch Order #{{ $dispatch->id }}</h3>
                <p class="text-gray-500">Created on {{ $dispatch->created_at->format('F j, Y, g:i a') }}</p>
            </div>
            @if($dispatch->driver_id && $dispatch->current_latitude && $dispatch->current_longitude)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 mr-1 bg-green-500 rounded-full animate-pulse"></span>
                    Live Tracking
                </span>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="font-semibold text-gray-700">Pickup Address:</p>
            <p class="text-gray-800">{{ $dispatch->pickup_address ?? 'N/A' }}</p>
            
            <p class="font-semibold text-gray-700 mt-4">Delivery Address:</p>
            <p class="text-gray-800">{{ $dispatch->delivery_address ?? 'N/A' }}</p>
            
            <p class="font-semibold text-gray-700 mt-4">Distance:</p>
            <p class="text-gray-800">{{ $dispatch->total_distance ?? 'N/A' }} km</p>
        </div>
        
        <div>
            <p class="font-semibold text-gray-700">Status:</p>
            <p class="text-gray-800">
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-{{ $dispatch->status == 'delivered' ? 'green' : ($dispatch->status == 'pending' ? 'yellow' : 'blue') }}-100 text-{{ $dispatch->status == 'delivered' ? 'green' : ($dispatch->status == 'pending' ? 'yellow' : 'blue') }}-800">
                    {{ ucfirst($dispatch->status ?? 'Pending') }}
                </span>
            </p>
            
            <p class="font-semibold text-gray-700 mt-4">Base Price:</p>
            <p class="text-gray-800 text-xl font-bold text-orange-600">रू {{ number_format($dispatch->base_price ?? 0) }}</p>
            
            <p class="font-semibold text-gray-700 mt-4">Assigned Driver:</p>
            <p class="text-gray-800">{{ optional($dispatch->driver)->name ?? 'Not assigned yet' }}</p>
        </div>
    </div>
    
    @if($dispatch->deliveryStops && $dispatch->deliveryStops->count() > 0)
    <div class="mt-6">
        <h4 class="font-semibold text-gray-700 mb-2">Delivery Stops</h4>
        <div class="space-y-2">
            @foreach($dispatch->deliveryStops as $stop)
            <div class="border rounded-lg p-3 bg-gray-50">
                <p><strong>Stop {{ $stop->stop_order }}:</strong> {{ $stop->address }}</p>
                <p class="text-sm text-gray-600">Recipient: {{ $stop->recipient_name }} ({{ $stop->recipient_phone }})</p>
                <p class="text-sm text-gray-600">Boxes: {{ $stop->boxes_count }}</p>
                @if($stop->invoice_document)
                <div class="mt-2">
                    <a href="{{ Storage::url($stop->invoice_document) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm">
                        <i class="fas fa-file-invoice mr-1"></i> View Invoice
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    {{-- ==================== LIVE MAP (Only for authorized users) ==================== --}}
    @if($dispatch->driver_id && (Auth::user()->role === 'admin' || Auth::id() === $dispatch->client_id || Auth::id() === $dispatch->driver_id))
    <div class="mt-6">
        <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i> 
            Live Location
            @if($dispatch->current_latitude && $dispatch->current_longitude)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 mr-1 bg-green-500 rounded-full animate-pulse"></span>
                    Online
                </span>
            @endif
        </h4>
        <div id="liveMap" style="height: 350px; border-radius: 8px; border: 1px solid #e5e7eb;"></div>
<div id="etaDisplay" class="mt-2 text-sm text-gray-700">
    <i class="fas fa-clock text-gray-500"></i> 
    <span id="etaText">Calculating ETA…</span>
</div>
        <p class="text-xs text-gray-500 mt-2">Marker updates in real‑time as the driver moves.</p>
    </div>
    @endif

    <div class="mt-6 flex justify-start">
        <a href="{{ route('dispatch.index') }}" class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition">
            Back to Dispatches
        </a>
    </div>
</div>

<div id="eta-progress" class="mt-2">
    <div class="flex items-center">
        <i class="fas fa-clock text-gray-500 mr-2"></i>
        <span id="eta-text">Calculating ETA...</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
        <div id="eta-bar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($dispatch->driver_id && (Auth::user()->role === 'admin' || Auth::id() === $dispatch->client_id || Auth::id() === $dispatch->driver_id))
    
    const mapContainer = document.getElementById('liveMap');
    if (!mapContainer) return;

    // 1. Initialize Leaflet map centered on Nepal
    const map = L.map('liveMap').setView([27.7172, 85.3240], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // 2. Initial marker (if coordinates exist)
    let driverMarker = null;
    const initialLat = {{ $dispatch->current_latitude ?? 'null' }};
    const initialLng = {{ $dispatch->current_longitude ?? 'null' }};

    if (initialLat && initialLng) {
        driverMarker = L.marker([initialLat, initialLng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="background-color:#f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            })
        }).addTo(map)
        .bindPopup('<b>Current Driver Location</b>');
        map.setView([initialLat, initialLng], 14);
    }

    // 3. Connect to Reverb and subscribe
    const dispatchId = {{ $dispatch->id }};

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: @json(config('broadcasting.connections.reverb.key')),
        wsHost: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
        wsPort: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
        forceTLS: @json((bool) config('broadcasting.connections.reverb.options.useTLS', false)),
        encrypted: @json((bool) config('broadcasting.connections.reverb.options.useTLS', false)),
        enabledTransports: ['ws', 'wss'],
    });

  window.Echo.channel('dispatch.' + dispatchId)
    .listen('.location.updated', (e) => {
        const lat = e.latitude;
        const lng = e.longitude;

        // Update marker (same as before)
        if (driverMarker) {
            driverMarker.setLatLng([lat, lng]);
        } else {
            driverMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style="background-color:#f59e0b; width: 16px; height: 16px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>`,
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                })
            }).addTo(map)
            .bindPopup('<b>Driver Location</b>');
        }
        map.panTo([lat, lng]);

        // ----- ETA UPDATE -----
      if (e.eta) {
    const etaDate = new Date(e.eta);
    const now = new Date();
    const diffMs = etaDate - now;
    if (diffMs > 0) {
        const minutes = Math.floor(diffMs / 60000);
        const seconds = Math.floor((diffMs % 60000) / 1000);
        document.getElementById('eta-text').textContent = `ETA: ${minutes}m ${seconds}s`;
        // Progress bar (assume total trip time known, or just show a relative value)
        // For simplicity, we can just show a percentage if we know total trip duration.
        // Here we'll just set a fixed 0-100 based on some logic, but we'll skip for now.
    }
} else {
            document.getElementById('etaText').textContent = 'ETA not available';
        }
            map.panTo([lat, lng]);
        });

window.Echo.private('notifications.' + dispatch.clientId)  // you need to get clientId from blade
    .listen('.location.updated', (e) => {
        // Show toast
        showToast('📍 Driver moved to new location');
    });

function showToast(message) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'bg-blue-500 text-white px-4 py-2 rounded shadow-lg mb-2';
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}


    @endif
});
</script>
@endpush
@endsection
