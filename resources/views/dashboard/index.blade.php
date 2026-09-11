@extends('layouts.app')

@section('title', 'Executive Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .kwdc-kpi-val {
        font-feature-settings: "tnum";
        font-variant-numeric: tabular-nums;
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $role = $role ?? $user->role ?? 'client';

    $roleTitles = [
        'admin' => 'Operations Administrator',
        'client' => 'Enterprise Logistics Client',
        'driver' => 'Fleet Logistics Driver',
        'property_owner' => 'Warehouse Property Partner',
        'equipment_owner' => 'Heavy Equipment Provider',
        'security_agency' => 'Licensed Security Agency',
    ];
    $roleTitle = $roleTitles[$role] ?? ucwords(str_replace('_', ' ', $role));

    $quickActions = [];
    if ($role === 'admin') {
        $quickActions = [
            ['label' => 'Pending Approvals', 'icon' => 'clock', 'route' => route('admin.pending'), 'badge' => $stats['pending_requests'] ?? null],
            ['label' => 'Dispatch Logistics', 'icon' => 'truck-fast', 'route' => route('admin.dispatch'), 'badge' => null],
            ['label' => 'Manage Drivers', 'icon' => 'id-card', 'route' => route('admin.drivers'), 'badge' => null],
            ['label' => 'Warehouse Hubs', 'icon' => 'warehouse', 'route' => route('warehouses.index'), 'badge' => null],
        ];
    } elseif ($role === 'client') {
        $quickActions = [
            ['label' => 'Request Warehouse Space', 'icon' => 'plus-circle', 'route' => route('my-requests.create'), 'badge' => null],
            ['label' => 'Book Dispatch Order', 'icon' => 'truck-arrow-right', 'route' => route('dispatch.direct-create'), 'badge' => null],
            ['label' => 'Schedule Pickup', 'icon' => 'boxes-packing', 'route' => route('pickup.direct-create'), 'badge' => null],
            ['label' => 'My Invoices', 'icon' => 'receipt', 'route' => route('invoices.client-index'), 'badge' => $stats['pending_invoices'] ?? null],
        ];
    } elseif ($role === 'driver') {
        $quickActions = [
            ['label' => 'Available Shipments', 'icon' => 'magnifying-glass-location', 'route' => route('driver.available-jobs'), 'badge' => $stats['available_jobs'] ?? null],
            ['label' => 'My Active Deliveries', 'icon' => 'route', 'route' => route('driver.jobs'), 'badge' => $stats['active_jobs'] ?? null],
            ['label' => 'Pickup Assignments', 'icon' => 'boxes-stacked', 'route' => route('driver.pickups'), 'badge' => null],
            ['label' => 'Earnings Ledger', 'icon' => 'wallet', 'route' => route('driver.earnings'), 'badge' => null],
        ];
    } elseif ($role === 'equipment_owner') {
        $quickActions = [
            ['label' => 'Register Equipment', 'icon' => 'plus-circle', 'route' => route('equipment.register'), 'badge' => null],
            ['label' => 'Equipment Fleet', 'icon' => 'gear', 'route' => route('equipment.list'), 'badge' => null],
            ['label' => 'Rental Job Requests', 'icon' => 'clipboard-check', 'route' => route('equipment.jobs.requests'), 'badge' => $stats['job_requests'] ?? null],
            ['label' => 'Rental Earnings', 'icon' => 'coins', 'route' => route('equipment.jobs.earnings'), 'badge' => null],
        ];
    } elseif ($role === 'property_owner') {
        $quickActions = [
            ['label' => 'List New Warehouse', 'icon' => 'plus-circle', 'route' => route('warehouses.create'), 'badge' => null],
            ['label' => 'Storage Requests', 'icon' => 'clipboard-list', 'route' => route('property.requests.index'), 'badge' => $stats['pending_requests'] ?? null],
            ['label' => 'Warehouse Analytics', 'icon' => 'chart-line', 'route' => route('property.analytics'), 'badge' => null],
            ['label' => 'My Properties', 'icon' => 'building', 'route' => route('property.approved'), 'badge' => null],
        ];
    } elseif ($role === 'security_agency') {
        $quickActions = [
            ['label' => 'Security Dashboard', 'icon' => 'shield-halved', 'route' => route('security.dashboard'), 'badge' => null],
            ['label' => 'Deploy Personnel', 'icon' => 'user-shield', 'route' => route('security.dashboard'), 'badge' => null],
            ['label' => 'Surveillance Logs', 'icon' => 'video', 'route' => route('security.dashboard'), 'badge' => null],
        ];
    }

    // Curate clean, high-impact KPI cards per role
    $kpiList = [];
    if ($role === 'admin') {
        $kpiList = [
            ['label' => 'Approved Warehouses', 'val' => $stats['approved_warehouses'] ?? 0, 'icon' => 'warehouse', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => ($stats['warehouses'] ?? 0) . ' Total Listed'],
            ['label' => 'Active Dispatches', 'val' => ($stats['total_dispatches'] ?? 0) - ($stats['completed_dispatches'] ?? 0), 'icon' => 'truck-fast', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => ($stats['completed_dispatches'] ?? 0) . ' Delivered'],
            ['label' => 'Pending Approvals', 'val' => ($stats['pending_warehouses'] ?? 0) + ($stats['pending_requests'] ?? 0), 'icon' => 'clock', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Action required'],
            ['label' => 'Fleet & Drivers', 'val' => ($stats['drivers'] ?? 0) + ($stats['vehicles'] ?? 0), 'icon' => 'users-gear', 'bg' => '#f5f3ff', 'fg' => '#7c3aed', 'sub' => ($stats['drivers'] ?? 0) . ' Verified Drivers'],
        ];
    } elseif ($role === 'client') {
        $kpiList = [
            ['label' => 'Warehouse Requests', 'val' => $stats['active_requests'] ?? 0, 'icon' => 'warehouse', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => 'Active storage leases'],
            ['label' => 'Dispatches in Transit', 'val' => $stats['pending_dispatches'] ?? 0, 'icon' => 'truck-moving', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => ($stats['completed_dispatches'] ?? 0) . ' Completed'],
            ['label' => 'Scheduled Pickups', 'val' => $stats['pickups'] ?? 0, 'icon' => 'box-open', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Logistics transfers'],
            ['label' => 'Pending Invoices', 'val' => $stats['pending_invoices'] ?? 0, 'icon' => 'receipt', 'bg' => '#fdf2f8', 'fg' => '#db2777', 'sub' => ($stats['pending_invoices'] ?? 0) > 0 ? 'Awaiting Payment' : 'All clear'],
        ];
    } elseif ($role === 'driver') {
        $kpiList = [
            ['label' => 'Active Deliveries', 'val' => $stats['active_jobs'] ?? 0, 'icon' => 'route', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => 'In progress right now'],
            ['label' => 'Completed Trips', 'val' => $stats['completed_jobs'] ?? 0, 'icon' => 'circle-check', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => 'Lifetime deliveries'],
            ['label' => 'Total Earnings', 'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)), 'icon' => 'wallet', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Net driver revenue'],
            ['label' => 'Driver Rating', 'val' => number_format((float) ($stats['rating'] > 0 ? $stats['rating'] : 5.0), 1) . ' ★', 'icon' => 'star', 'bg' => '#f5f3ff', 'fg' => '#7c3aed', 'sub' => 'High quality score'],
        ];
    } elseif ($role === 'property_owner') {
        $kpiList = [
            ['label' => 'My Properties', 'val' => $stats['my_properties'] ?? 0, 'icon' => 'building', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => ($stats['approved_properties'] ?? 0) . ' Approved & Live'],
            ['label' => 'Space Requests', 'val' => $stats['total_requests'] ?? 0, 'icon' => 'clipboard-list', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => ($stats['pending_requests'] ?? 0) . ' Pending review'],
            ['label' => 'Leased Storage', 'val' => $stats['approved_requests'] ?? 0, 'icon' => 'cubes', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Active client leases'],
            ['label' => 'Rental Revenue', 'val' => 'NPR ' . number_format((float) ($stats['total_revenue'] ?? 0)), 'icon' => 'money-bill-trend-up', 'bg' => '#f0fdfa', 'fg' => '#0f766e', 'sub' => 'Accumulated rental'],
        ];
    } elseif ($role === 'equipment_owner') {
        $kpiList = [
            ['label' => 'Registered Fleet', 'val' => $stats['my_equipment'] ?? 0, 'icon' => 'gear', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => ($stats['available_equipment'] ?? 0) . ' Available for Rent'],
            ['label' => 'Active Job Leases', 'val' => $stats['active_jobs'] ?? 0, 'icon' => 'hard-hat', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => 'Currently deployed'],
            ['label' => 'Pending Inquiries', 'val' => $stats['job_requests'] ?? 0, 'icon' => 'clock', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Needs owner review'],
            ['label' => 'Rental Earnings', 'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)), 'icon' => 'coins', 'bg' => '#f5f3ff', 'fg' => '#7c3aed', 'sub' => 'Completed jobs'],
        ];
    } elseif ($role === 'security_agency') {
        $agencyExists = $stats['agency_exists'] ?? false;
        $kpiList = [
            ['label' => 'Security Personnel', 'val' => $stats['personnel_count'] ?? 0, 'icon' => 'user-shield', 'bg' => '#eff6ff', 'fg' => '#2563eb', 'sub' => 'Active licensed guards'],
            ['label' => 'Facility Assignments', 'val' => $stats['assignments'] ?? 0, 'icon' => 'building-shield', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => ($stats['active_assignments'] ?? 0) . ' Active Posts'],
            ['label' => 'Secured High-Value Goods', 'val' => $stats['goods_count'] ?? 0, 'icon' => 'box-archive', 'bg' => '#fffbeb', 'fg' => '#d97706', 'sub' => 'Verified items under watch'],
            ['label' => 'Incidents Logged', 'val' => $stats['incidents_reported'] ?? 0, 'icon' => 'shield-check', 'bg' => '#ecfdf5', 'fg' => '#059669', 'sub' => 'Incident-free operations'],
        ];
    }

    // Chart preparation
    $weeklyEarningLabels = collect($weeklyEarnings ?? [])->pluck('day')->values();
    $weeklyEarningValues = collect($weeklyEarnings ?? [])->pluck('amount')->map(fn ($val) => round((float) $val, 2))->values();

    $chartTitle = 'Operational Activity Trend';
    if ($role === 'driver' && $weeklyEarningValues->isNotEmpty()) {
        $chartTitle = 'Weekly Payout Trend (NPR)';
    } elseif ($role === 'property_owner' || $role === 'equipment_owner') {
        $chartTitle = 'Revenue & Utilization Overview';
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Birthday Banner if applicable -->
    @if($isBirthday ?? false)
    <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/10 border border-amber-200 p-5 rounded-3xl flex items-center gap-4 text-amber-900 shadow-sm">
        <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
            <i class="fas fa-cake-candles"></i>
        </div>
        <div>
            <p class="font-bold text-base">{{ $birthdayMessage ?? 'Happy Birthday!' }}</p>
            <p class="text-xs text-amber-800">Wishing you a smooth and prosperous logistics operations year ahead.</p>
        </div>
    </div>
    @endif

    <!-- Apple-style Hero Banner -->
    <div class="kwdc-dashboard-hero flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2.5 mb-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200">
                    {{ $roleTitle }}
                </span>
                <span class="text-xs text-gray-400 font-medium">KTM-WDC Logistics System</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 tracking-tight">
                Namaste, {{ $user->name }}
            </h1>
            <p class="text-sm text-gray-500 mt-1 max-w-xl">
                Real-time operational command center. Monitor shipments, facility occupancies, workflows, and automated dispatches across the Kathmandu Valley network.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="kwdc-beacon-container">
                <span class="kwdc-beacon-dot"></span>
                <span>Live & Operational</span>
            </div>
            <div class="px-4 py-2 rounded-full bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-600">
                <i class="far fa-calendar-alt text-gray-400 mr-1.5"></i> {{ now()->format('D, M j, Y') }}
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    @if(!empty($quickActions))
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
        @foreach($quickActions as $action)
        <a href="{{ $action['route'] }}" class="kwdc-action-pill whitespace-nowrap">
            <i class="fas fa-{{ $action['icon'] }} text-orange-500"></i>
            <span>{{ $action['label'] }}</span>
            @if(!empty($action['badge']) && $action['badge'] > 0)
                <span class="ml-1 px-2 py-0.5 text-xs font-extrabold rounded-full bg-orange-100 text-orange-800">
                    {{ $action['badge'] }}
                </span>
            @endif
        </a>
        @endforeach
    </div>
    @endif

    <!-- Sculpted KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($kpiList as $kpi)
        <div class="kwdc-kpi-card">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 kwdc-kpi-val">{{ $kpi['val'] }}</p>
                </div>
                <div class="kwdc-kpi-icon" style="background: {{ $kpi['bg'] }}; color: {{ $kpi['fg'] }};">
                    <i class="fas fa-{{ $kpi['icon'] }}"></i>
                </div>
            </div>
            <div class="pt-4 mt-2 border-t border-gray-50 flex items-center justify-between text-xs text-gray-500">
                <span>{{ $kpi['sub'] }}</span>
                <i class="fas fa-arrow-up-right text-gray-300"></i>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Analytics Visualizer Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line/Bar Chart -->
        <div class="lg:col-span-2 kwdc-chart-surface space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ $chartTitle }}</h2>
                    <p class="text-xs text-gray-400">Weekly throughput and operational velocity</p>
                </div>
                <div class="p-2 rounded-xl bg-gray-50 border border-gray-100 text-gray-500 text-xs font-semibold">
                    <i class="fas fa-chart-line text-orange-500 mr-1"></i> Live Metric
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full relative">
                <canvas id="dashboardPrimaryChart"></canvas>
            </div>
        </div>

        <!-- Activity Mix Doughnut Chart -->
        <div class="kwdc-chart-surface space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Workload Distribution</h2>
                    <p class="text-xs text-gray-400">Proportional resource allocation</p>
                </div>
                <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                <canvas id="dashboardActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Role-Specific Operational Feeds & Data Tables -->
    <div class="space-y-6">
        {{-- ================= ADMIN FEEDS ================= --}}
        @if($role === 'admin')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dispatches in Motion -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-truck-moving text-orange-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Active Dispatches</h3>
                    </div>
                    <a href="{{ route('admin.dispatch') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentDispatches ?? [] as $dispatch)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $dispatch->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Dispatch #{{ $dispatch->tracking_id ?? $dispatch->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ optional($dispatch->client)->name ?? 'Client' }} &bull; Driver: {{ optional($dispatch->driver)->name ?? 'Unassigned' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                {{ ucfirst(str_replace('_', ' ', $dispatch->status ?? 'pending')) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">NPR {{ number_format((float) ($dispatch->base_price ?? 0)) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No active dispatches found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Review Queue -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-amber-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Warehouse Approvals Queue</h3>
                    </div>
                    <a href="{{ route('admin.pending') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Review Queue</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentRequests ?? [] as $req)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('warehouse-requests.show', $req->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Request #{{ $req->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ optional($req->client)->name ?? 'Client' }} &bull; {{ optional($req->warehouse)->name ?? 'Warehouse' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                {{ ucfirst($req->status ?? 'pending') }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ optional($req->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">All review queues are clear.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ================= CLIENT FEEDS ================= --}}
        @if($role === 'client')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Shipments -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-truck text-orange-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Active Shipments</h3>
                    </div>
                    <a href="{{ route('dispatch.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All Orders</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentDispatches ?? [] as $dispatch)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $dispatch->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Shipment #{{ $dispatch->tracking_id ?? $dispatch->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Destination: {{ Str::limit($dispatch->destination_address ?? 'Kathmandu Hub', 32) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                {{ ucfirst(str_replace('_', ' ', $dispatch->status ?? 'pending')) }}
                            </span>
                            <a href="{{ route('tracking.shipment', $dispatch->id) }}" class="p-2 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100 transition text-xs font-bold">
                                <i class="fas fa-location-crosshairs"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No active shipments in transit.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Storage Leases -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-warehouse text-blue-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Warehouse Storage Space</h3>
                    </div>
                    <a href="{{ route('my-requests.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All Requests</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentRequests ?? [] as $req)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('warehouse-requests.show', $req->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ optional($req->warehouse)->name ?? ('Request #' . $req->id) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Space: {{ $req->space_sqft ?? 0 }} sq.ft &bull; {{ optional($req->created_at)->format('M d, Y') }}</p>
                        </div>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ ($req->status ?? 'pending') === 'approved' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($req->status ?? 'pending') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No warehouse requests yet. Click "Request Warehouse Space" above to begin.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ================= DRIVER FEEDS ================= --}}
        @if($role === 'driver')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Jobs in Transit -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-route text-orange-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Assigned Dispatches</h3>
                    </div>
                    <a href="{{ route('driver.jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($activeJobs ?? [] as $job)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $job->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Order #{{ $job->tracking_id ?? $job->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">To: {{ Str::limit($job->destination_address ?? 'Kathmandu', 30) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ ucfirst(str_replace('_', ' ', $job->status ?? 'assigned')) }}
                            </span>
                            <p class="text-xs font-bold text-emerald-600 mt-1">NPR {{ number_format((float) ($job->driver_earning ?? 0)) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No active delivery assignments in progress.</p>
                    @endforelse
                </div>
            </div>

            <!-- Available Job Pool -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-hand-holding-dollar text-emerald-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Available Job Marketplace</h3>
                    </div>
                    <a href="{{ route('driver.available-jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Browse Jobs</a>
                </div>
                <div class="space-y-3">
                    @forelse($availableJobs ?? [] as $job)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4 cursor-pointer" data-record-url="{{ route('dispatch.show', $job->id) }}">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Trip #{{ $job->id }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Pickup: {{ Str::limit($job->pickup_address ?? 'Kathmandu', 28) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-emerald-600">NPR {{ number_format((float) ($job->driver_earning ?? $job->base_price ?? 0)) }}</p>
                            <span class="text-xs text-gray-400">{{ number_format((float) ($job->total_distance ?? 0), 1) }} km</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No pending orders in the marketplace right now.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ================= PROPERTY OWNER FEEDS ================= --}}
        @if($role === 'property_owner')
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-orange-500"></i>
                    <h3 class="font-bold text-gray-900 text-base">My Warehouse Facilities</h3>
                </div>
                <a href="{{ route('warehouses.create') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">+ Add Property</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($myWarehouses ?? [] as $wh)
                <div class="kwdc-feed-item space-y-3 cursor-pointer" data-record-url="{{ route('warehouses.show', $wh->id) }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">{{ $wh->name }}</h4>
                            <p class="text-xs text-gray-500"><i class="fas fa-location-dot text-orange-500 mr-1"></i>{{ $wh->address ?? $wh->city ?? 'Kathmandu' }}</p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $wh->status === 'approved' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($wh->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 flex items-center justify-between pt-2 border-t border-gray-100">
                        <span>Capacity: <strong>{{ number_format((float) ($wh->capacity ?? 0)) }} m³</strong></span>
                        <span class="text-orange-600 font-bold">Inspect Details &rarr;</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-4 col-span-3 text-center">No warehouses listed yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ================= EQUIPMENT OWNER FEEDS ================= --}}
        @if($role === 'equipment_owner')
        <div class="kwdc-surface-card space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-gear text-orange-500"></i>
                    <h3 class="font-bold text-gray-900 text-base">Registered Equipment Fleet</h3>
                </div>
                <a href="{{ route('equipment.register') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">+ Register Machine</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($myEquipment ?? [] as $eq)
                <div class="kwdc-feed-item space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">{{ $eq->name ?? $eq->equipment_name ?? 'Machinery' }}</h4>
                            <p class="text-xs text-gray-500">{{ $eq->type ?? $eq->category ?? 'Logistics' }} &bull; {{ $eq->model ?? 'Standard' }}</p>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ ($eq->status ?? 'available') === 'available' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ ucfirst($eq->status ?? 'available') }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-600 flex items-center justify-between pt-2 border-t border-gray-100">
                        <span>Rate: <strong>NPR {{ number_format((float) ($eq->daily_rate ?? $eq->price ?? 0)) }}/day</strong></span>
                        <span class="text-gray-400">ID #{{ $eq->id }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-4 col-span-3 text-center">No equipment registered yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        {{-- ================= SECURITY AGENCY FEEDS ================= --}}
        @if($role === 'security_agency')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Facility Assignments -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-building-shield text-blue-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Facility Surveillance Posts</h3>
                    </div>
                    <a href="{{ route('security.dashboard') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Security Portal</a>
                </div>
                <div class="space-y-3">
                    @forelse($assignments ?? [] as $assign)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ optional($assign->warehouse)->name ?? ('Assignment #' . $assign->id) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Status: {{ ucfirst($assign->status ?? 'active') }}</p>
                        </div>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Secured
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No active warehouse assignments.</p>
                    @endforelse
                </div>
            </div>

            <!-- Guard Personnel -->
            <div class="kwdc-surface-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-shield text-emerald-500"></i>
                        <h3 class="font-bold text-gray-900 text-base">Deployed Personnel</h3>
                    </div>
                    <a href="{{ route('security.dashboard') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Manage Guards</a>
                </div>
                <div class="space-y-3">
                    @forelse($personnel ?? [] as $guard)
                    <div class="kwdc-feed-item flex items-center justify-between gap-4">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">{{ $guard->name ?? 'Guard' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Badge #{{ $guard->badge_number ?? $guard->id }} &bull; {{ $guard->phone ?? 'On duty' }}</p>
                        </div>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                            {{ ucfirst($guard->status ?? 'active') }}
                        </span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-4 text-center">No personnel deployed.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDriverEarnings = @json($role === 'driver' && $weeklyEarningValues->isNotEmpty());
    const primaryLabels = isDriverEarnings 
        ? @json($weeklyEarningLabels) 
        : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
    const primaryValues = isDriverEarnings 
        ? @json($weeklyEarningValues) 
        : [14, 22, 19, 27, 34, 42, 38];

    // Primary Chart (Smooth Line with Gradient)
    const primaryCanvas = document.getElementById('dashboardPrimaryChart');
    if (primaryCanvas) {
        const ctx = primaryCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(249, 115, 22, 0.22)');
        gradient.addColorStop(1, 'rgba(249, 115, 22, 0.00)');

        new Chart(primaryCanvas, {
            type: 'line',
            data: {
                labels: primaryLabels,
                datasets: [{
                    label: isDriverEarnings ? 'Earnings (NPR)' : 'Activity Volume',
                    data: primaryValues,
                    backgroundColor: gradient,
                    borderColor: '#f97316',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f97316',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.36,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { weight: 'bold', size: 12 },
                        callbacks: {
                            label: function(context) {
                                return (isDriverEarnings ? ' NPR ' : ' Units: ') + Number(context.parsed.y).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Doughnut Chart (Activity Mix)
    const activityCanvas = document.getElementById('dashboardActivityChart');
    if (activityCanvas) {
        new Chart(activityCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Warehouses', 'Dispatches', 'Pickups', 'Equipment'],
                datasets: [{
                    data: [35, 40, 15, 10],
                    backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '76%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 16,
                            font: { size: 11, weight: '600' },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
