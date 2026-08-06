@extends('layouts.app')

@section('title', 'Approved Properties')
@section('header', 'Approved Properties')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-check-circle text-success me-2"></i>
        Approved Properties
        <span class="badge bg-success ms-2">{{ $approvedWarehouses->total() }}</span>
    </div>
    <div class="card-body">
        @if($approvedWarehouses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Area (sq ft)</th>
                            <th>Approved On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedWarehouses as $warehouse)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $warehouse->name }}</strong>
                                <br>
                                <small class="text-muted">Owner: {{ $warehouse->owner_name ?? 'N/A' }}</small>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($warehouse->location, 30) }}</td>
                            <td>{{ $warehouse->area_sqft ?? 'N/A' }}</td>
                            <td>{{ $warehouse->approved_at ? $warehouse->approved_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success">Approved</span>
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
                {{ $approvedWarehouses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-warehouse text-muted fa-4x mb-3"></i>
                <h5 class="text-muted">No approved properties yet</h5>
                <p class="text-muted">Submit your properties for approval</p>
                <a href="{{ route('warehouses.create') }}" class="btn btn-orange mt-2">
                    <i class="fas fa-plus-circle me-2"></i>Register New Property
                </a>
            </div>
        @endif
    </div>
</div>
@endsection