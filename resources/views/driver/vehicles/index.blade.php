@extends('layouts.app')

@section('title', 'My Vehicles')
@section('header', 'My Vehicles')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Registered Vehicles</h3>
            <p class="text-sm text-gray-500 mt-1">Manage your vehicle fleet and documentation</p>
        </div>
        <a href="{{ route('driver.vehicles.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Register New Vehicle
        </a>
    </div>

    @if(isset($vehicles) && $vehicles->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle Number</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verification</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($vehicles as $vehicle)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-sm font-medium">
                        {{ $vehicle->vehicle_number }}
                        <p class="text-xs text-gray-400">ID: {{ $vehicle->registration_number ?? 'N/A' }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $vehicle->full_vehicle_type ?? $vehicle->vehicle_type }}
                        @if($vehicle->manufacturer || $vehicle->model)
                            <p class="text-xs text-gray-500">{{ $vehicle->manufacturer }} {{ $vehicle->model }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ number_format($vehicle->capacity) }} {{ $vehicle->capacity_unit }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="text-xs">
                            <div>Reg: {{ $vehicle->registration_date ? date('M d, Y', strtotime($vehicle->registration_date)) : 'N/A' }}</div>
                            @if($vehicle->insurance_valid_until)
                                <div class="text-{{ \Carbon\Carbon::parse($vehicle->insurance_valid_until)->isPast() ? 'red' : 'green' }}-600">
                                    Ins: {{ date('M d, Y', strtotime($vehicle->insurance_valid_until)) }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($vehicle->status == 'active')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @elseif($vehicle->status == 'pending')
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-600">
                                <i class="fas fa-clock mr-1"></i>Pending
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-600">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($vehicle->is_verified)
                            <span class="text-green-600 text-sm">
                                <i class="fas fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span class="text-yellow-600 text-sm">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                        @if($vehicle->rejection_reason)
                            <p class="text-xs text-red-500 mt-1">{{ $vehicle->rejection_reason }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('driver.vehicles.edit', $vehicle->id) }}" 
                               class="text-blue-500 hover:text-blue-700 transition" 
                               title="Edit Vehicle">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('driver.vehicles.show', $vehicle->id) }}" 
                               class="text-green-500 hover:text-green-700 transition" 
                               title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('driver.vehicles.destroy', $vehicle->id) }}" 
                                  class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete Vehicle">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $vehicles->links() }}
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pt-4 border-t">
        <div class="bg-gray-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $vehicles->total() }}</p>
            <p class="text-xs text-gray-500">Total Vehicles</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-green-600">
                {{ $vehicles->filter(function($v) { return $v->status == 'active' && $v->is_verified; })->count() }}
            </p>
            <p class="text-xs text-gray-500">Active & Verified</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">
                {{ $vehicles->filter(function($v) { return $v->status == 'pending' || !$v->is_verified; })->count() }}
            </p>
            <p class="text-xs text-gray-500">Pending Approval</p>
        </div>
    </div>
    
    @else
    <div class="text-center py-12 bg-gray-50 rounded-lg">
        <i class="fas fa-truck text-gray-400 text-5xl mb-4"></i>
        <p class="text-gray-500 text-lg">No vehicles registered yet</p>
        <p class="text-gray-400 text-sm mt-2">Click "Register New Vehicle" to add your vehicle</p>
        <div class="mt-6">
            <a href="{{ route('driver.vehicles.create') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i> Register Your First Vehicle
            </a>
        </div>
    </div>
    @endif
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this vehicle? This action cannot be undone.');
}
</script>
@endsection