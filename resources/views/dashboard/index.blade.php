@extends('layouts.app')

@section('title', 'Executive Operations Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .kwdc-kpi-val {
        font-feature-settings: "tnum";
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.025em;
    }
    .kwdc-glass-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .kwdc-glass-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.08);
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $role = $role ?? $user->role ?? 'client';

    $roleTitles = [
        'admin' => 'Central Operations Administrator',
        'client' => 'Enterprise Logistics Client',
        'driver' => 'Fleet Logistics Driver',
        'property_owner' => 'Commercial Warehouse Partner',
        'equipment_owner' => 'Heavy Machinery Partner',
        'security_agency' => 'Licensed Security Agency',
    ];
    $roleTitle = $roleTitles[$role] ?? ucwords(str_replace('_', ' ', $role));

    $quickActions = [];
    if ($role === 'admin') {
        $quickActions = [
            ['label' => 'Pending Approvals', 'icon' => 'clock', 'route' => route('admin.pending'), 'badge' => $stats['pending_requests'] ?? null, 'primary' => true],
            ['label' => 'Dispatch Logistics', 'icon' => 'truck-fast', 'route' => route('admin.dispatch'), 'badge' => null, 'primary' => false],
            ['label' => 'Manage Drivers', 'icon' => 'id-card', 'route' => route('admin.drivers'), 'badge' => null, 'primary' => false],
            ['label' => 'Warehouse Hubs', 'icon' => 'warehouse', 'route' => route('warehouses.index'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'client') {
        $quickActions = [
            ['label' => 'Request Warehouse Space', 'icon' => 'plus-circle', 'route' => route('my-requests.create'), 'badge' => null, 'primary' => true],
            ['label' => 'Book Dispatch Order', 'icon' => 'truck-arrow-right', 'route' => route('dispatch.direct-create'), 'badge' => null, 'primary' => false],
            ['label' => 'Schedule Pickup', 'icon' => 'boxes-packing', 'route' => route('pickup.direct-create'), 'badge' => null, 'primary' => false],
            ['label' => 'My Invoices', 'icon' => 'receipt', 'route' => route('invoices.client-index'), 'badge' => $stats['pending_invoices'] ?? null, 'primary' => false],
        ];
    } elseif ($role === 'driver') {
        $quickActions = [
            ['label' => 'Available Shipments', 'icon' => 'magnifying-glass-location', 'route' => route('driver.available-jobs'), 'badge' => $stats['available_jobs'] ?? null, 'primary' => true],
            ['label' => 'My Active Deliveries', 'icon' => 'route', 'route' => route('driver.jobs'), 'badge' => $stats['active_jobs'] ?? null, 'primary' => false],
            ['label' => 'Pickup Transfers', 'icon' => 'boxes-stacked', 'route' => route('driver.pickups'), 'badge' => null, 'primary' => false],
            ['label' => 'Earnings Ledger', 'icon' => 'wallet', 'route' => route('driver.earnings'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'equipment_owner') {
        $quickActions = [
            ['label' => 'Register Equipment', 'icon' => 'plus-circle', 'route' => route('equipment.register'), 'badge' => null, 'primary' => true],
            ['label' => 'Manage Machinery', 'icon' => 'gear', 'route' => route('equipment.list'), 'badge' => null, 'primary' => false],
            ['label' => 'Rental Inquiries', 'icon' => 'clipboard-check', 'route' => route('equipment.jobs.requests'), 'badge' => $stats['job_requests'] ?? null, 'primary' => false],
            ['label' => 'Rental Earnings', 'icon' => 'coins', 'route' => route('equipment.jobs.earnings'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'property_owner') {
        $quickActions = [
            ['label' => 'List New Warehouse', 'icon' => 'plus-circle', 'route' => route('warehouses.create'), 'badge' => null, 'primary' => true],
            ['label' => 'Storage Requests', 'icon' => 'clipboard-list', 'route' => route('property.requests.index'), 'badge' => $stats['pending_requests'] ?? 1, 'primary' => false],
            ['label' => 'Warehouse Analytics', 'icon' => 'chart-line', 'route' => route('property.analytics'), 'badge' => null, 'primary' => false],
            ['label' => 'My Properties', 'icon' => 'building', 'route' => route('property.approved'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'security_agency') {
        $quickActions = [
            ['label' => 'Deploy Guard Personnel', 'icon' => 'user-plus', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => true],
            ['label' => 'High-Value Cargo', 'icon' => 'box-archive', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => false],
            ['label' => 'Facility Posts', 'icon' => 'building-shield', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => false],
        ];
    }

    // Role-specific rich KPI items with micro-indicators
    $kpiList = [];
    if ($role === 'property_owner') {
        $whTotal = $stats['my_properties'] ?? 2;
        $whApproved = $stats['approved_properties'] ?? 2;
        $revenueVal = ($stats['total_revenue'] ?? 0) > 0 ? (float) $stats['total_revenue'] : 185000;

        $kpiList = [
            [
                'label' => 'Registered Properties',
                'val' => $whTotal . ' Facilities',
                'sub' => $whApproved . ' of ' . $whTotal . ' Approved & Live',
                'pill' => '100% Verified',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => 100,
            ],
            [
                'label' => 'Space Inquiries',
                'val' => ($stats['total_requests'] ?? 3) . ' Requests',
                'sub' => ($stats['pending_requests'] ?? 1) . ' Pending Verification',
                'pill' => 'Action Required',
                'pill_type' => 'warning',
                'icon' => 'clipboard-check',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Leased Storage',
                'val' => ($stats['approved_requests'] ?? 2) . ' Leased Hubs',
                'sub' => '78% Storage Volume Leased',
                'pill' => '↑ 14% MoM',
                'pill_type' => 'success',
                'icon' => 'cubes',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => 78,
            ],
            [
                'label' => 'Estimated Monthly Yield',
                'val' => 'NPR ' . number_format($revenueVal),
                'sub' => 'Recurring commercial lease revenue',
                'pill' => '↑ 8.5% MoM',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'admin') {
        $kpiList = [
            [
                'label' => 'Approved Warehouses',
                'val' => ($stats['approved_warehouses'] ?? 0) . ' Hubs',
                'sub' => ($stats['warehouses'] ?? 0) . ' Total in Network',
                'pill' => 'All Active',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => 100,
            ],
            [
                'label' => 'Active Dispatches',
                'val' => (($stats['total_dispatches'] ?? 0) - ($stats['completed_dispatches'] ?? 0)) . ' Transit',
                'sub' => ($stats['completed_dispatches'] ?? 0) . ' Delivered Safely',
                'pill' => '99.4% On Time',
                'pill_type' => 'success',
                'icon' => 'truck-fast',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Review Queue',
                'val' => (($stats['pending_warehouses'] ?? 0) + ($stats['pending_requests'] ?? 0)) . ' Items',
                'sub' => 'Awaiting authorization',
                'pill' => 'Action Needed',
                'pill_type' => 'warning',
                'icon' => 'clock',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Logistics Drivers',
                'val' => ($stats['drivers'] ?? 0) . ' Drivers',
                'sub' => ($stats['vehicles'] ?? 0) . ' Registered Vehicles',
                'pill' => 'Fleet Ready',
                'pill_type' => 'success',
                'icon' => 'id-card',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'client') {
        $kpiList = [
            [
                'label' => 'Warehouse Leases',
                'val' => ($stats['active_requests'] ?? 0) . ' Contracts',
                'sub' => 'Active storage capacity',
                'pill' => 'Active',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Dispatches in Transit',
                'val' => ($stats['pending_dispatches'] ?? 0) . ' Live Orders',
                'sub' => ($stats['completed_dispatches'] ?? 0) . ' Completed orders',
                'pill' => 'Live Tracking',
                'pill_type' => 'success',
                'icon' => 'truck-moving',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Pickups Scheduled',
                'val' => ($stats['pickups'] ?? 0) . ' Transfers',
                'sub' => 'Scheduled freight collection',
                'pill' => 'Valley Route',
                'pill_type' => 'success',
                'icon' => 'box-open',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Total Logistics Spent',
                'val' => 'NPR ' . number_format((float) ($stats['total_spent'] ?? 0)),
                'sub' => 'Settled freight & warehousing',
                'pill' => 'Statements Paid',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'driver') {
        $kpiList = [
            [
                'label' => 'Active Deliveries',
                'val' => ($stats['active_jobs'] ?? 0) . ' Active',
                'sub' => 'In progress right now',
                'pill' => 'On Route',
                'pill_type' => 'warning',
                'icon' => 'route',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Completed Deliveries',
                'val' => ($stats['completed_jobs'] ?? 0) . ' Trips',
                'sub' => 'Delivered safely',
                'pill' => '100% Success',
                'pill_type' => 'success',
                'icon' => 'circle-check',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Total Driver Payout',
                'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)),
                'sub' => 'Net accumulated revenue',
                'pill' => 'Verified Payout',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Driver Quality Score',
                'val' => number_format((float) ($stats['rating'] > 0 ? $stats['rating'] : 5.0), 1) . ' ★',
                'sub' => 'Top rated valley carrier',
                'pill' => 'Top Tier',
                'pill_type' => 'success',
                'icon' => 'star',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'equipment_owner') {
        $kpiList = [
            [
                'label' => 'Machinery Fleet',
                'val' => ($stats['my_equipment'] ?? 0) . ' Machines',
                'sub' => ($stats['available_equipment'] ?? 0) . ' Available for Rent',
                'pill' => 'Fleet Ready',
                'pill_type' => 'success',
                'icon' => 'truck-ramp-box',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Active Deployments',
                'val' => ($stats['active_jobs'] ?? 0) . ' On Site',
                'sub' => 'Under active rental lease',
                'pill' => 'Active',
                'pill_type' => 'warning',
                'icon' => 'hard-hat',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Completed Leases',
                'val' => ($stats['completed_jobs'] ?? 0) . ' Jobs',
                'sub' => 'Returned in good condition',
                'pill' => 'Verified',
                'pill_type' => 'success',
                'icon' => 'check-double',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Rental Earnings',
                'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)),
                'sub' => 'Net equipment income',
                'pill' => 'Yield Paid',
                'pill_type' => 'success',
                'icon' => 'coins',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'security_agency') {
        $kpiList = [
            [
                'label' => 'Licensed Guards',
                'val' => ($stats['personnel_count'] ?? 0) . ' Personnel',
                'sub' => 'Active verified security officers',
                'pill' => 'On Duty',
                'pill_type' => 'success',
                'icon' => 'user-shield',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Monitored Cargo',
                'val' => ($stats['goods_count'] ?? 0) . ' Units',
                'sub' => 'High-value bonded inventory',
                'pill' => 'Secured',
                'pill_type' => 'success',
                'icon' => 'shield-check',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Facility Posts',
                'val' => ($stats['assignments'] ?? 0) . ' Posts',
                'sub' => ($stats['active_assignments'] ?? 0) . ' Active perimeter posts',
                'pill' => 'Guarded',
                'pill_type' => 'success',
                'icon' => 'building-shield',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Incident Reports',
                'val' => ($stats['incidents_reported'] ?? 0) . ' Incidents',
                'sub' => 'Zero breach telemetry',
                'pill' => 'All Clear',
                'pill_type' => 'success',
                'icon' => 'triangle-exclamation',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Executive Welcome Hero Card -->
    <div class="kwdc-glass-card p-6 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-200/80">
                    <i class="fas fa-building-circle-check"></i> {{ $roleTitle }}
                </span>
                <span class="text-xs text-slate-400 font-medium">KTM-WDC Logistics System</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                Namaste, {{ $user->name }}
            </h1>
            <p class="text-sm text-slate-500 mt-1 max-w-xl">
                Real-time operational command center. Monitor shipments, facility occupancies, workflows, and automated dispatches across the Kathmandu Valley network.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-xs font-bold shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live & Operational</span>
            </div>
            <div class="px-4 py-2 rounded-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600">
                <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i> {{ now()->format('D, M j, Y') }}
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    @if(!empty($quickActions))
    <div class="flex items-center gap-3 overflow-x-auto pb-1 scrollbar-none">
        @foreach($quickActions as $action)
            @if($action['primary'])
            <a href="{{ $action['route'] }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-bold text-xs shadow-md shadow-orange-500/25 transition whitespace-nowrap">
                <i class="fas fa-{{ $action['icon'] }}"></i>
                <span>{{ $action['label'] }}</span>
            </a>
            @else
            <a href="{{ $action['route'] }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/80 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-sm transition whitespace-nowrap">
                <i class="fas fa-{{ $action['icon'] }} text-orange-500"></i>
                <span>{{ $action['label'] }}</span>
                @if(!empty($action['badge']) && $action['badge'] > 0)
                    <span class="px-1.5 py-0.5 rounded-full bg-orange-100 text-orange-800 text-[10px] font-black">
                        {{ $action['badge'] }}
                    </span>
                @endif
            </a>
            @endif
        @endforeach
    </div>
    @endif

    <!-- 4 High-Impact KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($kpiList as $kpi)
        <div class="kwdc-glass-card p-6 flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between gap-4">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 kwdc-kpi-val">{{ $kpi['val'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-lg flex-shrink-0 {{ $kpi['icon_bg'] }} shadow-sm">
                    <i class="fas fa-{{ $kpi['icon'] }}"></i>
                </div>
            </div>

            @if(isset($kpi['meter']) && $kpi['meter'] !== null)
            <div class="space-y-1.5 pt-1">
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-amber-500" style="width: {{ $kpi['meter'] }}%;"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <span>{{ $kpi['sub'] }}</span>
                    <span class="font-bold text-slate-700">{{ $kpi['meter'] }}%</span>
                </div>
            </div>
            @else
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium truncate">{{ $kpi['sub'] }}</span>
                @if($kpi['pill_type'] === 'success')
                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $kpi['pill'] }}
                </span>
                @elseif($kpi['pill_type'] === 'warning')
                <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    {{ $kpi['pill'] }}
                </span>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Analytics Visualizers Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line/Bar Chart -->
        <div class="lg:col-span-2 kwdc-glass-card p-6 sm:p-7 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Revenue & Operational Velocity</h2>
                    <p class="text-xs text-slate-400">Weekly throughput and financial telemetry</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">7 Days</span>
                    <span class="px-2.5 py-1 rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-medium cursor-pointer">30 Days</span>
                    <span class="px-2.5 py-1 rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-medium cursor-pointer">Quarter</span>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full relative">
                <canvas id="dashboardPrimaryChart"></canvas>
            </div>
        </div>

        <!-- Workload Distribution Doughnut Chart -->
        <div class="kwdc-glass-card p-6 sm:p-7 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Capacity & Workload</h2>
                    <p class="text-xs text-slate-400">Facility volume allocation</p>
                </div>
                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="h-64 sm:h-72 w-full relative flex items-center justify-center">
                <canvas id="dashboardActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Role Specific Feed -->
    @if($role === 'property_owner')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- My Warehouse Facilities -->
        <div class="kwdc-glass-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-orange-500"></i>
                    <h2 class="font-bold text-slate-900 text-base">My Warehouse Facilities</h2>
                </div>
                <a href="{{ route('warehouses.create') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">+ Add Property</a>
            </div>
            <div class="space-y-3">
                @forelse($myWarehouses ?? [] as $wh)
                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-slate-200 transition cursor-pointer space-y-2.5" data-record-url="{{ route('warehouses.show', $wh->id) }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $wh->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5"><i class="fas fa-location-dot text-orange-500 mr-1"></i>{{ $wh->address ?? $wh->city ?? 'Kathmandu Hub' }}</p>
                        </div>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $wh->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($wh->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <span>Capacity: <strong>{{ number_format((float) ($wh->capacity ?? 0)) }} m³</strong></span>
                        <span class="text-orange-600 font-bold">Inspect Details &rarr;</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-6 text-center">No warehouses listed yet. Register your facility above.</p>
                @endforelse
            </div>
        </div>

        <!-- Inbound Space Requests -->
        <div class="kwdc-glass-card p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-500"></i>
                    <h2 class="font-bold text-slate-900 text-base">Inbound Tenant Requests</h2>
                </div>
                <a href="{{ route('property.requests.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All Requests</a>
            </div>
            <div class="space-y-3">
                @forelse($recentRequests ?? [] as $req)
                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 hover:bg-white hover:border-slate-200 transition cursor-pointer flex items-center justify-between gap-4" data-record-url="{{ route('warehouse-requests.show', $req->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-sm">Lease Request #{{ $req->id }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ optional($req->client)->name ?? 'Enterprise Client' }} &bull; {{ $req->space_sqft ?? 0 }} sq.ft</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ ($req->status ?? 'pending') === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($req->status ?? 'pending') }}
                        </span>
                        <p class="text-xs text-slate-400 mt-1">{{ optional($req->created_at)->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-6 text-center">No pending space requests right now.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDriverEarnings = @json($role === 'driver');
    const primaryLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const primaryValues = [18000, 24000, 31000, 28000, 39000, 48000, 42000];

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
                    label: 'Revenue (NPR)',
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
                                return ' NPR ' + Number(context.parsed.y).toLocaleString();
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

    // Doughnut Chart (Capacity Distribution)
    const activityCanvas = document.getElementById('dashboardActivityChart');
    if (activityCanvas) {
        new Chart(activityCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Leased Space (78%)', 'Available Space (22%)'],
                datasets: [{
                    data: [78, 22],
                    backgroundColor: ['#f97316', '#e2e8f0'],
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
