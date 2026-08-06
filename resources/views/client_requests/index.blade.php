<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Warehouse Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <a href="{{ route('my-requests.create') }}" class="inline-block bg-green-500 text-white font-bold py-2 px-4 rounded mb-4">+ New Request</a>

                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($requests->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Space (m³)</th>
                                <th class="border p-2">Duration (months)</th>
                                <th class="border p-2">Status</th>
                                <th class="border p-2">Assigned Warehouse</th>
                                <th class="border p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr class="border-b">
                                <td class="p-2">{{ $req->required_space }} m³</td>
                                <td class="p-2">{{ $req->duration_months }}</td>
                                <td class="p-2">
                                    <span class="px-2 py-1 rounded text-xs 
                                        @if($req->status == 'pending') bg-yellow-200 text-gray-800
                                        @elseif($req->status == 'assigned') bg-green-200 text-gray-800
                                        @else bg-blue-200 text-gray-800 @endif">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="p-2">{{ $req->assignedWarehouse->name ?? 'Not yet assigned' }}</td>
                                <td class="p-2">
                                    @if($req->status == 'assigned')
                                        <div class="flex space-x-2">
                                            <a href="{{ route('client.stocks', $req->id) }}" class="text-blue-600 hover:underline">View Stock</a>
                                            <a href="{{ route('dispatch.create', $req->id) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Create Dispatch Order</a>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No requests yet. Click "New Request" to start.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>