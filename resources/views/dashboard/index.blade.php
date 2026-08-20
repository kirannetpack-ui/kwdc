@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .kwdc-dashboard-shell { display: grid; gap: 24px; }
    .kwdc-hero {
        background: #0f172a;
        color: #ffffff;
        border-radius: 8px;
        padding: 24px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 20px;
        align-items: center;
        border: 1px solid #1e293b;
    }
    .kwdc-hero-eyebrow {
        color: #fbbf24;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .kwdc-hero h2 {
        margin: 0;
        font-size: clamp(24px, 3vw, 36px);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: 0;
    }
    .kwdc-hero p { margin: 10px 0 0; color: #cbd5e1; max-width: 760px; }
    .kwdc-system-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 999px;
        background: rgba(34, 197, 94, .12);
        color: #bbf7d0;
        border: 1px solid rgba(34, 197, 94, .25);
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }
    .kwdc-system-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 5px rgba(34, 197, 94, .14);
    }
    .kwdc-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }
    .kwdc-stat-card,
    .kwdc-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .kwdc-stat-card {
        padding: 18px;
        min-height: 142px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .kwdc-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .kwdc-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin: 0;
    }
    .kwdc-stat-value {
        color: #111827;
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
        margin-top: 10px;
        word-break: break-word;
    }
    .kwdc-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        flex: 0 0 auto;
    }
    .kwdc-stat-foot { color: #94a3b8; font-size: 12px; margin-top: 16px; }
    .kwdc-grid-2 {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(320px, .65fr);
        gap: 16px;
    }
    .kwdc-panel { padding: 18px; min-width: 0; }
    .kwdc-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .kwdc-panel-title {
        margin: 0;
        color: #111827;
        font-size: 16px;
        font-weight: 800;
    }
    .kwdc-panel-kicker { margin: 3px 0 0; color: #64748b; font-size: 13px; }
    .kwdc-chart-box { position: relative; height: 300px; width: 100%; }
    .kwdc-activity-list { display: grid; gap: 10px; }
    .kwdc-activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        background: #f8fafc;
    }
    .kwdc-activity-title { margin: 0; color: #172033; font-weight: 700; font-size: 14px; }
    .kwdc-activity-meta { margin: 3px 0 0; color: #64748b; font-size: 12px; }
    .kwdc-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 86px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #ffedd5;
        color: #9a3412;
        font-size: 12px;
        font-weight: 800;
        text-transform: capitalize;
        white-space: nowrap;
    }
    .kwdc-quick-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .kwdc-action {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 14px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #172033;
        text-decoration: none;
        font-weight: 800;
        min-height: 54px;
    }
    .kwdc-action:hover { color: #92400e; border-color: #f59e0b; background: #fffbeb; }
    @media (max-width: 1180px) {
        .kwdc-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .kwdc-grid-2 { grid-template-columns: 1fr; }
    }
    @media (max-width: 760px) {
        .kwdc-hero { grid-template-columns: 1fr; }
        .kwdc-stat-grid,
        .kwdc-quick-grid { grid-template-columns: 1fr; }
        .kwdc-chart-box { height: 260px; }
    }
</style>
@endpush

@section('content')
@php
    $rawStats = collect($stats ?? []);
    $numericStats = $rawStats
        ->filter(fn ($value) => (is_numeric($value) || is_bool($value)) && !is_array($value))
        ->take(8);

    $statIcons = ['warehouse', 'truck-moving', 'clipboard-list', 'users', 'box-open', 'file-invoice', 'wallet', 'shield-alt'];
    $statColors = [
        ['bg' => '#eff6ff', 'fg' => '#2563eb'],
        ['bg' => '#ecfdf5', 'fg' => '#059669'],
        ['bg' => '#fffbeb', 'fg' => '#d97706'],
        ['bg' => '#f5f3ff', 'fg' => '#7c3aed'],
        ['bg' => '#fdf2f8', 'fg' => '#db2777'],
        ['bg' => '#eef2ff', 'fg' => '#4f46e5'],
        ['bg' => '#f0fdfa', 'fg' => '#0f766e'],
        ['bg' => '#fef2f2', 'fg' => '#dc2626'],
    ];

    $chartLabels = $numericStats->keys()->map(fn ($key) => ucwords(str_replace('_', ' ', $key)))->values();
    $chartValues = $numericStats->values()->map(fn ($value) => is_bool($value) ? ($value ? 1 : 0) : round((float) $value, 2))->values();

    $activitySummary = collect([
        'Recent Requests' => isset($recentRequests) ? $recentRequests->count() : 0,
        'Recent Dispatches' => isset($recentDispatches) ? $recentDispatches->count() : 0,
        'Recent Pickups' => isset($recentPickups) ? $recentPickups->count() : 0,
        'Active Jobs' => isset($activeJobs) ? $activeJobs->count() : 0,
        'Available Jobs' => isset($availableJobs) ? $availableJobs->count() : 0,
        'Equipment Jobs' => isset($jobRequests) ? $jobRequests->count() : 0,
    ])->filter(fn ($value) => $value > 0);

    if ($activitySummary->isEmpty()) {
        $activitySummary = $numericStats->take(4)->mapWithKeys(fn ($value, $key) => [ucwords(str_replace('_', ' ', $key)) => is_bool($value) ? ($value ? 1 : 0) : (float) $value]);
    }

    $weeklyEarningLabels = collect($weeklyEarnings ?? [])->pluck('day')->values();
    $weeklyEarningValues = collect($weeklyEarnings ?? [])->pluck('amount')->map(fn ($value) => round((float) $value, 2))->values();

    $quickActions = [];
    if (($role ?? '') === 'admin') {
        $quickActions = [
            ['label' => 'Pending approvals', 'icon' => 'clock', 'route' => route('admin.pending')],
            ['label' => 'Analytics', 'icon' => 'chart-pie', 'route' => route('admin.analytics')],
            ['label' => 'Dispatch orders', 'icon' => 'truck-moving', 'route' => route('admin.dispatch')],
        ];
    } elseif (($role ?? '') === 'client') {
        $quickActions = [
            ['label' => 'New request', 'icon' => 'plus-circle', 'route' => route('my-requests.create')],
            ['label' => 'Create dispatch', 'icon' => 'truck', 'route' => route('dispatch.direct-create')],
            ['label' => 'Create pickup', 'icon' => 'box-open', 'route' => route('pickup.direct-create')],
        ];
    } elseif (($role ?? '') === 'driver') {
        $quickActions = [
            ['label' => 'Available jobs', 'icon' => 'search', 'route' => route('driver.available-jobs')],
            ['label' => 'My jobs', 'icon' => 'tasks', 'route' => route('driver.jobs')],
            ['label' => 'Earnings', 'icon' => 'chart-line', 'route' => route('driver.earnings')],
        ];
    } elseif (($role ?? '') === 'equipment_owner') {
        $quickActions = [
            ['label' => 'Register equipment', 'icon' => 'plus-circle', 'route' => route('equipment.register')],
            ['label' => 'Job requests', 'icon' => 'clipboard-list', 'route' => route('equipment.jobs.requests')],
            ['label' => 'Earnings', 'icon' => 'wallet', 'route' => route('equipment.jobs.earnings')],
        ];
    } elseif (($role ?? '') === 'property_owner') {
        $quickActions = [
            ['label' => 'Register property', 'icon' => 'plus-circle', 'route' => route('warehouses.create')],
            ['label' => 'Requests', 'icon' => 'clipboard-list', 'route' => route('property.requests.index')],
            ['label' => 'Analytics', 'icon' => 'chart-bar', 'route' => route('property.analytics')],
        ];
    }
@endphp

<div class="kwdc-dashboard-shell">
    @if($isBirthday ?? false)
    <div class="p-5 rounded-lg font-bold" style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;">
        <i class="fas fa-birthday-cake mr-2"></i>{{ $birthdayMessage ?? 'Happy Birthday!' }}
    </div>
    @endif

    <section class="kwdc-hero">
        <div>
            <div class="kwdc-hero-eyebrow">KTM-WDC {{ ucwords(str_replace('_', ' ', $role ?? 'user')) }}</div>
            <h2>Welcome back, {{ $user->name ?? 'User' }}</h2>
            <p>Here is the current picture of operations, requests, jobs, and revenue signals across your workspace.</p>
        </div>
        <div class="kwdc-system-pill">
            <span class="kwdc-system-dot"></span>
            System Online
        </div>
    </section>

    @if($numericStats->isNotEmpty())
    <section class="kwdc-stat-grid">
        @foreach($numericStats as $key => $value)
            @php
                $style = $statColors[$loop->index % count($statColors)];
                $icon = $statIcons[$loop->index % count($statIcons)];
                $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : number_format((float) $value, str_contains($key, 'rating') ? 1 : 0);
            @endphp
            <article class="kwdc-stat-card">
                <div class="kwdc-stat-top">
                    <div>
                        <p class="kwdc-stat-label">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                        <div class="kwdc-stat-value">{{ $displayValue }}</div>
                    </div>
                    <div class="kwdc-stat-icon" style="background: {{ $style['bg'] }}; color: {{ $style['fg'] }};">
                        <i class="fas fa-{{ $icon }}"></i>
                    </div>
                </div>
                <div class="kwdc-stat-foot">Updated {{ now()->format('M j, g:i A') }}</div>
            </article>
        @endforeach
    </section>
    @endif

    <section class="kwdc-grid-2">
        <div class="kwdc-panel">
            <div class="kwdc-panel-header">
                <div>
                    <h3 class="kwdc-panel-title">{{ $weeklyEarningValues->isNotEmpty() ? 'Weekly Earnings' : 'Key Metrics' }}</h3>
                    <p class="kwdc-panel-kicker">{{ $weeklyEarningValues->isNotEmpty() ? 'Delivered job earnings over the last 7 days' : 'Fast visual read of your most important numbers' }}</p>
                </div>
                <i class="fas fa-chart-line text-orange-500"></i>
            </div>
            <div class="kwdc-chart-box">
                <canvas id="dashboardPrimaryChart"></canvas>
            </div>
        </div>

        <div class="kwdc-panel">
            <div class="kwdc-panel-header">
                <div>
                    <h3 class="kwdc-panel-title">Activity Mix</h3>
                    <p class="kwdc-panel-kicker">Recent work by type</p>
                </div>
                <i class="fas fa-chart-pie text-blue-500"></i>
            </div>
            <div class="kwdc-chart-box">
                <canvas id="dashboardActivityChart"></canvas>
            </div>
        </div>
    </section>

    @if(!empty($quickActions))
    <section class="kwdc-panel">
        <div class="kwdc-panel-header">
            <div>
                <h3 class="kwdc-panel-title">Quick Actions</h3>
                <p class="kwdc-panel-kicker">Common next steps for your role</p>
            </div>
        </div>
        <div class="kwdc-quick-grid">
            @foreach($quickActions as $action)
            <a class="kwdc-action" href="{{ $action['route'] }}">
                <i class="fas fa-{{ $action['icon'] }} text-orange-500"></i>
                <span>{{ $action['label'] }}</span>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <section class="kwdc-grid-2">
        @if(!empty($recentRequests) && $recentRequests->count() > 0)
        <div class="kwdc-panel">
            <div class="kwdc-panel-header">
                <div>
                    <h3 class="kwdc-panel-title">Recent Requests</h3>
                    <p class="kwdc-panel-kicker">Newest warehouse and service requests</p>
                </div>
            </div>
            <div class="kwdc-activity-list">
                @foreach($recentRequests->take(6) as $request)
                <div class="kwdc-activity-item">
                    <div>
                        <p class="kwdc-activity-title">Request #{{ $request->id }}</p>
                        <p class="kwdc-activity-meta">{{ optional($request->created_at)->format('M d, Y') }}</p>
                    </div>
                    <span class="kwdc-status-badge">{{ $request->status ?? 'pending' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($recentDispatches) && $recentDispatches->count() > 0)
        <div class="kwdc-panel">
            <div class="kwdc-panel-header">
                <div>
                    <h3 class="kwdc-panel-title">Recent Dispatches</h3>
                    <p class="kwdc-panel-kicker">Latest movement and delivery work</p>
                </div>
            </div>
            <div class="kwdc-activity-list">
                @foreach($recentDispatches->take(6) as $dispatch)
                <div class="kwdc-activity-item">
                    <div>
                        <p class="kwdc-activity-title">Dispatch #{{ $dispatch->id }}</p>
                        <p class="kwdc-activity-meta">Driver: {{ $dispatch->driver->name ?? 'Unassigned' }}</p>
                    </div>
                    <a href="{{ route('tracking.shipment', $dispatch->id) }}" class="text-orange-600 text-sm font-bold">Track</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    @if(!empty($recentPickups) && $recentPickups->count() > 0)
    <section class="kwdc-panel">
        <div class="kwdc-panel-header">
            <div>
                <h3 class="kwdc-panel-title">Recent Pickups</h3>
                <p class="kwdc-panel-kicker">New pickup requests and their current status</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                        <th class="py-3 px-3 font-bold">ID</th>
                        <th class="py-3 px-3 font-bold">Pickup Address</th>
                        <th class="py-3 px-3 font-bold">Status</th>
                        <th class="py-3 px-3 font-bold">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPickups as $pickup)
                    <tr class="border-b border-gray-100">
                        <td class="py-3 px-3 font-semibold">#{{ $pickup->id }}</td>
                        <td class="py-3 px-3">{{ Str::limit($pickup->pickup_address ?? 'N/A', 42) }}</td>
                        <td class="py-3 px-3"><span class="kwdc-status-badge">{{ $pickup->status ?? 'pending' }}</span></td>
                        <td class="py-3 px-3 text-gray-500">{{ optional($pickup->created_at)->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartText = '#475569';
    const gridColor = '#e5e7eb';
    const primaryLabels = @json($weeklyEarningValues->isNotEmpty() ? $weeklyEarningLabels : $chartLabels);
    const primaryValues = @json($weeklyEarningValues->isNotEmpty() ? $weeklyEarningValues : $chartValues);
    const primaryType = @json($weeklyEarningValues->isNotEmpty() ? 'line' : 'bar');
    const activityLabels = @json($activitySummary->keys()->values());
    const activityValues = @json($activitySummary->values()->map(fn ($value) => round((float) $value, 2))->values());

    const primaryCanvas = document.getElementById('dashboardPrimaryChart');
    if (primaryCanvas && primaryLabels.length) {
        new Chart(primaryCanvas, {
            type: primaryType,
            data: {
                labels: primaryLabels,
                datasets: [{
                    label: primaryType === 'line' ? 'Earnings' : 'Value',
                    data: primaryValues,
                    backgroundColor: primaryType === 'line' ? 'rgba(245, 158, 11, .14)' : '#2563eb',
                    borderColor: '#f59e0b',
                    borderWidth: primaryType === 'line' ? 3 : 0,
                    borderRadius: 6,
                    pointRadius: 4,
                    pointBackgroundColor: '#f59e0b',
                    tension: .35,
                    fill: primaryType === 'line'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: chartText }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { color: chartText }, grid: { color: gridColor } }
                }
            }
        });
    }

    const activityCanvas = document.getElementById('dashboardActivityChart');
    if (activityCanvas && activityLabels.length) {
        new Chart(activityCanvas, {
            type: 'doughnut',
            data: {
                labels: activityLabels,
                datasets: [{
                    data: activityValues,
                    backgroundColor: ['#f59e0b', '#2563eb', '#059669', '#7c3aed', '#db2777', '#0f766e'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, color: chartText }
                    }
                }
            }
        });
    }
});
</script>
@endpush
