<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pending Warehouses</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-4">{{ session('error') }}</div>
                @endif

                @if($warehouses->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Name</th>
                                <th class="border p-2">Owner</th>
                                <th class="border p-2">Dimensions</th>
                                <th class="border p-2">Price (per unit)</th>
                                <th class="border p-2">Deposit</th>
                                <th class="border p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouses as $wh)
                            <tr>
                                <td class="border p-2">{{ $wh->name }}<br><span class="text-xs">{{ $wh->address ?? 'No address' }}</span></td>
                                <td class="border p-2">{{ $wh->owner->name ?? 'N/A' }}<br>{{ $wh->owner->email ?? '' }}</td>
                                <td class="border p-2">{{ $wh->length }}x{{ $wh->width }}x{{ $wh->height }} m<br>{{ $wh->type === 'building' ? 'Volume' : 'Area' }}: {{ number_format($wh->total_capacity, 2) }} {{ $wh->type === 'building' ? 'm³' : 'm²' }}</td>
                                <td class="border p-2">
                                    Rs. {{ number_format($wh->price_per_unit, 2) }} 
                                    @if($wh->price_unit_type === 'percentage')
                                        (percentage of total capacity)
                                    @else
                                        per m³/m²
                                    @endif
                                    <form method="POST" action="{{ route('admin.warehouse.updatePrice', $wh->id) }}" class="mt-1">
                                        @csrf
                                        <input type="number" step="0.01" name="price_per_unit" value="{{ $wh->price_per_unit }}" class="w-24 border rounded p-1">
                                        <button type="submit" class="text-blue-600 text-sm">Update</button>
                                    </form>
                                </td>
                                <td class="border p-2">
                                    @if($wh->security_deposit_fixed)
                                        Fixed: Rs. {{ number_format($wh->security_deposit_fixed, 2) }}
                                    @elseif($wh->security_deposit_percentage)
                                        Percentage: {{ $wh->security_deposit_percentage }}%
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('admin.approve', $wh) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reject', $wh) }}" class="inline ml-2">
                                        @csrf
                                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Reject</button>
                                    </form>
                                    <a href="{{ route('warehouses.show', $wh) }}" class="ml-2 text-blue-600">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No pending warehouses.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>