@extends('layouts.app')

@section('title', 'My Equipment')
@section('header', 'My Equipment List')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">My Registered Equipment</h3>
        <a href="{{ route('equipment.register') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
            <i class="fas fa-plus mr-2"></i> Register Equipment
        </a>
    </div>
    
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($equipment ?? [] as $item)
                <tr>
                    <td class="px-6 py-4">{{ $item->name }}</td>
                    <td class="px-6 py-4">{{ $item->type }}</td>
                    <td class="px-6 py-4">{{ $item->model ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-{{ $item->status ?? 'available' }}">
                            {{ ucfirst($item->status ?? 'Available') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
    {{-- <a href="{{ route('equipment.edit', $item->id) }}" class="text-blue-500 hover:text-blue-700">
        <i class="fas fa-edit"></i>
    </a> --}}
    <form action="{{ route('equipment.delete', $item->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this equipment?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No equipment registered yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-available { background: #d1fae5; color: #059669; }
    .status-rented { background: #fed7aa; color: #ea580c; }
    .status-maintenance { background: #fee2e2; color: #dc2626; }
</style>
@endsection