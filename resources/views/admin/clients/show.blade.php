<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Details: {{ $client->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Client Info -->
                <div class="mb-6">
                    <p><strong>Email:</strong> {{ $client->email }}</p>
                    <p><strong>Phone:</strong> {{ $client->phone ?? 'Not provided' }}</p>
                </div>

                <!-- Warehouse Requests and Stock Items -->
                @if($client->warehouseRequests->count())
                    @foreach($client->warehouseRequests as $req)
                        <div class="mb-8 border-b pb-4">
                            <h3 class="text-lg font-bold mb-2">Request #{{ $req->id }}</h3>
                            <p><strong>Required space:</strong> {{ $req->required_space }} m³</p>
                            <p><strong>Duration:</strong> {{ $req->duration_months }} months</p>
                            <p><strong>Status:</strong> {{ ucfirst($req->status) }}</p>
                            <p><strong>Assigned Warehouse(s):</strong>
                                @if($req->assignedWarehouse)
                                    {{ $req->assignedWarehouse->name }}
                                @elseif($req->assignedWarehouses->count())
                                    @foreach($req->assignedWarehouses as $aw)
                                        {{ $aw->name }} ({{ $aw->pivot->allocated_space }} m³)@if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    Not assigned
                                @endif
                            </p>

                            <h4 class="font-bold mt-4">Stock Items</h4>
                            @if($req->stocks->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border p-2">Product</th>
                                                <th class="border p-2">Description</th>
                                                <th class="border p-2">Quantity</th>
                                                <th class="border p-2">SKU</th>
                                                <th class="border p-2">Status</th>
                                                <th class="border p-2">QR Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($req->stocks as $stock)
                                            <tr>
                                                <td class="border p-2">{{ $stock->product_name }}</td>
                                                <td class="border p-2">{{ $stock->description ?? '-' }}</td>
                                                <td class="border p-2">{{ $stock->quantity }} boxes</td>
                                                <td class="border p-2">{{ $stock->sku ?? '-' }}</td>
                                                <td class="border p-2">
                                                    <span class="px-2 py-1 rounded text-xs 
                                                        @if($stock->status == 'pending') bg-yellow-200
                                                        @elseif($stock->status == 'verified') bg-green-200
                                                        @else bg-red-200 @endif">
                                                        {{ ucfirst($stock->status) }}
                                                    </span>
                                                </td>
                                                <td class="border p-2 text-center">
                                                    @if($stock->qr_code)
                                                        <a href="{{ asset('storage/'.$stock->qr_code) }}" target="_blank" class="text-blue-600">View QR</a>
                                                    @else
                                                        <span class="text-gray-400">Not generated</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500">No stock items for this request.</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p>No warehouse requests found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>