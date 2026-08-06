@extends('layouts.app')

@section('title', 'Rejected Properties')
@section('header', 'Rejected Properties')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-times-circle text-danger me-2"></i>
        Rejected Properties
        <span class="badge bg-danger ms-2">{{ $rejectedWarehouses->total() }}</span>
    </div>
    <div class="card-body">
        @if($rejectedWarehouses->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Area (sq ft)</th>
                            <th>Rejected On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rejectedWarehouses as $warehouse)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $warehouse->name }}</strong>
                                <br>
                                <small class="text-muted">Owner: {{ $warehouse->owner_name ?? 'N/A' }}</small>
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($warehouse->location, 30) }}</td>
                            <td>{{ $warehouse->area_sqft ?? 'N/A' }}</td>
                            <td>{{ $warehouse->updated_at->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-danger">Rejected</span>
                            </td>
                            <td>
                                <a href="{{ route('property.warehouses.show', $warehouse->id) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('property.warehouses.edit', $warehouse->id) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $rejectedWarehouses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h5 class="text-muted">No rejected properties</h5>
                <p class="text-muted">All your properties have been approved or are pending</p>
            </div>
        @endif
    </div>
</div>
@endsection