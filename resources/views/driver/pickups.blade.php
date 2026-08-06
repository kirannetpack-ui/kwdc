@extends('layouts.app')

@section('title', 'My Pickups - Driver')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Pickup Jobs</h2>
        <a href="{{ route('driver.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card stats-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stats-label">Total Pickups</h6>
                            <h2 class="stats-number">{{ $stats['total_pickups'] ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card stats-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stats-label">Active Pickups</h6>
                            <h2 class="stats-number">{{ $stats['active_pickups'] ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card stats-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stats-label">Completed</h6>
                            <h2 class="stats-number">{{ $stats['completed_pickups'] ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stats-card stats-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="stats-label">Earnings</h6>
                            <h2 class="stats-number">रु {{ number_format($stats['total_earnings'] ?? 0) }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Pickups -->
    @if(isset($availablePickups) && $availablePickups->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clock text-warning me-2"></i>Available Pickups</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pickup ID</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Items</th>
                            <th>Distance</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availablePickups as $pickup)
                        <tr>
                            <td>#{{ $pickup->id }}</td>
                            <td>{{ $pickup->client->name ?? 'N/A' }}</td>
                            <td>{{ $pickup->pickup_address ?? 'N/A' }}</td>
                            <td>{{ $pickup->items_description ?? 'N/A' }}</td>
                            <td>{{ number_format($pickup->total_distance, 2) }} km</td>
                            <td>रु {{ number_format($pickup->total_price, 2) }}</td>
                            <td>
                                <form action="{{ route('driver.accept-pickup', $pickup->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Accept</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- My Active Pickups -->
    @if(isset($pickups) && $pickups->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tasks text-primary me-2"></i>My Pickups</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pickup ID</th>
                            <th>Client</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Distance</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pickups as $pickup)
                        <tr>
                            <td>#{{ $pickup->id }}</td>
                            <td>{{ $pickup->client->name ?? 'N/A' }}</td>
                            <td>{{ $pickup->pickup_address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $pickup->status_badge }}">
                                    {{ $pickup->status_text }}
                                </span>
                            </td>
                            <td>{{ number_format($pickup->total_distance, 2) }} km</td>
                            <td>रु {{ number_format($pickup->total_price, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($pickup->status == 'assigned')
                                        <form action="{{ route('driver.start-pickup', $pickup->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Start</button>
                                        </form>
                                    @endif
                                    @if($pickup->status == 'in_progress')
                                        <form action="{{ route('driver.complete-pickup', $pickup->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Complete</button>
                                        </form>
                                    @endif
                                    @if(in_array($pickup->status, ['assigned', 'in_progress']))
                                        <form action="{{ route('driver.cancel-pickup', $pickup->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this pickup?')">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($pickups, 'links'))
            <div class="card-footer">
                {{ $pickups->links() }}
            </div>
        @endif
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-boxes text-muted" style="font-size: 48px;"></i>
            <h4 class="mt-3">No Pickups</h4>
            <p class="text-muted">You don't have any active pickups.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .stats-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .stats-card .stats-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    
    .stats-card .stats-number {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    .stats-card .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    
    .stats-card.stats-primary .stats-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    
    .stats-card.stats-success .stats-icon {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
    }
    
    .stats-card.stats-warning .stats-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    
    .stats-card.stats-danger .stats-icon {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
</style>
@endpush