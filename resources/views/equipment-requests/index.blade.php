@extends('layouts.app')
@section('title', 'Equipment requests')
@section('header', 'Equipment requests')
@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <h1 class="text-2xl font-semibold">Equipment requests</h1>
    @if(auth()->user()->role === 'client' || auth()->user()->isAdmin())
        <a class="btn btn-primary" href="{{ route('equipment-requests.create') }}"><i class="fas fa-plus" aria-hidden="true"></i> New request</a>
    @endif
</div>
<div class="overflow-x-auto">
    <table class="table w-full">
        <thead><tr><th scope="col">Request</th><th scope="col">Location</th><th scope="col">Dates</th><th scope="col">Status</th><th scope="col"><span class="sr-only">Actions</span></th></tr></thead>
        <tbody>
        @forelse($requests as $request)
            <tr><td><a class="font-semibold" href="{{ route('equipment-requests.show', $request) }}">{{ $request->equipment_type }}</a><div class="text-sm text-gray-500">#{{ $request->id }}</div></td><td>{{ $request->location }}</td><td>{{ $request->start_date?->format('M j, Y') }} - {{ $request->end_date?->format('M j, Y') }}</td><td><span class="badge bg-light text-dark">{{ ucfirst($request->status) }}</span></td><td><a href="{{ route('equipment-requests.show', $request) }}" aria-label="Open equipment request {{ $request->id }}"><i class="fas fa-arrow-right" aria-hidden="true"></i></a></td></tr>
        @empty
            <tr><td colspan="5"><div class="py-8 text-center"><i class="fas fa-tractor text-3xl mb-3 text-gray-400" aria-hidden="true"></i><p class="font-semibold">No equipment requests yet</p></div></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $requests->links() }}</div>
@endsection
