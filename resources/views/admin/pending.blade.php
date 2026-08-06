@extends('layouts.app')

@section('title', 'Pending Approvals')
@section('header', 'Pending Approvals')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">Pending Warehouse Approvals</h3>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pendingWarehouses as $warehouse)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $warehouse->name }}</td>
                    <td class="px-6 py-4">{{ $warehouse->location }}</td>
                    <td class="px-6 py-4">{{ optional($warehouse->owner)->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $warehouse->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        <form action="{{ route('admin.approve', $warehouse->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 mr-2">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.reject', $warehouse->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this warehouse?')">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                        <p>No pending warehouses awaiting approval!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $pendingWarehouses->links() }}
    </div>
</div>
@endsection