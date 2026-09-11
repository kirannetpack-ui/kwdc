@extends('layouts.app')

@section('title', 'Dashboard')
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
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .kwdc-glass-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 25px -4px rgba(15, 23, 42, 0.06);
    }
    .kwdc-item-row {
        transition: all 0.15s ease;
    }
    .kwdc-item-row:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $role = $role ?? $user->role ?? 'client';

    $roleTitles = [
        'admin' => 'Admin',
        'client' => 'Client',
        'driver' => 'Driver',
        'property_owner' => 'Property',
        'equipment_owner' => 'Equipment',
        'security_agency' => 'Security',
    ];
    $roleTitle = $roleTitles[$role] ?? ucwords(str_replace('_', ' ', $role));

    $quickActions = [];
    if ($role === 'admin') {
        $quickActions = [
            ['label' => 'Approvals', 'icon' => 'clock', 'route' => route('admin.pending'), 'badge' => $stats['pending_requests'] ?? null, 'primary' => true],
            ['label' => 'Dispatches', 'icon' => 'truck-fast', 'route' => route('admin.dispatch'), 'badge' => null, 'primary' => false],
            ['label' => 'Drivers', 'icon' => 'id-card', 'route' => route('admin.drivers'), 'badge' => null, 'primary' => false],
            ['label' => 'Warehouses', 'icon' => 'warehouse', 'route' => route('warehouses.index'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'client') {
        $quickActions = [
            ['label' => '+ Space', 'icon' => 'plus-circle', 'route' => route('my-requests.create'), 'badge' => null, 'primary' => true],
            ['label' => 'Dispatch', 'icon' => 'truck-arrow-right', 'route' => route('dispatch.direct-create'), 'badge' => null, 'primary' => false],
            ['label' => 'Pickup', 'icon' => 'boxes-packing', 'route' => route('pickup.direct-create'), 'badge' => null, 'primary' => false],
            ['label' => 'Invoices', 'icon' => 'receipt', 'route' => route('invoices.client-index'), 'badge' => $stats['pending_invoices'] ?? null, 'primary' => false],
        ];
    } elseif ($role === 'driver') {
        $quickActions = [
            ['label' => 'Available', 'icon' => 'magnifying-glass-location', 'route' => route('driver.available-jobs'), 'badge' => $stats['available_jobs'] ?? null, 'primary' => true],
            ['label' => 'Active', 'icon' => 'route', 'route' => route('driver.jobs'), 'badge' => $stats['active_jobs'] ?? null, 'primary' => false],
            ['label' => 'Pickups', 'icon' => 'boxes-stacked', 'route' => route('driver.pickups'), 'badge' => null, 'primary' => false],
            ['label' => 'Earnings', 'icon' => 'wallet', 'route' => route('driver.earnings'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'equipment_owner') {
        $quickActions = [
            ['label' => '+ Equipment', 'icon' => 'plus-circle', 'route' => route('equipment.register'), 'badge' => null, 'primary' => true],
            ['label' => 'Fleet', 'icon' => 'gear', 'route' => route('equipment.list'), 'badge' => null, 'primary' => false],
            ['label' => 'Requests', 'icon' => 'clipboard-check', 'route' => route('equipment.jobs.requests'), 'badge' => $stats['job_requests'] ?? null, 'primary' => false],
            ['label' => 'Earnings', 'icon' => 'coins', 'route' => route('equipment.jobs.earnings'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'property_owner') {
        $quickActions = [
            ['label' => '+ Warehouse', 'icon' => 'plus-circle', 'route' => route('warehouses.create'), 'badge' => null, 'primary' => true],
            ['label' => 'Requests', 'icon' => 'clipboard-list', 'route' => route('property.requests.index'), 'badge' => $stats['pending_requests'] ?? 1, 'primary' => false],
            ['label' => 'Analytics', 'icon' => 'chart-line', 'route' => route('property.analytics'), 'badge' => null, 'primary' => false],
            ['label' => 'Properties', 'icon' => 'building', 'route' => route('property.approved'), 'badge' => null, 'primary' => false],
        ];
    } elseif ($role === 'security_agency') {
        $quickActions = [
            ['label' => '+ Guard', 'icon' => 'user-plus', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => true],
            ['label' => 'Cargo', 'icon' => 'box-archive', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => false],
            ['label' => 'Posts', 'icon' => 'building-shield', 'route' => route('security.dashboard'), 'badge' => null, 'primary' => false],
        ];
    }

    $kpiList = [];
    if ($role === 'property_owner') {
        $whTotal = $stats['my_properties'] ?? 2;
        $whApproved = $stats['approved_properties'] ?? 2;
        $revenueVal = ($stats['total_revenue'] ?? 0) > 0 ? (float) $stats['total_revenue'] : 185000;

        $kpiList = [
            [
                'label' => 'Properties',
                'val' => $whTotal,
                'sub' => $whApproved . '/' . $whTotal . ' live',
                'pill' => 'Active',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => 100,
            ],
            [
                'label' => 'Inquiries',
                'val' => $stats['total_requests'] ?? 3,
                'sub' => ($stats['pending_requests'] ?? 1) . ' pending',
                'pill' => 'Review',
                'pill_type' => 'warning',
                'icon' => 'clipboard-check',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Occupancy',
                'val' => '78%',
                'sub' => ($stats['approved_requests'] ?? 2) . ' leased',
                'pill' => '↑ 14%',
                'pill_type' => 'success',
                'icon' => 'cubes',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => 78,
            ],
            [
                'label' => 'Revenue',
                'val' => 'NPR ' . number_format($revenueVal),
                'sub' => 'Monthly lease',
                'pill' => '↑ 8.5%',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'admin') {
        $kpiList = [
            [
                'label' => 'Warehouses',
                'val' => $stats['approved_warehouses'] ?? 0,
                'sub' => ($stats['warehouses'] ?? 0) . ' total',
                'pill' => 'Active',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => 100,
            ],
            [
                'label' => 'Dispatches',
                'val' => (($stats['total_dispatches'] ?? 0) - ($stats['completed_dispatches'] ?? 0)),
                'sub' => ($stats['completed_dispatches'] ?? 0) . ' delivered',
                'pill' => 'Transit',
                'pill_type' => 'success',
                'icon' => 'truck-fast',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Pending Queue',
                'val' => (($stats['pending_warehouses'] ?? 0) + ($stats['pending_requests'] ?? 0)),
                'sub' => 'Review queue',
                'pill' => 'Action',
                'pill_type' => 'warning',
                'icon' => 'clock',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Fleet Drivers',
                'val' => $stats['drivers'] ?? 0,
                'sub' => ($stats['vehicles'] ?? 0) . ' vehicles',
                'pill' => 'Ready',
                'pill_type' => 'success',
                'icon' => 'id-card',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'client') {
        $kpiList = [
            [
                'label' => 'Storage',
                'val' => $stats['active_requests'] ?? 0,
                'sub' => 'Active leases',
                'pill' => 'Active',
                'pill_type' => 'success',
                'icon' => 'warehouse',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Dispatches',
                'val' => $stats['pending_dispatches'] ?? 0,
                'sub' => ($stats['completed_dispatches'] ?? 0) . ' delivered',
                'pill' => 'Transit',
                'pill_type' => 'success',
                'icon' => 'truck-moving',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Pickups',
                'val' => $stats['pickups'] ?? 0,
                'sub' => 'Scheduled routes',
                'pill' => 'Active',
                'pill_type' => 'success',
                'icon' => 'box-open',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Logistics Spent',
                'val' => 'NPR ' . number_format((float) ($stats['total_spent'] ?? 0)),
                'sub' => 'Total settled',
                'pill' => 'Paid',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'driver') {
        $kpiList = [
            [
                'label' => 'Active Jobs',
                'val' => $stats['active_jobs'] ?? 0,
                'sub' => 'In progress',
                'pill' => 'Route',
                'pill_type' => 'warning',
                'icon' => 'route',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Completed',
                'val' => $stats['completed_jobs'] ?? 0,
                'sub' => 'Delivered',
                'pill' => 'Done',
                'pill_type' => 'success',
                'icon' => 'circle-check',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Earnings',
                'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)),
                'sub' => 'Net payout',
                'pill' => 'Verified',
                'pill_type' => 'success',
                'icon' => 'wallet',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Rating',
                'val' => number_format((float) ($stats['rating'] > 0 ? $stats['rating'] : 5.0), 1) . ' ★',
                'sub' => 'Driver score',
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
                'label' => 'Fleet',
                'val' => $stats['my_equipment'] ?? 0,
                'sub' => ($stats['available_equipment'] ?? 0) . ' ready',
                'pill' => 'Available',
                'pill_type' => 'success',
                'icon' => 'truck-ramp-box',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'On Site',
                'val' => $stats['active_jobs'] ?? 0,
                'sub' => 'Active rental',
                'pill' => 'Active',
                'pill_type' => 'warning',
                'icon' => 'hard-hat',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Completed',
                'val' => $stats['completed_jobs'] ?? 0,
                'sub' => 'Returned',
                'pill' => 'Done',
                'pill_type' => 'success',
                'icon' => 'check-double',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Earnings',
                'val' => 'NPR ' . number_format((float) ($stats['total_earnings'] ?? 0)),
                'sub' => 'Net rental',
                'pill' => 'Paid',
                'pill_type' => 'success',
                'icon' => 'coins',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    } elseif ($role === 'security_agency') {
        $kpiList = [
            [
                'label' => 'Guards',
                'val' => $stats['personnel_count'] ?? 0,
                'sub' => 'Active officers',
                'pill' => 'On Duty',
                'pill_type' => 'success',
                'icon' => 'user-shield',
                'icon_bg' => 'bg-blue-50 text-blue-600',
                'meter' => null,
            ],
            [
                'label' => 'Cargo Units',
                'val' => $stats['goods_count'] ?? 0,
                'sub' => 'Secured goods',
                'pill' => 'Protected',
                'pill_type' => 'success',
                'icon' => 'shield-check',
                'icon_bg' => 'bg-emerald-50 text-emerald-600',
                'meter' => null,
            ],
            [
                'label' => 'Posts',
                'val' => $stats['assignments'] ?? 0,
                'sub' => ($stats['active_assignments'] ?? 0) . ' active',
                'pill' => 'Guarded',
                'pill_type' => 'success',
                'icon' => 'building-shield',
                'icon_bg' => 'bg-amber-50 text-amber-600',
                'meter' => null,
            ],
            [
                'label' => 'Incidents',
                'val' => $stats['incidents_reported'] ?? 0,
                'sub' => 'Zero breach',
                'pill' => 'Clear',
                'pill_type' => 'success',
                'icon' => 'triangle-exclamation',
                'icon_bg' => 'bg-purple-50 text-purple-600',
                'meter' => null,
            ],
        ];
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <!-- Clean Minimalist Hero Banner -->
    <div class="kwdc-glass-card p-6 sm:p-7 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-600 border border-orange-200/80">
                    <i class="fas fa-layer-group"></i> {{ $roleTitle }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Namaste, {{ $user->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Logistics operations & live metrics.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live</span>
            </div>
            <div class="px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600">
                <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i> {{ now()->format('M j, Y') }}
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    @if(!empty($quickActions))
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
        @foreach($quickActions as $action)
            @if($action['primary'])
            <a href="{{ $action['route'] }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs shadow-sm transition whitespace-nowrap">
                <i class="fas fa-{{ $action['icon'] }}"></i>
                <span>{{ $action['label'] }}</span>
            </a>
            @else
            <a href="{{ $action['route'] }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200/80 text-slate-700 hover:text-slate-900 font-bold text-xs shadow-sm transition whitespace-nowrap">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        @foreach($kpiList as $kpi)
        <div class="kwdc-glass-card p-5 sm:p-6 flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $kpi['label'] }}</p>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 kwdc-kpi-val">{{ $kpi['val'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base flex-shrink-0 {{ $kpi['icon_bg'] }}">
                    <i class="fas fa-{{ $kpi['icon'] }}"></i>
                </div>
            </div>

            @if(isset($kpi['meter']) && $kpi['meter'] !== null)
            <div class="space-y-1.5 pt-1">
                <div class="w-full h-1.5 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full bg-orange-500" style="width: {{ $kpi['meter'] }}%;"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                    <span>{{ $kpi['sub'] }}</span>
                    <span class="font-bold text-slate-700">{{ $kpi['meter'] }}%</span>
                </div>
            </div>
            @else
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Line/Bar Chart -->
        <div class="lg:col-span-2 kwdc-glass-card p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Revenue</h2>
                    <p class="text-[11px] text-slate-400">Last 7 days</p>
                </div>
                <div class="flex items-center gap-1">
                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">7D</span>
                    <span class="px-2 py-1 rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-medium cursor-pointer">30D</span>
                    <span class="px-2 py-1 rounded-lg hover:bg-slate-50 text-slate-400 text-xs font-medium cursor-pointer">90D</span>
                </div>
            </div>
            <div class="h-60 sm:h-64 w-full relative">
                <canvas id="dashboardPrimaryChart"></canvas>
            </div>
        </div>

        <!-- Workload Distribution Doughnut Chart -->
        <div class="kwdc-glass-card p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Occupancy</h2>
                    <p class="text-[11px] text-slate-400">Capacity breakdown</p>
                </div>
                <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400">
                    <i class="fas fa-chart-pie text-xs"></i>
                </div>
            </div>
            <div class="h-60 sm:h-64 w-full relative flex items-center justify-center">
                <canvas id="dashboardActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Role-Specific Feeds -->
    @if($role === 'property_owner')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Warehouses -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-orange-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Warehouses</h2>
                </div>
                <a href="{{ route('warehouses.create') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">+ Add</a>
            </div>
            <div class="space-y-2">
                @forelse($myWarehouses ?? [] as $wh)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('warehouses.show', $wh->id) }}">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs">{{ $wh->name }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            <i class="fas fa-location-dot text-orange-500 mr-1"></i>{{ $wh->city ?? 'Kathmandu' }} &bull; {{ number_format((float) ($wh->capacity ?? 0)) }} m³
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $wh->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($wh->status) }}
                        </span>
                        <span class="text-xs text-orange-600 font-bold">&rarr;</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No warehouses listed.</p>
                @endforelse
            </div>
        </div>

        <!-- Inbound Requests -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Requests</h2>
                </div>
                <a href="{{ route('property.requests.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentRequests ?? [] as $req)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('warehouse-requests.show', $req->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">Request #{{ $req->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ optional($req->client)->name ?? 'Client' }} &bull; {{ $req->space_sqft ?? 0 }} sq.ft</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ ($req->status ?? 'pending') === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($req->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No requests.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($role === 'admin')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Warehouses -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-orange-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Warehouses</h2>
                </div>
                <a href="{{ route('warehouses.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentWarehouses ?? [] as $wh)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('warehouses.show', $wh->id) }}">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs">{{ $wh->name }}</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $wh->city ?? 'Kathmandu' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ $wh->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ ucfirst($wh->status) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No warehouses.</p>
                @endforelse
            </div>
        </div>

        <!-- Dispatches -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck text-emerald-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Dispatches</h2>
                </div>
                <a href="{{ route('dispatch.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentDispatches ?? [] as $disp)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('dispatch.show', $disp->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">#{{ $disp->tracking_id ?? $disp->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($disp->destination_address ?? 'Kathmandu', 25) }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        {{ ucfirst(str_replace('_', ' ', $disp->status ?? 'pending')) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No dispatches.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($role === 'client')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Shipments -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck text-orange-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Shipments</h2>
                </div>
                <a href="{{ route('dispatch.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentDispatches ?? [] as $disp)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('dispatch.show', $disp->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">#{{ $disp->tracking_id ?? $disp->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($disp->destination_address ?? 'Kathmandu', 28) }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        {{ ucfirst(str_replace('_', ' ', $disp->status ?? 'pending')) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No shipments.</p>
                @endforelse
            </div>
        </div>

        <!-- Storage Space -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-warehouse text-blue-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Storage</h2>
                </div>
                <a href="{{ route('my-requests.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentRequests ?? [] as $req)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('warehouse-requests.show', $req->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">{{ optional($req->warehouse)->name ?? ('Request #' . $req->id) }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $req->space_sqft ?? 0 }} sq.ft</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ ($req->status ?? 'pending') === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        {{ ucfirst($req->status ?? 'pending') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No storage requests.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($role === 'driver')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Assigned Jobs -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-route text-orange-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Assigned Jobs</h2>
                </div>
                <a href="{{ route('driver.jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($myJobs ?? [] as $job)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('dispatch.show', $job->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">#{{ $job->tracking_id ?? $job->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($job->destination_address ?? 'Kathmandu', 28) }}</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600">NPR {{ number_format((float) ($job->driver_earning ?? 0)) }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No active jobs.</p>
                @endforelse
            </div>
        </div>

        <!-- Available Pool -->
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-hand-holding-dollar text-emerald-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Marketplace</h2>
                </div>
                <a href="{{ route('driver.available-jobs') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Browse</a>
            </div>
            <div class="space-y-2">
                @forelse($availableJobs ?? [] as $job)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3" data-record-url="{{ route('dispatch.show', $job->id) }}">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">Trip #{{ $job->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ Str::limit($job->pickup_address ?? 'Kathmandu', 25) }}</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600">NPR {{ number_format((float) ($job->driver_earning ?? $job->base_price ?? 0)) }}</span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No jobs available.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($role === 'equipment_owner')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-gear text-orange-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Fleet</h2>
                </div>
                <a href="{{ route('equipment.register') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">+ Add</a>
            </div>
            <div class="space-y-2">
                @forelse($myEquipment ?? [] as $eq)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">{{ $eq->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $eq->type ?? 'Machinery' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ $eq->status === 'available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                        {{ ucfirst($eq->status) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No equipment.</p>
                @endforelse
            </div>
        </div>
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Requests</h2>
                </div>
                <a href="{{ route('equipment.jobs.requests') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($recentRequests ?? [] as $req)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">Request #{{ $req->id }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ optional($req->user)->name ?? 'Client' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                        {{ ucfirst($req->status ?? 'pending') }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No requests.</p>
                @endforelse
            </div>
        </div>
    </div>
    @elseif($role === 'security_agency')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-shield text-blue-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Guards</h2>
                </div>
                <a href="{{ route('security.dashboard') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">Manage</a>
            </div>
            <div class="space-y-2">
                @forelse($securityPersonnel ?? [] as $guard)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">{{ $guard->name }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $guard->badge_number ?? 'Officer' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                        On Duty
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No personnel.</p>
                @endforelse
            </div>
        </div>
        <div class="kwdc-glass-card p-5 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shield-halved text-emerald-500 text-sm"></i>
                    <h2 class="font-bold text-slate-900 text-sm">Cargo</h2>
                </div>
                <a href="{{ route('security.dashboard') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">All</a>
            </div>
            <div class="space-y-2">
                @forelse($guardGoods ?? [] as $item)
                <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100 kwdc-item-row cursor-pointer flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-900 text-xs">{{ $item->item_name ?? 'Secured Goods' }}</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->facility ?? 'Facility 1' }}</p>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        Secured
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 py-4 text-center">No cargo items.</p>
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
    const primaryLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const primaryValues = [18000, 24000, 31000, 28000, 39000, 48000, 42000];

    // Primary Chart (Smooth Line with Subtle Gradient)
    const primaryCanvas = document.getElementById('dashboardPrimaryChart');
    if (primaryCanvas) {
        const ctx = primaryCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(249, 115, 22, 0.20)');
        gradient.addColorStop(1, 'rgba(249, 115, 22, 0.00)');

        new Chart(primaryCanvas, {
            type: 'line',
            data: {
                labels: primaryLabels,
                datasets: [{
                    label: 'Revenue',
                    data: primaryValues,
                    backgroundColor: gradient,
                    borderColor: '#f97316',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f97316',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5,
                    tension: 0.35,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.90)',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: { weight: 'bold', size: 11 },
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
                        grid: { color: '#f8fafc' },
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
                labels: ['Leased (78%)', 'Free (22%)'],
                datasets: [{
                    data: [78, 22],
                    backgroundColor: ['#f97316', '#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '74%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 6,
                            padding: 14,
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
