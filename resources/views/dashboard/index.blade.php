@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Birthday Banner -->
@if($isBirthday)
<div class="mb-6 p-6 rounded-lg text-center font-bold text-xl" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e;">
    <i class="fas fa-birthday-cake mr-3" style="font-size: 30px;"></i>
    {{ $birthdayMessage }}
</div>
@endif

<!-- Welcome Section -->
<div class="mb-8 flex items-center justify-between">
    <div>
        <p class="text-gray-600 text-sm mb-1">Namaste, {{ $user->name }} <span class="text-orange-600 text-sm">({{ ucfirst($role) }})</span></p>
        <h2 class="text-3xl font-bold text-gray-800">Welcome back, {{ $user->name }}</h2>
        <p class="text-gray-500 mt-1">Here's what's happening with your logistics today.</p>
    </div>
    <div class="text-right">
        <p class="text-gray-600 text-sm">System Online</p>
        <p class="text-xs text-gray-400 mt-1">{{ now()->format('F j, Y') }}</p>
    </div>
</div>

<!-- Quick Action Buttons -->
@php
    $colorMap = [
        'blue' => ['bg' => '#eff6ff', 'text' => '#1e40af', 'icon' => '#3b82f6'],
        'green' => ['bg' => '#f0fdf4', 'text' => '#166534', 'icon' => '#22c55e'],
        'orange' => ['bg' => '#fffbeb', 'text' => '#92400e', 'icon' => '#f59e0b'],
        'purple' => ['bg' => '#faf5ff', 'text' => '#6b21a8', 'icon' => '#a855f7'],
        'cyan' => ['bg' => '#ecf9fd', 'text' => '#164e63', 'icon' => '#06b6d4'],
        'gray' => ['bg' => '#f3f4f6', 'text' => '#374151', 'icon' => '#6b7280'],
    ];

    $quickActions = [
        'client' => [
            ['label' => 'New Request', 'icon' => 'fa-plus-circle', 'route' => 'my-requests.create', 'color' => 'blue'],
            ['label' => 'Create Dispatch', 'icon' => 'fa-truck', 'route' => 'dispatch.direct-create', 'color' => 'green'],
            ['label' => 'Request Pickup', 'icon' => 'fa-box-open', 'route' => 'pickup.create', 'color' => 'orange'],
            ['label' => 'View Reports', 'icon' => 'fa-chart-bar', 'route' => 'client.reports', 'color' => 'purple'],
        ],
        'admin' => [
            ['label' => 'Pending Approvals', 'icon' => 'fa-clock', 'route' => 'admin.pending', 'color' => 'orange'],
            ['label' => 'Add Warehouse', 'icon' => 'fa-warehouse', 'route' => 'admin.warehouses.create', 'color' => 'blue'],
            ['label' => 'Manage Users', 'icon' => 'fa-users-cog', 'route' => 'admin.clients', 'color' => 'cyan'],
            ['label' => 'View Reports', 'icon' => 'fa-chart-pie', 'route' => 'admin.reports', 'color' => 'gray'],
        ],
        'driver' => [
            ['label' => 'Available Jobs', 'icon' => 'fa-search', 'route' => 'driver.available-jobs', 'color' => 'green'],
            ['label' => 'My Jobs', 'icon' => 'fa-tasks', 'route' => 'driver.jobs', 'color' => 'blue'],
            ['label' => 'My Earnings', 'icon' => 'fa-wallet', 'route' => 'driver.earnings', 'color' => 'cyan'],
            ['label' => 'My Vehicles', 'icon' => 'fa-truck', 'route' => 'driver.vehicles.index', 'color' => 'gray'],
        ],
        'property_owner' => [
            ['label' => 'Register Property', 'icon' => 'fa-plus-circle', 'route' => 'warehouses.create', 'color' => 'blue'],
            ['label' => 'My Properties', 'icon' => 'fa-building', 'route' => 'warehouses.index', 'color' => 'green'],
            ['label' => 'Requests', 'icon' => 'fa-clipboard-list', 'route' => 'property.requests.index', 'color' => 'orange'],
        ],
        'equipment_owner' => [
            ['label' => 'Register Equipment', 'icon' => 'fa-plus-circle', 'route' => 'equipment.register', 'color' => 'blue'],
            ['label' => 'My Equipment', 'icon' => 'fa-tools', 'route' => 'equipment.list', 'color' => 'green'],
            ['label' => 'Job Requests', 'icon' => 'fa-clipboard-list', 'route' => 'equipment.jobs.requests', 'color' => 'orange'],
        ],
        'security_agency' => [
            ['label' => 'Add Personnel', 'icon' => 'fa-user-plus', 'route' => 'security.personnel.create', 'color' => 'blue'],
            ['label' => 'My Goods', 'icon' => 'fa-boxes', 'route' => 'security.goods.index', 'color' => 'green'],
            ['label' => 'Assignments', 'icon' => 'fa-calendar-check', 'route' => 'security.assignments.index', 'color' => 'orange'],
        ],
    ];
    $actions = $quickActions[$role] ?? [];
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    @foreach($actions as $action)
    @php
        $colors = $colorMap[$action['color']] ?? $colorMap['blue'];
    @endphp
    <a href="{{ route($action['route']) }}" class="rounded-lg p-4 shadow-sm hover:shadow-md transition border-l-4" style="background-color: {{ $colors['bg'] }}; border-left-color: {{ $colors['icon'] }};">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-sm" style="color: {{ $colors['text'] }};">{{ $action['label'] }}</p>
                <p class="text-gray-500 text-xs">Go to {{ strtolower($action['label']) }}</p>
            </div>
            <i class="fas {{ $action['icon'] }} text-2xl" style="color: {{ $colors['icon'] }};"></i>
        </div>
    </a>
    @endforeach
</div>

<!-- Metrics Grid - First Row (4 columns) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    @php
        $metricOrder = [
            'client' => ['active_requests', 'dispatches', 'pending_dispatches', 'completed_dispatches', 'pickups', 'stock_items', 'pending_invoices', 'total_spent'],
            'admin' => ['clients', 'drivers', 'total_dispatches', 'vehicles'],
            'driver' => ['available_jobs', 'active_jobs', 'completed_jobs', 'total_earnings'],
            'property_owner' => ['my_properties', 'approved_properties', 'pending_properties', 'total_requests'],
            'equipment_owner' => ['my_equipment', 'available_equipment', 'job_requests', 'active_jobs'],
            'security_agency' => ['personnel_count', 'goods_count', 'assignments', 'active_assignments'],
        ];
        
        $metricConfig = [
            'active_requests' => ['label' => 'ACTIVE REQUESTS', 'icon' => 'fa-clipboard-list', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'dispatches' => ['label' => 'DISPATCHES', 'icon' => 'fa-truck', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'pending_dispatches' => ['label' => 'PENDING DISPATCHES', 'icon' => 'fa-hourglass-half', 'bg' => '#fffbeb', 'icon_color' => '#f59e0b'],
            'completed_dispatches' => ['label' => 'COMPLETED DISPATCHES', 'icon' => 'fa-check-circle', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
            'pickups' => ['label' => 'PICKUPS', 'icon' => 'fa-box-open', 'bg' => '#ecfdfd', 'icon_color' => '#06b6d4'],
            'stock_items' => ['label' => 'STOCK ITEMS', 'icon' => 'fa-cube', 'bg' => '#faf5ff', 'icon_color' => '#a855f7'],
            'pending_invoices' => ['label' => 'PENDING INVOICES', 'icon' => 'fa-file-invoice', 'bg' => '#fef2f2', 'icon_color' => '#ef4444'],
            'total_spent' => ['label' => 'TOTAL SPENT', 'icon' => 'fa-wallet', 'bg' => '#fdf2f8', 'icon_color' => '#ec4899'],
            'clients' => ['label' => 'CLIENTS', 'icon' => 'fa-users', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'drivers' => ['label' => 'DRIVERS', 'icon' => 'fa-car', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
            'total_dispatches' => ['label' => 'TOTAL DISPATCHES', 'icon' => 'fa-truck', 'bg' => '#fffbeb', 'icon_color' => '#f59e0b'],
            'vehicles' => ['label' => 'VEHICLES', 'icon' => 'fa-car', 'bg' => '#faf5ff', 'icon_color' => '#a855f7'],
            'available_jobs' => ['label' => 'AVAILABLE JOBS', 'icon' => 'fa-search', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'active_jobs' => ['label' => 'ACTIVE JOBS', 'icon' => 'fa-play-circle', 'bg' => '#ecfdfd', 'icon_color' => '#06b6d4'],
            'completed_jobs' => ['label' => 'COMPLETED JOBS', 'icon' => 'fa-check-circle', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
            'total_earnings' => ['label' => 'TOTAL EARNINGS', 'icon' => 'fa-wallet', 'bg' => '#fdf2f8', 'icon_color' => '#ec4899'],
            'my_properties' => ['label' => 'MY PROPERTIES', 'icon' => 'fa-building', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'approved_properties' => ['label' => 'APPROVED PROPERTIES', 'icon' => 'fa-check-circle', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
            'pending_properties' => ['label' => 'PENDING PROPERTIES', 'icon' => 'fa-clock', 'bg' => '#fffbeb', 'icon_color' => '#f59e0b'],
            'total_requests' => ['label' => 'TOTAL REQUESTS', 'icon' => 'fa-clipboard-list', 'bg' => '#ecfdfd', 'icon_color' => '#06b6d4'],
            'my_equipment' => ['label' => 'MY EQUIPMENT', 'icon' => 'fa-tools', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'available_equipment' => ['label' => 'AVAILABLE EQUIPMENT', 'icon' => 'fa-check-circle', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
            'job_requests' => ['label' => 'JOB REQUESTS', 'icon' => 'fa-clipboard-list', 'bg' => '#fffbeb', 'icon_color' => '#f59e0b'],
            'personnel_count' => ['label' => 'PERSONNEL', 'icon' => 'fa-users', 'bg' => '#eff6ff', 'icon_color' => '#3b82f6'],
            'goods_count' => ['label' => 'GOODS', 'icon' => 'fa-boxes', 'bg' => '#ecfdfd', 'icon_color' => '#06b6d4'],
            'assignments' => ['label' => 'ASSIGNMENTS', 'icon' => 'fa-calendar-check', 'bg' => '#fffbeb', 'icon_color' => '#f59e0b'],
            'active_assignments' => ['label' => 'ACTIVE ASSIGNMENTS', 'icon' => 'fa-shield-alt', 'bg' => '#f0fdf4', 'icon_color' => '#22c55e'],
        ];

        $order = $metricOrder[$role] ?? array_keys($stats);
        $metricsToDisplay = array_slice($order, 0, 4);
    @endphp

    @foreach($metricsToDisplay as $key)
        @if(isset($stats[$key]) && (is_numeric($stats[$key]) || is_bool($stats[$key])))
            @php
                $config = $metricConfig[$key] ?? ['label' => ucwords(str_replace('_', ' ', $key)), 'icon' => 'fa-circle', 'bg' => '#f3f4f6', 'icon_color' => '#6b7280'];
            @endphp
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">{{ $config['label'] }}</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">{{ is_bool($stats[$key]) ? ($stats[$key] ? 'Yes' : 'No') : number_format($stats[$key]) }}</p>
                    </div>
                    <div class="p-3 rounded-lg" style="background-color: {{ $config['bg'] }};">
                        <i class="fas {{ $config['icon'] }} text-2xl" style="color: {{ $config['icon_color'] }};"></i>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Metrics Grid - Second Row (4 columns) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    @php
        $metricsToDisplay = array_slice($order, 4, 4);
    @endphp

    @foreach($metricsToDisplay as $key)
        @if(isset($stats[$key]) && (is_numeric($stats[$key]) || is_bool($stats[$key])))
            @php
                $config = $metricConfig[$key] ?? ['label' => ucwords(str_replace('_', ' ', $key)), 'icon' => 'fa-circle', 'bg' => '#f3f4f6', 'icon_color' => '#6b7280'];
            @endphp
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">{{ $config['label'] }}</p>
                        <p class="text-4xl font-bold text-gray-800 mt-2">{{ is_bool($stats[$key]) ? ($stats[$key] ? 'Yes' : 'No') : number_format($stats[$key]) }}</p>
                    </div>
                    <div class="p-3 rounded-lg" style="background-color: {{ $config['bg'] }};">
                        <i class="fas {{ $config['icon'] }} text-2xl" style="color: {{ $config['icon_color'] }};"></i>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Recent Activity Sections -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Recent Requests -->
    @if(isset($recentRequests) && $recentRequests->count())
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Requests</h3>
            <a href="{{ route('my-requests.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentRequests as $request)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Request #{{ $request->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ ucfirst($request->status) }}</span>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No requests found</p>
            @endforelse
        </div>
    </div>
    @endif

    <!-- Recent Dispatches -->
    @if(isset($recentDispatches) && $recentDispatches->count())
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Recent Dispatches</h3>
            <a href="{{ route('dispatch.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentDispatches as $dispatch)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-800">Dispatch #{{ $dispatch->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">Driver: {{ $dispatch->driver->name ?? 'Unassigned' }}</p>
                    </div>
                    <a href="{{ route('tracking.shipment', $dispatch->id) }}" class="text-orange-500 text-sm hover:text-orange-600">Track →</a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No dispatches found</p>
            @endforelse
        </div>
    </div>
    @endif
</div>

<!-- Additional sections for other roles -->
@if(isset($recentPickups) && $recentPickups->count())
<div class="mt-6 bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Recent Pickups</h3>
        <a href="{{ route('pickup.index') }}" class="text-orange-500 text-sm hover:text-orange-600">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b-2 border-gray-200">
                <tr>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">ID</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Pickup Address</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Status</th>
                    <th class="text-left py-2 px-4 font-semibold text-gray-600">Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentPickups as $pickup)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4">#{{ $pickup->id }}</td>
                    <td class="py-3 px-4">{{ Str::limit($pickup->pickup_address, 30) }}</td>
                    <td class="py-3 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">{{ ucfirst($pickup->status) }}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-500">{{ $pickup->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
