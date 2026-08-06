@extends('layouts.app')

@section('title', 'My Jobs - Driver')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Jobs</h2>
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
                            <h6 class="stats-label">Active Jobs</h6>
                            <h2 class="stats-number">{{ $activeJobs->total() ?? 0 }}</h2>
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
                            <h2 class="stats-number">{{ $completedJobs->total() ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
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
                            <h6 class="stats-label">Available</h6>
                            <h2 class="stats-number">{{ $availableJobs->total() ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
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
                            <h6 class="stats-label">Cancelled</h6>
                            <h2 class="stats-number">{{ $cancelledJobs->total() ?? 0 }}</h2>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Jobs -->
    @if(isset($availableJobs) && $availableJobs->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clock text-warning me-2"></i>Available Jobs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Client</th>
                            <th>Pickup Location</th>
                            <th>Distance</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableJobs as $job)
                        <tr>
                            <td>#{{ $job->id }}</td>
                            <td>{{ $job->client->name ?? 'N/A' }}</td>
                            <td>{{ $job->pickup_address ?? 'N/A' }}</td>
                            <td>{{ number_format($job->total_distance ?? 0, 2) }} km</td>
                            <td>रु {{ number_format($job->base_price ?? 0, 2) }}</td>
                            <td>
                                <form action="{{ route('driver.jobs.accept', $job->id) }}" method="POST">
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
        @if(method_exists($availableJobs, 'links'))
            <div class="card-footer">
                {{ $availableJobs->links() }}
            </div>
        @endif
    </div>
    @endif

    <!-- Active Jobs -->
    @if(isset($activeJobs) && $activeJobs->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tasks text-primary me-2"></i>Active Jobs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Client</th>
                            <th>Pickup Location</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeJobs as $job)
                        <tr>
                            <td>#{{ $job->id }}</td>
                            <td>{{ $job->client->name ?? 'N/A' }}</td>
                            <td>{{ $job->pickup_address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $job->status === 'assigned' ? 'info' : ($job->status === 'picked_up' ? 'warning' : 'primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                </span>
                            </td>
                            <td>रु {{ number_format($job->driver_earning ?? 0, 2) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($job->status == 'assigned')
                                        <form action="{{ route('driver.jobs.start', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Start</button>
                                        </form>
                                    @endif
                                    @if($job->status == 'picked_up' || $job->status == 'on_the_way')
                                        <form action="{{ route('driver.jobs.deliver', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Deliver</button>
                                        </form>
                                    @endif
                                    @if(in_array($job->status, ['assigned', 'picked_up']))
                                        <form action="{{ route('driver.jobs.cancel', $job->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this job?')">Cancel</button>
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
        @if(method_exists($activeJobs, 'links'))
            <div class="card-footer">
                {{ $activeJobs->links() }}
            </div>
        @endif
    </div>
    @else
    <div class="card mb-4">
        <div class="card-body text-center py-4">
            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
            <h5 class="mt-2">No Active Jobs</h5>
            <p class="text-muted">You don't have any active jobs right now.</p>
        </div>
    </div>
    @endif

    <!-- Completed Jobs -->
    @if(isset($completedJobs) && $completedJobs->count() > 0)
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>Completed Jobs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedJobs as $job)
                        <tr>
                            <td>#{{ $job->id }}</td>
                            <td>{{ $job->client->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-success">Delivered</span>
                            </td>
                            <td>रु {{ number_format($job->driver_earning ?? 0, 2) }}</td>
                            <td>{{ $job->delivered_at ? $job->delivered_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($completedJobs, 'links'))
            <div class="card-footer">
                {{ $completedJobs->links() }}
            </div>
        @endif
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
    
    .table th {
        font-weight: 600;
        color: #475569;
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 12px;
    }
</style>
@endpush