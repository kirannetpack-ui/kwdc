@extends('layouts.app')

@section('title', 'Driver Dashboard')
@section('header', 'Driver Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Apple-style Hero Banner -->
    <div class="kwdc-dashboard-hero flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                    Fleet Driver Console
                </span>
                <span class="text-xs text-gray-400 font-medium">Namaste, {{ auth()->user()->name }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                Driver Dashboard
            </h1>
            <p class="text-sm text-gray-500 mt-1 max-w-xl">
                Track your assigned delivery routes, accept marketplace dispatches, and manage customer pickup transfers across the Kathmandu Valley network.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="kwdc-beacon-container">
                <span class="kwdc-beacon-dot"></span>
                <span>Active & On Duty</span>
            </div>
            <div class="px-4 py-2 rounded-full bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-600">
                <i class="far fa-calendar-alt text-gray-400 mr-1.5"></i> {{ now()->format('D, M j, Y') }}
            </div>
        </div>
    </div>

    <!-- Quick Action Pills -->
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
        <a href="{{ route('driver.available-jobs') }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-magnifying-glass-location text-orange-500"></i>
            <span>Browse Available Jobs</span>
        </a>
        <a href="{{ route('driver.jobs') }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-route text-blue-500"></i>
            <span>My Active Dispatches</span>
        </a>
        <a href="{{ route('driver.pickups') }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-boxes-stacked text-emerald-500"></i>
            <span>Pickup Assignments</span>
        </a>
        <a href="{{ route('driver.earnings') }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-wallet text-purple-500"></i>
            <span>Earnings Ledger</span>
        </a>
        <a href="{{ route('reminders.index') }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-calendar-check text-gray-500"></i>
            <span>Reminders</span>
        </a>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Deliveries</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $stats['active_jobs'] ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #eff6ff; color: #2563eb;">
                    <i class="fas fa-route"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>In Progress Now</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Completed Trips</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $stats['completed_jobs'] ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #ecfdf5; color: #059669;">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Delivered Safely</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Earnings</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">NPR {{ number_format((float) ($stats['total_earnings'] ?? 0)) }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #fffbeb; color: #d97706;">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Net Driver Payout</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>

        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Pickups</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900">{{ $stats['active_pickups'] ?? 0 }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: #fdf2f8; color: #db2777;">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>Scheduled Transfers</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>
    </div>

    <!-- Active Jobs & Marketplace Grids -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Dispatches -->
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck text-orange-500"></i>
                    <h2 class="font-bold text-gray-900 text-base">My Assigned Dispatches</h2>
                </div>
                <a href="{{ route('driver.jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($recentJobs ?? [] as $job)
                <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $job->id) }}">
                    <div>
                        <p class="font-bold text-gray-900 text-sm">Order #{{ $job->tracking_id ?? $job->id }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            <span class="font-semibold text-gray-800">{{ $job->pickup_address ?? 'Origin' }}</span> &rarr; 
                            <span>{{ $job->delivery_address ?? $job->destination_address ?? 'Destination' }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                            {{ ucfirst(str_replace('_', ' ', $job->status ?? 'assigned')) }}
                        </span>
                        <p class="text-xs font-bold text-emerald-600 mt-1">NPR {{ number_format((float) ($job->driver_earning ?? 0)) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">No active delivery assignments. Browse available jobs below.</p>
                @endforelse
            </div>
        </div>

        <!-- Available Marketplace Jobs -->
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hand-holding-dollar text-emerald-500"></i>
                    <h2 class="font-bold text-gray-900 text-base">Available Job Marketplace</h2>
                </div>
                <a href="{{ route('driver.available-jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Marketplace</a>
            </div>
            <div class="space-y-3">
                @forelse($availableJobs ?? [] as $job)
                <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $job->id) }}">
                    <div>
                        <p class="font-bold text-gray-900 text-sm">Dispatch #{{ $job->tracking_id ?? $job->id }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Pickup: <span class="font-semibold text-gray-800">{{ $job->pickup_address ?? 'Kathmandu Hub' }}</span>
                            @if($job->delivery_address || $job->destination_address)
                                &bull; To: {{ $job->delivery_address ?? $job->destination_address }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-extrabold text-emerald-600">NPR {{ number_format((float) ($job->driver_earning ?? $job->base_price ?? 0)) }}</p>
                        <span class="text-xs text-gray-400">{{ number_format((float) ($job->total_distance ?? 0), 1) }} km</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">No unassigned dispatches in the marketplace right now.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Pickups Section -->
    @if(!empty($recentPickups) && count($recentPickups) > 0)
    <div class="kwdc-surface-card space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-orange-500"></i>
                <h2 class="font-bold text-gray-900 text-base">Scheduled Pickup Transfers</h2>
            </div>
            <a href="{{ route('driver.pickups') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All Pickups</a>
        </div>
        <div class="space-y-3">
            @foreach($recentPickups as $pickup)
            <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('pickup.show', $pickup->id) }}">
                <div>
                    <p class="font-bold text-gray-900 text-sm">Pickup {{ $pickup->tracking_id ?? ('#' . $pickup->id) }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        {{ $pickup->pickup_address ?? $pickup->notes ?? 'Kathmandu Location' }}
                        @if($pickup->notes && $pickup->notes !== $pickup->pickup_address)
                            &bull; <span class="italic text-gray-500">{{ $pickup->notes }}</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                        {{ ucfirst(str_replace('_', ' ', $pickup->status ?? 'pending')) }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ optional($pickup->created_at)->format('M d, Y') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
