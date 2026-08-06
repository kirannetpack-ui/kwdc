<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Warehouse Details: {{ $warehouse->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4">
                    <dt class="font-bold">Name:</dt><dd>{{ $warehouse->name }}</dd>
                    <dt class="font-bold">Type:</dt><dd>{{ $warehouse->type === 'building' ? 'Building (indoor)' : 'Open Field / Land' }}</dd>
                    <dt class="font-bold">Address:</dt><dd>{{ $warehouse->address ?? 'Not provided' }}</dd>

                    {{-- Dimensions / Area --}}
                    @if($warehouse->type === 'building')
                        <dt class="font-bold">Dimensions:</dt><dd>{{ $warehouse->length }} × {{ $warehouse->width }} × {{ $warehouse->height }} m</dd>
                        <dt class="font-bold">Volume:</dt><dd>{{ number_format($warehouse->total_capacity, 2) }} m³</dd>
                        <dt class="font-bold">Floor Area:</dt><dd>{{ number_format($warehouse->length * $warehouse->width, 2) }} m²</dd>
                    @else
                        <dt class="font-bold">Area:</dt><dd>{{ number_format($warehouse->area_sq_m ?? ($warehouse->length * $warehouse->width), 2) }} m²</dd>
                    @endif

                    {{-- Capacity Info --}}
                    <dt class="font-bold">Total Capacity (100%):</dt>
                    <dd>{{ number_format($warehouse->total_capacity, 2) }} {{ $warehouse->type === 'building' ? 'm³' : 'm²' }}</dd>
                    <dt class="font-bold">Usable Capacity (90%):</dt>
                    <dd>{{ number_format($warehouse->usable_capacity, 2) }} {{ $warehouse->type === 'building' ? 'm³' : 'm²' }}</dd>
                    <dt class="font-bold">Allocated Space:</dt>
                    <dd>{{ number_format($warehouse->allocated_space, 2) }} m²</dd>

                    {{-- Occupancy Bar --}}
                    <dt class="font-bold">Occupancy:</dt>
                    <dd>
                        @php
                            $percent = $warehouse->total_capacity > 0 ? round(($warehouse->allocated_space / $warehouse->total_capacity) * 100) : 0;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="text-sm">{{ $percent }}% ({{ number_format($warehouse->allocated_space, 2) }} / {{ number_format($warehouse->total_capacity, 2) }} m²)</span>
                    </dd>

                    {{-- Amenities --}}
                    <dt class="font-bold">CCTV:</dt><dd>{{ $warehouse->has_cctv ? 'Yes' : 'No' }}</dd>
                    <dt class="font-bold">Security Guard:</dt><dd>{{ $warehouse->has_security_guard ? 'Yes ('.$warehouse->guard_count.' guards)' : 'No' }}</dd>
                    <dt class="font-bold">Labors:</dt><dd>{{ $warehouse->has_labors ? 'Yes' : 'No' }}</dd>
                    <dt class="font-bold">Motorable:</dt><dd>{{ $warehouse->is_motorable ? 'Yes' : 'No' }}</dd>
                    <dt class="font-bold">Distance from city:</dt><dd>{{ $warehouse->distance_from_city ?: 'N/A' }} km</dd>
                    <dt class="font-bold">Sharing:</dt><dd>{{ $warehouse->allow_shared ? 'Shared' : 'Exclusive' }}</dd>

                    {{-- Pricing --}}
                    <dt class="font-bold">Price per unit:</dt><dd>Rs. {{ number_format($warehouse->price_per_unit, 2) }} per m²</dd>
                    <dt class="font-bold">Security deposit:</dt>
                    <dd>
                        @if($warehouse->security_deposit_fixed)
                            Rs. {{ number_format($warehouse->security_deposit_fixed, 2) }} fixed
                        @elseif($warehouse->security_deposit_percentage)
                            {{ $warehouse->security_deposit_percentage }}% of monthly rent
                        @else
                            Not set
                        @endif
                    </dd>

                    <dt class="font-bold">Status:</dt>
                    <dd>
                        <span class="px-2 py-1 rounded text-xs 
                            @if($warehouse->status == 'approved') bg-green-200 text-green-800
                            @elseif($warehouse->status == 'pending') bg-yellow-200 text-yellow-800
                            @else bg-red-200 text-red-800 @endif">
                            {{ ucfirst($warehouse->status) }}
                        </span>
                    </dd>
                </dl>

                {{-- CCTV Stream --}}
                @if($warehouse->camera_stream_url)
                    <div class="mt-4">
                        <strong>Live CCTV Feed:</strong>
                        <div class="mt-2">
                            <iframe src="{{ $warehouse->camera_stream_url }}" width="100%" height="400" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                @endif

                {{-- Photos --}}
                @if($warehouse->photos->count())
                    <div class="mt-4">
                        <strong>Photos:</strong>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($warehouse->photos as $photo)
                                <img src="{{ asset('storage/'.$photo->photo_path) }}" class="w-32 h-32 object-cover rounded border">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Documents --}}
                @if($warehouse->documents->count())
                    <div class="mt-4">
                        <strong>Documents:</strong>
                        <ul>
                            @foreach($warehouse->documents as $doc)
                                <li><a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank">{{ $doc->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('warehouses.index') }}" class="text-blue-600">← Back to My Warehouses</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>