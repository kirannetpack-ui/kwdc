@extends('layouts.app')

@section('title', 'Client Requests')
@section('header', 'Client Requests')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">All Client Requests</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($requests ?? [] as $request)
                <tr>
                    <td class="px-6 py-4">#{{ $request->id }}</td>
                    <td class="px-6 py-4">{{ $request->client->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $request->warehouse->name ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $request->status }}">{{ ucfirst($request->status ?? 'Pending') }}</span>
                    </td>
                    <td class="px-6 py-4">{{ $request->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('warehouse-requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" aria-label="View request #{{ $request->id }}"><i class="fas fa-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No requests found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
