@extends('layouts.app')

@section('title', 'Pending Properties')
@section('header', 'Pending Approval')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-clock text-warning me-2"></i>
        Properties Pending Approval
        <span class="badge bg-warning ms-2">{{ $pendingWarehouses->total() }}</span>
    </div>
    <div class="card-body">
        @if($pendingWarehouses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Area (sq ft)</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingWarehouses as $warehouse)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $warehouse->name }}</strong>
                                <br>
                                <small class="text-muted">Owner: {{ $warehouse->owner_name ?? 'N/A' }}</small>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($warehouse->location, 30) }}</td>
                            <td>{{ $warehouse->area_sqft ?? 'N/A' }}</td>
                            <td>{{ $warehouse->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-warning">Pending</span>
                            </td>
                            <td>
                                <a href="{{ route('property.warehouses.show', $warehouse->id) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pendingWarehouses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h5 class="text-muted">No properties pending approval</h5>
                <p class="text-muted">All your properties have been reviewed</p>
                <a href="{{ route('warehouses.create') }}" class="btn btn-orange mt-2">
                    <i class="fas fa-plus-circle me-2"></i>Register New Property
                </a>
            </div>
        @endif
    </div>
</div>
@endsection