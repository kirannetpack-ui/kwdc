@extends('layouts.app')

@section('title', 'My Properties')
@section('header', 'My Properties')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold text-gray-800">
                <i class="fas fa-warehouse text-orange-500 me-2"></i>My Properties
            </h1>
            <p class="text-muted small">Manage your registered warehouses and properties</p>
        </div>
        <a href="{{ route('warehouses.create') }}" class="btn btn-orange" style="background: #f59e0b; color: white; border: none;">
            <i class="fas fa-plus-circle me-2"></i>Register New Property
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Total Properties</h6>
                        <h2 class="mb-0">{{ $warehouses->total() }}</h2>
                    </div>
                    <i class="fas fa-warehouse fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Approved</h6>
                        <h2 class="mb-0">{{ $warehouses->where('status', 'approved')->count() }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Pending</h6>
                        <h2 class="mb-0">{{ $warehouses->where('status', 'pending')->count() }}</h2>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 opacity-75">Rejected</h6>
                        <h2 class="mb-0">{{ $warehouses->where('status', 'rejected')->count() }}</h2>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Table -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list me-2"></i>Property List
        </div>
        <div class="card-body">
            @if($warehouses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Property Name</th>
                                <th>Location</th>
                                <th>Area (sq ft)</th>
                                <th>Price/sq ft</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th style="min-width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouses as $warehouse)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $warehouse->name }}</strong>
                                    @if($warehouse->cold_storage)
                                        <span class="badge bg-info ms-1"><i class="fas fa-snowflake"></i></span>
                                    @endif
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($warehouse->location, 30) }}</td>
                                <td>{{ number_format($warehouse->area_sqft ?? 0) }}</td>
                                <td>रू {{ number_format($warehouse->price_per_sqft ?? 0, 2) }}</td>
                                <td>
                                    @if($warehouse->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($warehouse->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($warehouse->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($warehouse->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $warehouse->created_at->format('M d, Y') }}</td>
                                <td>
                                    <!-- Action Buttons -->
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View Button -->
                                        <a href="{{ route('property.warehouses.show', $warehouse->id) }}" 
                                           class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('property.warehouses.edit', $warehouse->id) }}" 
                                           class="btn btn-warning" title="Edit Property">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        <form action="{{ route('property.warehouses.destroy', $warehouse->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete Property">
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
                <div class="mt-3">
                    {{ $warehouses->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-warehouse text-muted fa-4x mb-3"></i>
                    <h5 class="text-muted">No properties registered yet</h5>
                    <p class="text-muted">Start by registering your first warehouse</p>
                    <a href="{{ route('warehouses.create') }}" class="btn btn-orange mt-2" style="background: #f59e0b; color: white; border: none;">
                        <i class="fas fa-plus-circle me-2"></i>Register Your First Property
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection