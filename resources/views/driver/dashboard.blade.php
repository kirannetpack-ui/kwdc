@extends('layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
@php
    $statusClasses = [
        'pending' => 'warning',
        'assigned' => 'info',
        'picked_up' => 'primary',
        'on_the_way' => 'primary',
        'in_progress' => 'primary',
        'delivered' => 'success',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];
@endphp

<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-1">Namaste, {{ auth()->user()->name }}</p>
            <h2 class="mb-0">Driver Dashboard</h2>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('driver.jobs') }}" class="btn btn-primary">
                <i class="fas fa-route me-2"></i>My Jobs
            </a>
            <a href="{{ route('driver.pickups') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes-stacked me-2"></i>Pickups
            </a>
            <a href="{{ route('reminders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-calendar-check me-2"></i>Reminders
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card stats-primary h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Assigned Jobs</h6>
                        <h2 class="stats-number">{{ $stats['total_jobs'] ?? 0 }}</h2>
                    </div>
                    <div class="stats-icon"><i class="fas fa-truck-fast"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card stats-warning h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Active Runs</h6>
                        <h2 class="stats-number">{{ $stats['active_jobs'] ?? 0 }}</h2>
                    </div>
                    <div class="stats-icon"><i class="fas fa-location-arrow"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card stats-success h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Completed</h6>
                        <h2 class="stats-number">{{ $stats['completed_jobs'] ?? 0 }}</h2>
                    </div>
                    <div class="stats-icon"><i class="fas fa-circle-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card stats-info h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stats-label">Earnings</h6>
                        <h2 class="stats-number">Rs {{ number_format($stats['total_earnings'] ?? 0) }}</h2>
                    </div>
                    <div class="stats-icon"><i class="fas fa-wallet"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Pickup Summary</h5>
                        <span class="badge bg-primary">{{ $stats['total_pickups'] ?? 0 }} total</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Active Pickups</span>
                        <strong>{{ $stats['active_pickups'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Completed Pickups</span>
                        <strong>{{ $stats['completed_pickups'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Registered Vehicles</span>
                        <strong>{{ $stats['vehicles'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Available Dispatches</h5>
                        <a href="{{ route('driver.available-jobs') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>

                    @forelse($availableJobs as $job)
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $job->pickup_address }}</div>
                                <div class="text-muted small">
                                    To {{ $job->delivery_address ?? 'multiple stops' }} · {{ number_format($job->total_distance ?? 0, 1) }} km
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-semibold">Rs {{ number_format($job->base_price ?? 0) }}</div>
                                <span class="badge bg-warning text-dark">{{ ucfirst($job->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                            <p class="mb-0">No open dispatches right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Recent Jobs</h5>
                        <a href="{{ route('driver.jobs') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                    </div>

                    @forelse($recentJobs as $job)
                        <div class="d-flex justify-content-between align-items-start gap-3 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold">#{{ $job->tracking_id ?? $job->id }}</div>
                                <div class="text-muted small">{{ $job->pickup_address }}</div>
                            </div>
                            <span class="badge bg-{{ $statusClasses[$job->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0 py-4 text-center">No assigned jobs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Recent Pickups</h5>
                        <a href="{{ route('driver.pickups') }}" class="btn btn-sm btn-outline-secondary">Open</a>
                    </div>

                    @forelse($recentPickups as $pickup)
                        <div class="d-flex justify-content-between align-items-start gap-3 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold">#{{ $pickup->tracking_id ?? $pickup->id }}</div>
                                <div class="text-muted small">{{ $pickup->destination_address ?? $pickup->notes ?? 'Pickup route' }}</div>
                            </div>
                            <span class="badge bg-{{ $statusClasses[$pickup->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $pickup->status)) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0 py-4 text-center">No pickup work yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
