@extends('layouts.app')

@section('title', 'Insurance Requests')
@section('header', 'Insurance Requests')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-6">All Insurance Requests</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Request ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Insurance Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($insurances ?? [] as $insurance)
                <tr>
                    <td class="px-6 py-4">#{{ $insurance->id }}</td>
                    <td class="px-6 py-4">{{ $insurance->client->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $insurance->type }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $insurance->status }}">
                            {{ ucfirst($insurance->status ?? 'Pending') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $insurance->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <button class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-eye"></i></button>
                        <button class="text-green-500 hover:text-green-700"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No insurance requests found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection