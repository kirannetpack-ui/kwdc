@extends('layouts.app')

@section('title', 'Equipment List')
@section('header', 'Equipment List')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jobs</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($equipment ?? [] as $item)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $item->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->type }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ optional($item->owner)->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $item->status == 'available' ? 'bg-green-100 text-green-800' : ($item->status == 'rented' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $active = $item->equipmentJobs->whereIn('status', ['accepted', 'in_progress'])->count();
                            $completed = $item->equipmentJobs->where('status', 'completed')->count();
                        @endphp
                        @if($active > 0)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 mr-1">
                                {{ $active }} Active
                            </span>
                        @endif
                        @if($completed > 0)
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                {{ $completed }} Done
                            </span>
                        @endif
                        @if($active == 0 && $completed == 0)
                            <span class="text-gray-400 text-xs">No jobs</span>
                        @endif
                        
                        <a href="{{ route('admin.equipment.jobs', $item->id) }}" class="text-blue-500 hover:text-blue-700 text-xs ml-2 underline">
                            View All
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.equipment.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.equipment.destroy', $item->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this equipment and its history?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-tools text-4xl mb-3 text-gray-300"></i>
                        <p>No equipment registered by owners yet.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $equipment->links() }}
    </div>
</div>
@endsection