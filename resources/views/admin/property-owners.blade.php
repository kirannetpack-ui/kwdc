@extends('layouts.app')

@section('title', 'Property Owners')
@section('header', 'Property Owners')

@section('content')
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">Property Owners</h3>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Properties</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($propertyOwners ?? [] as $owner)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-500">#{{ $owner->id }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $owner->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $owner->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $owner->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @php $propertyCount = \App\Models\Warehouse::where('user_id', $owner->id)->count(); @endphp
                        {{ $propertyCount }} Registered
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if(Route::has('admin.property-owners.show'))
                        <a href="{{ route('admin.property-owners.show', $owner->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        @endif

                        @if(Route::has('admin.property-owners.edit'))
                        <a href="{{ route('admin.property-owners.edit', $owner->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-building text-4xl mb-3 text-gray-300"></i>
                        <p>No property owners found in the system.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection