@extends('layouts.app')

@section('title', 'Property Analytics')
@section('header', 'Analytics & Reports')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
</style>
@endpush

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">Total Properties</h6>
                    <h2 class="mb-0">{{ $stats['total_properties'] ?? 0 }}</h2>
                </div>
                <div class="stat-icon bg-white bg-opacity-25 text-white">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">Approved</h6>
                    <h2 class="mb-0">{{ $stats['approved_properties'] ?? 0 }}</h2>
                </div>
                <div class="stat-icon bg-white bg-opacity-25 text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">Pending</h6>
                    <h2 class="mb-0">{{ $stats['pending_properties'] ?? 0 }}</h2>
                </div>
                <div class="stat-icon bg-white bg-opacity-25 text-white">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">Total Revenue</h6>
                    <h2 class="mb-0">रू {{ number_format($stats['total_revenue'] ?? 0, 0) }}</h2>
                </div>
                <div class="stat-icon bg-white bg-opacity-25 text-white">
                    <i class="fas fa-rupee-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Warehouse Requests</h6>
                <h3 class="mb-2">{{ $stats['total_requests'] ?? 0 }}</h3>
                <div class="d-flex gap-3">
                    <span class="badge bg-success">Approved: {{ $stats['approved_requests'] ?? 0 }}</span>
                    <span class="badge bg-warning">Pending: {{ $stats['pending_requests'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted">Monthly Revenue Trend</h6>
                @if(isset($monthlyRevenue) && count($monthlyRevenue) > 0)
                    <canvas id="revenueChart" height="80"></canvas>
                @else
                    <p class="text-muted text-center">No revenue data available yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Requests -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-clipboard-list me-2"></i>
        Recent Warehouse Requests
    </div>
    <div class="card-body">
        @if(isset($recentRequests) && $recentRequests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Warehouse</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRequests as $request)
                        <tr>
                            <td>{{ $request->client->name ?? 'N/A' }}</td>
                            <td>{{ $request->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $request->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('property.requests.index') }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View All
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-clipboard-list text-muted fa-3x mb-3"></i>
                <p class="text-muted">No warehouse requests yet</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
@if(isset($monthlyRevenue) && count($monthlyRevenue) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const monthlyData = @json($monthlyRevenue);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Revenue (NPR)',
                    data: monthlyData.map(item => item.revenue),
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(245, 158, 11, 1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'रू ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endpush
@endsection