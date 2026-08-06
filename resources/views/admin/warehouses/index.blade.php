@extends('layouts.app')

@section('title', 'All Warehouses')
@section('header', 'All Warehouses')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">Warehouse List</h3>
        <a href="{{ route('admin.warehouses.create') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
            <i class="fas fa-plus mr-2"></i> Add Warehouse
        </a>
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
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($warehouses ?? [] as $warehouse)
                <tr>
                    <!-- Name Column (Now clickable) -->
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" class="font-medium text-blue-600 hover:underline">
                            {{ $warehouse->name }}
                        </a>
                    </td>
                    
                    <td class="px-6 py-4">{{ $warehouse->location }}</td>
                    <td class="px-6 py-4">{{ $warehouse->owner->name ?? 'N/A' }}</td>
                    
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $warehouse->status ?? 'pending' }}">
                            {{ ucfirst($warehouse->status ?? 'Pending') }}
                        </span>
                    </td>
                    
                    <!-- Actions Column (Clean and fixed) -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <!-- Single View Button -->
                        <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i> View
                        </a>
                        
                        <!-- Edit Button -->
                        <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this warehouse?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No warehouses found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection