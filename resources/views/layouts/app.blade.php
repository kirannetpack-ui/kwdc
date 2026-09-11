<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">
    <script src="{{ asset('js/kwdc-maps.js') }}" defer></script>
    <title>KTM-WDC - @yield('title', 'Warehouse & Distribution Connect')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('css/kwdc-ui.css') }}">
    
    <style>
        * { font-family: 'Inter', sans-serif; }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 286px;
            height: 100vh;
            color: white;
            z-index: 50;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
        }

        .sidebar-link {
            display: flex !important;
            align-items: center;
            gap: 12px;
            margin: 4px 12px;
            padding: 11px 14px;
            color: #b8c2d6;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            width: auto;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            transition: color 0.18s ease, background-color 0.18s ease, transform 0.18s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            color: #ffffff;
            background: rgba(245, 158, 11, 0.16);
        }

        .sidebar-link i { width: 20px; text-align: center; font-size: 15px; flex-shrink: 0; }
        .sidebar-link .badge { background: #ef4444; color: white; font-size: 10px; padding: 2px 8px; border-radius: 999px; margin-left: auto; flex-shrink: 0; }
        .section-header { font-size: 10px; font-weight: 800; color: #78869d; text-transform: uppercase; letter-spacing: 1px; padding: 16px 18px 5px; display: block !important; }

        .main-content { margin-left: 286px; min-height: 100vh; }
        .top-bar { padding: 18px 26px; position: sticky; top: 0; z-index: 40; display: flex; justify-content: space-between; align-items: center; gap: 18px; }
        .page-content { padding: 28px; max-width: 1460px; margin: 0 auto; }
        .menu-toggle { display: none; position: fixed; top: 16px; left: 16px; z-index: 60; background: #f59e0b; color: #111827; padding: 10px 13px; border-radius: 8px; cursor: pointer; border: none; font-size: 18px; box-shadow: 0 14px 28px rgba(15, 23, 42, .18); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 286px; }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
            .top-bar { padding: 14px 16px 14px 70px; flex-wrap: wrap; }
            .top-bar h2 { font-size: 19px; }
            .page-content { padding: 18px 14px; }
        }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.56); z-index: 45; }
        .sidebar-overlay.active { display: block; }

        .notification-dropdown {
            width: min(380px, calc(100vw - 28px));
            max-height: 430px;
            overflow-y: auto;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 18px 46px rgba(15, 23, 42, 0.18);
        }
        .notification-dropdown .dropdown-header,
        .notification-dropdown .dropdown-footer {
            background: #f8fafc;
            border-color: #e5e7eb;
        }
        .notification-dropdown .dropdown-header { padding: 12px 16px; font-weight: 800; color: #0f172a; }
        .notification-dropdown .dropdown-item { padding: 12px 16px; border-bottom: 1px solid #eef2f7; white-space: normal; line-height: 1.4; }
        .notification-dropdown .dropdown-item:hover { background: #f8fafc; }
        .notification-title { font-weight: 800; color: #0f172a; }
        .notification-text { font-size: 13px; color: #64748b; margin: 2px 0; }
        .notification-time { font-size: 11px; color: #94a3b8; }
        .notification-dropdown .dropdown-footer { padding: 10px; text-align: center; border-top: 1px solid #e5e7eb; }

        .sidebar .text-gray-400 { color: #cbd5e1 !important; }
        .sidebar .text-gray-500 { color: #dbe4ef !important; }
        .sidebar .section-header { color: #cbd5e1 !important; }
        .main-content .text-gray-500,
        .main-content .text-muted { color: #526174 !important; }
        .main-content .text-gray-600 { color: #334155 !important; }
        .main-content .text-gray-400 { color: #64748b !important; }
    </style>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/kwdc-minimal.css') }}">
    <script src="{{ asset('js/kwdc-records.js') }}" defer></script>
</head>
<body class="kwdc-app-shell">
<div id="kwdc-page-progress" aria-hidden="true"></div>

<!-- Mobile Menu Toggle -->
<button class="menu-toggle" onclick="toggleMobileMenu()" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false"><i class="fas fa-bars" aria-hidden="true"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>

<!-- ============================================================ -->
<!-- SIDEBAR -->
<!-- ============================================================ -->
<div class="sidebar" id="sidebar">
    <div class="kwdc-sidebar-brand p-6">
        <div class="flex items-center space-x-3">
            <div class="kwdc-brand-icon w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 flex items-center justify-center shadow-lg shadow-orange-500/25 flex-shrink-0">
                <i class="fas fa-warehouse text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-extrabold text-white tracking-tight leading-tight">KTM-WDC</h1>
                <p class="text-[11px] text-slate-400 font-medium">Logistics & Distribution</p>
            </div>
        </div>
    </div>
    
    @auth
    <div class="kwdc-sidebar-user p-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('profile.edit') }}" class="kwdc-user-avatar w-10 h-10 rounded-xl overflow-hidden bg-gradient-to-br from-orange-500 to-amber-600 text-white border border-white/10 flex items-center justify-center flex-shrink-0 font-extrabold text-sm shadow-sm transition hover:scale-105" title="Edit Profile">
                @if(Auth::user()->profile_photo && file_exists(public_path(Auth::user()->profile_photo)))
                    <img src="{{ asset(Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    <span>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                @endif
            </a>
            <div class="min-w-0 flex-1">
                <p class="font-semibold text-xs text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                <span class="kwdc-role-pill text-[10px]">{{ ucfirst(str_replace('_', ' ', Auth::user()->role ?? 'User')) }}</span>
            </div>
        </div>
    </div>
    @endauth
    
    <!-- Navigation -->
    <nav class="py-4">
        @auth
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <!-- ADMIN -->
        @if(Auth::user()->role === 'admin')
        <div class="section-header">Management</div>
        <a href="{{ route('admin.pending') }}" class="sidebar-link {{ request()->routeIs('admin.pending') ? 'active' : '' }}">
            <i class="fas fa-clock"></i> Pending Approvals
            @php $pendingCount = \App\Models\Warehouse::where('status', 'pending')->count(); @endphp
            @if($pendingCount > 0) <span class="badge">{{ $pendingCount }}</span> @endif
        </a>
        <a href="{{ route('admin.all-warehouses') }}" class="sidebar-link {{ request()->routeIs('admin.all-warehouses') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> All Warehouses
            @php $warehouseCount = \App\Models\Warehouse::count(); @endphp
            @if($warehouseCount > 0) <span class="badge">{{ $warehouseCount }}</span> @endif
        </a>
        <a href="{{ route('admin.requests') }}" class="sidebar-link {{ request()->routeIs('admin.requests') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Warehouse Requests
        </a>
        <a href="{{ route('admin.security.agencies') }}" class="sidebar-link {{ request()->routeIs('admin.security.*') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i> Security Agencies
        </a>
        <a href="{{ route('admin.analytics.predictive') }}" class="sidebar-link {{ request()->routeIs('admin.analytics.predictive') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Predictive Analytics
        </a>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Analytics Dashboard
        </a>
        <a href="{{ route('admin.vehicles') }}" class="sidebar-link {{ request()->routeIs('admin.vehicles') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> All Vehicles
        </a>
        <a href="{{ route('tracking.index') }}" class="sidebar-link {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> Live Tracking
        </a>
        <a href="{{ route('admin.dispatch') }}" class="sidebar-link {{ request()->routeIs('admin.dispatch') ? 'active' : '' }}">
            <i class="fas fa-truck-moving"></i> Dispatch Orders
        </a>
        <a href="{{ route('admin.pickup') }}" class="sidebar-link {{ request()->routeIs('admin.pickup') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i> Pickup Requests
        </a>
        <a href="{{ route('admin.clients') }}" class="sidebar-link {{ request()->routeIs('admin.clients') ? 'active' : '' }}">
            <i class="fas fa-users"></i> All Clients
        </a>
        <a href="{{ route('admin.drivers') }}" class="sidebar-link {{ request()->routeIs('admin.drivers') ? 'active' : '' }}">
            <i class="fas fa-id-card"></i> Drivers
        </a>
        <a href="{{ route('admin.equipment-list') }}" class="sidebar-link {{ request()->routeIs('admin.equipment-list') ? 'active' : '' }}">
            <i class="fas fa-tools"></i> Equipment List
        </a>
        <a href="{{ route('admin.property-owners') }}" class="sidebar-link {{ request()->routeIs('admin.property-owners') ? 'active' : '' }}">
            <i class="fas fa-building"></i> Property Owners
        </a>
        <a href="{{ route('admin.invoices') }}" class="sidebar-link {{ request()->routeIs('admin.invoices') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i> Invoices
        </a>
        <a href="{{ route('admin.margins.index') }}" class="sidebar-link {{ request()->routeIs('admin.margins.*') ? 'active' : '' }}">
            <i class="fas fa-percent"></i> Margins & Tiers
        </a>
        <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
        <a href="{{ route('admin.partner-earnings') }}" class="sidebar-link {{ request()->routeIs('admin.partner-earnings') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd"></i> Partner Earnings
        </a>
        @endif

        <!-- CLIENT -->
        @if(Auth::user()->role === 'client')
        <div class="section-header">Client Zone</div>
        <a href="{{ route('my-requests.index') }}" class="sidebar-link {{ request()->routeIs('my-requests.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> My Requests
        </a>
        <a href="{{ route('my-requests.create') }}" class="sidebar-link {{ request()->routeIs('my-requests.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> New Request
        </a>
        <a href="{{ route('dispatch.index') }}" class="sidebar-link {{ request()->routeIs('dispatch.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Dispatch Orders
        </a>
        <a href="{{ route('pickup.index') }}" class="sidebar-link {{ request()->routeIs('pickup.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i> Pickup Requests
        </a>
        <a href="{{ route('tracking.index') }}" class="sidebar-link {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> Track My Shipments
        </a>
        <a href="{{ route('stock.index') }}" class="sidebar-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> My Stock
        </a>
        <a href="{{ route('client.proposals.index') }}" class="sidebar-link {{ request()->routeIs('client.proposals.*') ? 'active' : '' }}">
            <i class="fas fa-file-contract"></i> My Proposals
        </a>
        <a href="{{ route('client.equipment.requests') }}" class="sidebar-link {{ request()->routeIs('client.equipment.*') ? 'active' : '' }}">
            <i class="fas fa-tools"></i> Equipment Requests
        </a>
        <a href="{{ route('invoices.client-index') }}" class="sidebar-link {{ request()->routeIs('invoices.client-index') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i> My Invoices
        </a>
        <a href="{{ route('profile.contacts') }}" class="sidebar-link {{ request()->routeIs('profile.contacts') ? 'active' : '' }}">
            <i class="fas fa-address-book"></i> Manage Contacts
        </a>
        @endif

        <!-- DRIVER -->
        @if(Auth::user()->role === 'driver')
        <div class="section-header">Driver Zone</div>
        <a href="{{ route('driver.dashboard') }}" class="sidebar-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Driver Dashboard
        </a>
        <a href="{{ route('driver.jobs') }}" class="sidebar-link {{ request()->routeIs('driver.jobs') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i> My Jobs
        </a>
        <a href="{{ route('driver.available-jobs') }}" class="sidebar-link {{ request()->routeIs('driver.available-jobs') ? 'active' : '' }}">
            <i class="fas fa-search"></i> Available Jobs
        </a>
        <a href="{{ route('driver.pickups') }}" class="sidebar-link {{ request()->routeIs('driver.pickups') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> Pickup Jobs
        </a>
        <a href="{{ route('tracking.index') }}" class="sidebar-link {{ request()->routeIs('tracking.index') ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> My Live Jobs
        </a>
        <a href="{{ route('driver.vehicles.index') }}" class="sidebar-link {{ request()->routeIs('driver.vehicles.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> My Vehicles
        </a>
        <a href="{{ route('driver.rates') }}" class="sidebar-link {{ request()->routeIs('driver.rates.*') ? 'active' : '' }}">
            <i class="fas fa-tag"></i> My Rates
        </a>
        <a href="{{ route('driver.earnings') }}" class="sidebar-link {{ request()->routeIs('driver.earnings') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> My Earnings
        </a>
        @endif

        <!-- EQUIPMENT OWNER -->
        @if(Auth::user()->role === 'equipment_owner')
        <div class="section-header">Equipment Zone</div>
        <a href="{{ route('equipment.dashboard') }}" class="sidebar-link {{ request()->routeIs('equipment.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Equipment Dashboard
        </a>
        <a href="{{ route('equipment.register') }}" class="sidebar-link {{ request()->routeIs('equipment.register') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Register Equipment
        </a>
        <a href="{{ route('equipment.list') }}" class="sidebar-link {{ request()->routeIs('equipment.list') ? 'active' : '' }}">
            <i class="fas fa-tools"></i> My Equipment
        </a>
        <a href="{{ route('equipment.jobs.index') }}" class="sidebar-link {{ request()->routeIs('equipment.jobs.*') ? 'active' : '' }}">
            <i class="fas fa-briefcase"></i> All Jobs
        </a>
        <a href="{{ route('equipment.jobs.requests') }}" class="sidebar-link {{ request()->routeIs('equipment.jobs.requests') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Job Requests
        </a>
        <a href="{{ route('equipment.jobs.active') }}" class="sidebar-link {{ request()->routeIs('equipment.jobs.active') ? 'active' : '' }}">
            <i class="fas fa-play-circle"></i> Active Jobs
        </a>
        <a href="{{ route('equipment.jobs.history') }}" class="sidebar-link {{ request()->routeIs('equipment.jobs.history') ? 'active' : '' }}">
            <i class="fas fa-history"></i> Job History
        </a>
        <a href="{{ route('equipment.jobs.earnings') }}" class="sidebar-link {{ request()->routeIs('equipment.jobs.earnings') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> Earnings
        </a>
        @endif

        <!-- PROPERTY OWNER -->
        @if(Auth::user()->role === 'property_owner')
        <div class="section-header">Property Zone</div>
        <a href="{{ route('warehouses.index') }}" class="sidebar-link {{ request()->routeIs('warehouses.index') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i> My Properties
        </a>
        <a href="{{ route('warehouses.create') }}" class="sidebar-link {{ request()->routeIs('warehouses.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i> Register Property
        </a>
        <a href="{{ route('property.pending') }}" class="sidebar-link {{ request()->routeIs('property.pending') ? 'active' : '' }}">
            <i class="fas fa-clock"></i> Pending
        </a>
        <a href="{{ route('property.approved') }}" class="sidebar-link {{ request()->routeIs('property.approved') ? 'active' : '' }}">
            <i class="fas fa-check-circle"></i> Approved
        </a>
        <a href="{{ route('property.rejected') }}" class="sidebar-link {{ request()->routeIs('property.rejected') ? 'active' : '' }}">
            <i class="fas fa-times-circle"></i> Rejected
        </a>
        <a href="{{ route('property.requests.index') }}" class="sidebar-link {{ request()->routeIs('property.requests.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Requests
        </a>
        <a href="{{ route('property.analytics') }}" class="sidebar-link {{ request()->routeIs('property.analytics') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Analytics
        </a>
        @endif

        <!-- SECURITY AGENCY -->
        @if(Auth::user()->role === 'security_agency')
        <div class="section-header">Security Zone</div>
        <a href="{{ route('security.dashboard') }}" class="sidebar-link {{ request()->routeIs('security.dashboard') ? 'active' : '' }}">
            <i class="fas fa-shield-alt"></i> Security Dashboard
        </a>
        <a href="{{ route('security.personnel.index') }}" class="sidebar-link {{ request()->routeIs('security.personnel.*') ? 'active' : '' }}">
            <i class="fas fa-user-plus"></i> Add Personnel
        </a>
        <a href="{{ route('security.goods.index') }}" class="sidebar-link {{ request()->routeIs('security.goods.*') ? 'active' : '' }}">
            <i class="fas fa-boxes"></i> My Goods
        </a>
        <a href="{{ route('security.assignments.index') }}" class="sidebar-link {{ request()->routeIs('security.assignments.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Assignments
        </a>
        @endif

        <!-- ACCOUNT -->
        <div class="section-header">Account</div>
        @if(Auth::user()->role === 'security_agency')
            <a href="{{ route('security.profile') }}" class="sidebar-link {{ request()->routeIs('security.profile*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i> Agency Profile
            </a>
        @else
            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
        @endif
        <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
            <i class="fas fa-bell"></i> Notifications
            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
            @if($unreadCount > 0) <span class="badge">{{ $unreadCount }}</span> @endif
        </a>
        <a href="{{ route('reminders.index') }}" class="sidebar-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Reminder Calendar
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="sidebar-link w-full"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
        @endauth
    </nav>
</div>

<!-- ============================================================ -->
<!-- MAIN CONTENT -->
<!-- ============================================================ -->
<div class="main-content">
    <div class="top-bar">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-sm">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Workspace</span>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">@yield('header', 'Dashboard')</span>
                </div>
                <p class="text-xs text-slate-500 font-medium hidden sm:block">KTM-WDC Logistics &bull; Active Operational Session</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <!-- NOTIFICATION BELL WITH DROPDOWN -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="notificationDropdownToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; display: inline-block; padding: 0;">
                    <i class="fas fa-bell text-gray-500 text-xl cursor-pointer hover:text-orange-500 transition"></i>
                    @auth
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill" id="unreadCount" style="position: absolute; top: -5px; right: -5px; font-size: 10px; min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    @endauth
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdownToggle">
                    <li class="dropdown-header"><i class="fas fa-bell me-2"></i>Notifications</li>
                    @auth
                        @if(auth()->user()->notifications->count() > 0)
                            @foreach(auth()->user()->notifications->take(10) as $notification)
                                <li>
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item notification-item" data-id="{{ $notification->id }}">
                                        <div>
                                            <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                            <div class="notification-text">{{ $notification->data['message'] ?? '' }}</div>
                                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li class="dropdown-footer">
                                <a href="{{ route('notifications.index') }}" class="text-decoration-none small">View All</a>
                            </li>
                        @else
                            <li class="dropdown-item text-center text-muted">No notifications</li>
                        @endif
                    @endauth
                </ul>
            </div>

            <span class="text-sm text-gray-600 hidden sm:inline">{{ now()->format('F j, Y') }}</span>
        </div>
    </div>
    
    @if(session('success')) <div class="alert alert-success mx-4 mt-4">{{ session('success') }} <button type="button" class="float-right" onclick="this.parentElement.style.display='none'">&times;</button></div> @endif
    @if(session('error')) <div class="alert alert-danger mx-4 mt-4">{{ session('error') }} <button type="button" class="float-right" onclick="this.parentElement.style.display='none'">&times;</button></div> @endif
    @if(session('warning')) <div class="alert alert-warning mx-4 mt-4">{{ session('warning') }} <button type="button" class="float-right" onclick="this.parentElement.style.display='none'">&times;</button></div> @endif
    @if(session('info')) <div class="alert alert-info mx-4 mt-4">{{ session('info') }} <button type="button" class="float-right" onclick="this.parentElement.style.display='none'">&times;</button></div> @endif
    
    <div class="page-content">
        @yield('content')
        <footer class="kwdc-portal-footer mt-12 pt-6 pb-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-700"><i class="fas fa-warehouse text-orange-500 mr-1"></i> KTM-WDC</span>
                <span>&bull;</span>
                <span>&copy; {{ date('Y') }} Warehouse & Distribution Connect</span>
            </div>
            <div class="flex items-center gap-5 font-medium">
                <a href="{{ route('privacy-policy') }}" class="hover:text-orange-600 transition">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}" class="hover:text-orange-600 transition">Terms of Service</a>
                <a href="{{ route('compliance') }}" class="hover:text-orange-600 transition">Compliance & Safety</a>
            </div>
        </footer>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/kwdc-flow.js') }}" defer></script>
<script>
    function toggleMobileMenu() {
        const sidebar = document.getElementById('sidebar');
        const open = sidebar.classList.toggle('mobile-open');
        document.getElementById('sidebarOverlay').classList.toggle('active', open);
        const toggle = document.querySelector('.menu-toggle');
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
        if (open) sidebar.querySelector('a')?.focus();
        else toggle.focus();
    }
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && document.getElementById('sidebar').classList.contains('mobile-open')) toggleMobileMenu();
    });
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(function(alert) { 
            setTimeout(function() { 
                alert.style.transition = 'opacity 0.5s ease'; 
                alert.style.opacity = '0'; 
                setTimeout(function() { alert.style.display = 'none'; }, 500); 
            }, 5000); 
        });
        const links = document.querySelectorAll('.sidebar-link');
        links.forEach(function(link) { 
            link.addEventListener('click', function() { 
                if (window.innerWidth <= 768) { toggleMobileMenu(); } 
            }); 
        });

        // ===== MARK NOTIFICATION AS READ =====
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const id = this.dataset.id;
                if (id) {
                    fetch('/notifications/' + id + '/mark-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Remove badge count
                            const badge = document.getElementById('unreadCount');
                            if (badge) {
                                let count = parseInt(badge.textContent) || 0;
                                count--;
                                if (count > 0) {
                                    badge.textContent = count;
                                } else {
                                    badge.style.display = 'none';
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error marking notification as read:', error));
                }
            });
        });
    });

    // ===== REAL-TIME NOTIFICATIONS (Laravel Echo) =====
    @auth
    const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
    if (userId) {
        const loadScript = (src) => new Promise((resolve, reject) => {
            const existing = document.querySelector(`script[src="${src}"]`);
            if (existing) {
                existing.addEventListener('load', resolve, { once: true });
                existing.addEventListener('error', reject, { once: true });
                if (existing.dataset.loaded === 'true') resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            script.onload = () => {
                script.dataset.loaded = 'true';
                resolve();
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });

        const startRealtimeNotifications = async () => {
            try {
                if (typeof window.Echo === 'undefined') {
                    await loadScript('https://js.pusher.com/8.2.0/pusher.min.js');
                    window.Pusher = window.Pusher || window.pusher;
                    await loadScript('https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js');
                }

                if (typeof window.Echo === 'undefined' && typeof Echo !== 'undefined') {
                    window.Echo = Echo;
                }

                if (typeof window.Echo === 'function') {
                    window.Echo = new window.Echo({
                        broadcaster: 'reverb',
                        key: @json(config('broadcasting.connections.reverb.key')),
                        wsHost: @json(config('broadcasting.connections.reverb.options.host', 'localhost')),
                        wsPort: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
                        forceTLS: @json((bool) config('broadcasting.connections.reverb.options.useTLS', false)),
                        enabledTransports: ['ws', 'wss'],
                    });
                }

                if (window.Echo?.private) {
                    setupEcho();
                }
            } catch (error) {
                console.warn('Realtime notifications are unavailable on this page.', error);
            }
        };

        startRealtimeNotifications();

        function setupEcho() {
            window.Echo.private('notifications.' + userId)
                .notification((notification) => {
                    // Add to dropdown
                    const dropdown = document.querySelector('.notification-dropdown');
                    if (dropdown) {
                        const newItem = document.createElement('li');
                        newItem.innerHTML = `
                            <a href="${notification.url || '#'}" class="dropdown-item notification-item">
                                <div>
                                    <div class="notification-title">${notification.title || 'New'}</div>
                                    <div class="notification-text">${notification.message || ''}</div>
                                    <div class="notification-time">Just now</div>
                                </div>
                            </a>
                        `;
                        // Insert after header
                        const header = dropdown.querySelector('.dropdown-header');
                        if (header) {
                            dropdown.insertBefore(newItem, header.nextSibling);
                        } else {
                            dropdown.prepend(newItem);
                        }
                        // Update badge
                        const badge = document.getElementById('unreadCount');
                        if (badge) {
                            let count = parseInt(badge.textContent) || 0;
                            badge.textContent = count + 1;
                            badge.style.display = 'flex';
                        } else {
                            const toggle = document.querySelector('#notificationDropdownToggle');
                            if (toggle) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'badge bg-danger rounded-pill';
                                newBadge.id = 'unreadCount';
                                newBadge.style.cssText = 'position: absolute; top: -5px; right: -5px; font-size: 10px; min-width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;';
                                newBadge.textContent = '1';
                                toggle.appendChild(newBadge);
                            }
                        }
                    }
                });
        }
    }
    @endauth
</script>
@stack('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- ============================================================ -->
<!-- KWDC ASSISTANT -->
<!-- ============================================================ -->
<style>
    .kwdc-assistant {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .kwdc-assistant-panel {
        width: 380px;
        max-width: calc(100vw - 32px);
        height: 540px;
        max-height: calc(100vh - 100px);
        display: none;
        flex-direction: column;
        position: absolute;
        bottom: 74px;
        right: 0;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.22);
        animation: kwdcPanelIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes kwdcPanelIn {
        from { opacity: 0; transform: translateY(12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .kwdc-assistant-header {
        padding: 16px 18px;
        color: #ffffff;
        background: #090e17;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .kwdc-assistant-title {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .kwdc-assistant-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f97316 0%, #d97706 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 14px;
        flex-shrink: 0;
        box-shadow: 0 2px 10px rgba(249, 115, 22, 0.3);
    }
    .kwdc-assistant-title strong {
        display: block;
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.2;
    }
    .kwdc-assistant-title span {
        display: block;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.2;
    }
    .kwdc-assistant-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .kwdc-assistant-select {
        height: 28px;
        background: rgba(255, 255, 255, 0.08);
        color: #f1f5f9;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 8px;
        padding: 0 8px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        outline: none;
    }
    .kwdc-assistant-select option {
        background: #0f172a;
        color: #ffffff;
    }
    .kwdc-assistant-close {
        color: #94a3b8;
        background: transparent;
        border: 0;
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kwdc-assistant-close:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }
    .kwdc-assistant-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .kwdc-assistant-msg {
        display: flex;
        flex-direction: column;
        max-width: 88%;
    }
    .kwdc-assistant-msg.user-msg {
        align-self: flex-end;
        align-items: flex-end;
    }
    .kwdc-assistant-msg.assistant-msg {
        align-self: flex-start;
        align-items: flex-start;
    }
    .kwdc-assistant-bubble {
        padding: 10px 14px;
        border-radius: 16px;
        line-height: 1.45;
        font-size: 13px;
        white-space: pre-line;
    }
    .assistant-msg .kwdc-assistant-bubble {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .user-msg .kwdc-assistant-bubble {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #ffffff;
        border-bottom-right-radius: 4px;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25);
    }
    .kwdc-quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        margin-top: 8px;
        width: 100%;
    }
    .kwdc-quick-action {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        border-radius: 12px;
        padding: 8px 10px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kwdc-quick-action:hover {
        border-color: #f97316;
        color: #ea580c;
        background: #fff7ed;
        transform: translateY(-1px);
    }
    .kwdc-assistant-footer {
        padding: 12px 14px;
        border-top: 1px solid #f1f5f9;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .kwdc-assistant-status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        color: #64748b;
        padding: 0 4px;
    }
    .kwdc-assistant-mic {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .kwdc-assistant-mic:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .kwdc-assistant-mic.listening {
        background: #ef4444;
        border-color: #dc2626;
        color: #ffffff;
        animation: kwdcPulse 1.2s infinite;
    }
    @keyframes kwdcPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .kwdc-assistant-status {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
    }
    .kwdc-assistant-input-row {
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        padding: 4px 6px 4px 12px;
        transition: all 0.15s ease;
    }
    .kwdc-assistant-input-row:focus-within {
        border-color: #f97316;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    .kwdc-assistant-input-row input {
        flex: 1;
        border: 0;
        background: transparent;
        min-width: 0;
        padding: 6px 4px;
        font-size: 13px;
        color: #0f172a;
        outline: none;
    }
    .kwdc-assistant-send {
        width: 32px;
        height: 32px;
        border: 0;
        background: #f97316;
        color: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }
    .kwdc-assistant-send:hover {
        background: #ea580c;
        transform: scale(1.05);
    }
    .kwdc-assistant-launch {
        width: 54px;
        height: 54px;
        background: #090e17;
        border: 2px solid rgba(249, 115, 22, 0.4);
        color: #f97316;
        font-size: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .kwdc-assistant-launch:hover {
        transform: scale(1.08);
        border-color: #f97316;
        color: #ffffff;
        background: #f97316;
        box-shadow: 0 10px 30px rgba(249, 115, 22, 0.4);
    }
    @media (max-width: 640px) {
        .kwdc-assistant {
            right: 16px;
            bottom: 18px;
        }
        .kwdc-assistant-panel {
            right: -6px;
            width: calc(100vw - 24px);
            height: min(540px, calc(100vh - 90px));
        }
    }
</style>

<div id="voice-assistant-container" class="kwdc-assistant">
    <div class="kwdc-assistant-panel" id="voiceChatWindow">
        <div class="kwdc-assistant-header">
            <div class="kwdc-assistant-title">
                <div class="kwdc-assistant-avatar">
                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 16px; height: 16px; color: #f97316;">
                        <path d="M12 2L14.4 7.6L20 10L14.4 12.4L12 18L9.6 12.4L4 10L9.6 7.6L12 2Z" />
                        <path d="M19 15L20.2 17.8L23 19L20.2 20.2L19 23L17.8 20.2L15 19L17.8 17.8L19 15Z" opacity="0.8" />
                        <path d="M5 16L5.9 18.1L8 19L5.9 19.9L5 22L4.1 19.9L2 19L4.1 18.1L5 16Z" opacity="0.6" />
                    </svg>
                </div>
                <div>
                    <strong>KWDC Assistant</strong>
                    <span>Kathmandu Logistics AI</span>
                </div>
            </div>
            <div class="kwdc-assistant-actions">
                <select id="voiceLangSelect" class="kwdc-assistant-select">
                    <option value="en">EN</option>
                    <option value="np">NP</option>
                </select>
                <button id="voiceSpeechToggle" class="kwdc-assistant-close" aria-label="Toggle speech" title="Audible Responses"><i class="fas fa-volume-up"></i></button>
                <button id="assistantClearBtn" class="kwdc-assistant-close" aria-label="Clear chat" title="Reset"><i class="fas fa-rotate-left"></i></button>
                <button id="voiceCloseBtn" class="kwdc-assistant-close" aria-label="Close" title="Close"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="kwdc-assistant-messages" id="voiceMessages">
            <div class="kwdc-assistant-msg assistant-msg">
                <div class="kwdc-assistant-bubble">
                    Namaste! How can I assist your logistics today?
                </div>
                <div class="kwdc-quick-actions" id="assistantQuickActions">
                    <button type="button" class="kwdc-quick-action" data-prompt="Create a pickup from Boudha to Bhaktapur">Pickup</button>
                    <button type="button" class="kwdc-quick-action" data-prompt="Create a dispatch from Kathmandu to Pokhara">Dispatch</button>
                    <button type="button" class="kwdc-quick-action" data-prompt="Track my dispatch order">Tracking</button>
                    <button type="button" class="kwdc-quick-action" data-prompt="Remind me to call the driver tomorrow at 5 PM">Reminder</button>
                </div>
            </div>
        </div>

        <div class="kwdc-assistant-footer">
            <div class="kwdc-assistant-status-row">
                <button id="voiceToggleBtn" class="kwdc-assistant-mic"><i class="fas fa-microphone"></i> <span>Voice</span></button>
                <span id="voiceStatus" class="kwdc-assistant-status">Speak or type a prompt...</span>
            </div>
            <div class="kwdc-assistant-input-row">
                <input type="text" id="voiceTextInput" placeholder="Ask for pickup, dispatch, tracking, reminder...">
                <button id="voiceSendBtn" class="kwdc-assistant-send" aria-label="Send message"><i class="fas fa-arrow-up"></i></button>
            </div>
        </div>
    </div>

    <button id="voiceLaunchBtn" class="kwdc-assistant-launch" aria-label="Open KWDC assistant" title="KWDC Logistics AI Assistant">
        <svg viewBox="0 0 24 24" fill="currentColor" style="width: 26px; height: 26px; color: #f97316;">
            <path d="M12 2L14.4 7.6L20 10L14.4 12.4L12 18L9.6 12.4L4 10L9.6 7.6L12 2Z" />
            <path d="M19 15L20.2 17.8L23 19L20.2 20.2L19 23L17.8 20.2L15 19L17.8 17.8L19 15Z" opacity="0.8" />
            <path d="M5 16L5.9 18.1L8 19L5.9 19.9L5 22L4.1 19.9L2 19L4.1 18.1L5 16Z" opacity="0.6" />
        </svg>
    </button>
</div>

<script src="{{ asset('js/voice-assistant.js') }}"></script>
</body>
</html>
