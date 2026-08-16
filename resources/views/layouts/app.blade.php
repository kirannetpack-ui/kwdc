<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->check() ? auth()->id() : '' }}">
    <title>KTM-WDC - @yield('title', 'Warehouse & Distribution Connect')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="anonymous" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: #111827;
            color: white;
            z-index: 50;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
        }
        
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-track { background: #374151; }
        .sidebar::-webkit-scrollbar-thumb { background: #f59e0b; border-radius: 10px; }
        
        /* ===== SIDEBAR LINKS ===== */
        .sidebar-link {
            transition: all 0.3s ease;
            display: flex !important;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            margin: 4px 8px;
            color: #9ca3af;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            width: auto;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        
        .sidebar-link:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-link.active {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .sidebar-link i { width: 20px; text-align: center; font-size: 16px; flex-shrink: 0; }
        .sidebar-link .badge { background: #ef4444; color: white; font-size: 10px; padding: 2px 8px; border-radius: 10px; margin-left: auto; flex-shrink: 0; }
        .section-header { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; padding: 12px 16px 4px 16px; display: block !important; }
        
        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: 280px; min-height: 100vh; background: #f3f4f6; }
        .top-bar { background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 16px 24px; position: sticky; top: 0; z-index: 40; display: flex; justify-content: space-between; align-items: center; }
        .page-content { padding: 24px; max-width: 1400px; margin: 0 auto; }
        .menu-toggle { display: none; position: fixed; top: 16px; left: 16px; z-index: 60; background: #f59e0b; color: white; padding: 10px 14px; border-radius: 8px; cursor: pointer; border: none; font-size: 18px; }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 280px; }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block; }
            .top-bar { padding: 12px 16px 12px 70px; flex-wrap: wrap; }
            .top-bar h2 { font-size: 18px; }
            .page-content { padding: 16px; }
        }
        
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 45; }
        .sidebar-overlay.active { display: block; }
        
        .alert { padding: 12px 20px; border-radius: 8px; margin-bottom: 16px; border: none; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-warning { background: #fef3c7; color: #92400e; }
        .alert-info { background: #dbeafe; color: #1e40af; }

        /* ===== NOTIFICATION DROPDOWN ===== */
        .notification-dropdown {
            width: 380px;
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        .notification-dropdown .dropdown-header {
            background: #f8fafc;
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: #1e293b;
        }
        .notification-dropdown .dropdown-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            white-space: normal;
            line-height: 1.4;
        }
        .notification-dropdown .dropdown-item:hover {
            background: #f1f5f9;
        }
        .notification-dropdown .dropdown-item .notification-title {
            font-weight: 600;
            color: #0f172a;
        }
        .notification-dropdown .dropdown-item .notification-text {
            font-size: 13px;
            color: #64748b;
            margin: 2px 0;
        }
        .notification-dropdown .dropdown-item .notification-time {
            font-size: 11px;
            color: #94a3b8;
        }
        .notification-dropdown .dropdown-footer {
            padding: 10px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            background: #f8fafc;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Mobile Menu Toggle -->
<button class="menu-toggle" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleMobileMenu()"></div>

<!-- ============================================================ -->
<!-- SIDEBAR -->
<!-- ============================================================ -->
<div class="sidebar" id="sidebar">
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-warehouse text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">KTM-WDC</h1>
                <p class="text-xs text-gray-400">Warehouse & Distribution</p>
            </div>
        </div>
    </div>
    
    @auth
    <div class="p-4 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-sm truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                <span class="text-xs text-orange-400">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
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
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
            @auth
            <div class="flex items-center mt-1">
                <span class="text-xl mr-2">🙏</span>
                <p class="text-gray-600">
                    <span class="font-semibold">Namaste</span>, 
                    <span class="text-orange-600 font-semibold">{{ Auth::user()->name }}</span>
                    <span class="text-gray-500 text-sm ml-2">({{ ucfirst(Auth::user()->role ?? 'User') }})</span>
                </p>
            </div>
            @endauth
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
    
    <div class="page-content">@yield('content')</div>
</div>

<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleMobileMenu() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
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
                        key: '{{ env('REVERB_APP_KEY') }}',
                        wsHost: '{{ env('REVERB_HOST', 'localhost') }}',
                        wsPort: {{ env('REVERB_PORT', 8080) }},
                        forceTLS: false,
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
<!-- AI TOOLS (CHAT & VOICE) – Keep your existing code -->
<!-- ============================================================ -->
<div id="ai-tools-container" style="position: fixed; bottom: 40px; right: 25px; z-index: 99999; display: flex; flex-direction: column; align-items: flex-end; gap: 15px; pointer-events: none;">
    <div style="pointer-events: auto;">
        <!-- CHAT -->
        <div style="position: relative; margin-bottom: 15px;">
            <button id="ai-chat-btn" class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: none; color: white; transition: all 0.3s ease;">
                <i id="chat-icon-open" class="fas fa-comment-dots fa-lg"></i>
                <i id="chat-icon-close" class="fas fa-times fa-lg d-none"></i>
            </button>
            <div id="ai-chat-window" class="d-none shadow-lg" style="width: 380px; max-width: 90vw; height: 550px; max-height: 80vh; border-radius: 16px; background: white; position: absolute; bottom: 75px; right: 0; display: flex; flex-direction: column; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 16px 20px; color: white; display: flex; align-items: center; justify-content: space-between;">
                    <div class="d-flex align-items-center">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #f59e0b; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-weight: bold; font-size: 18px;">AI</div>
                        <div><h6 class="mb-0 fw-bold" style="font-size: 14px;">KTM-WDC Assistant</h6><span style="font-size: 11px; color: #94a3b8;"><span style="display: inline-block; width: 8px; height: 8px; background: #22c55e; border-radius: 50%; margin-right: 6px;"></span> Online</span></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select id="lang-selector" class="form-select form-select-sm bg-dark text-white border-0" style="width: 95px; font-size: 12px; padding: 2px 8px;">
                            <option value="English" selected>🇬🇧 English</option>
                            <option value="Nepali">🇳🇵 Nepali</option>
                            <option value="Hindi">🇮🇳 Hindi</option>
                        </select>
                        <button id="ai-chat-close-btn" class="btn btn-sm btn-link text-white p-0 opacity-75 hover:opacity-100" style="text-decoration: none;"><i class="fas fa-chevron-down"></i></button>
                    </div>
                </div>
                <div id="ai-chat-messages" style="flex: 1; padding: 20px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column;">
                    <div class="mb-3 d-flex align-items-start">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0; font-size: 12px; color: #64748b;">AI</div>
                        <div style="background: white; padding: 10px 14px; border-radius: 12px 12px 12px 0; max-width: 80%; box-shadow: 0 1px 3px rgba(0,0,0,0.05); color: #334155; font-size: 14px; line-height: 1.5;">Hello! 👋 I'm your KTM-WDC AI assistant.</div>
                    </div>
                    <div id="typing-indicator" class="d-none mb-3 d-flex align-items-start">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0; font-size: 12px; color: #64748b;">AI</div>
                        <div style="background: white; padding: 10px 14px; border-radius: 12px 12px 12px 0; max-width: 60%; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 4px;">
                            <div style="width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; animation: dot-typing 1.4s infinite;"></div>
                            <div style="width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; animation: dot-typing 1.4s 0.2s infinite;"></div>
                            <div style="width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; animation: dot-typing 1.4s 0.4s infinite;"></div>
                        </div>
                    </div>
                </div>
                <div style="padding: 16px; border-top: 1px solid #e2e8f0; background: white;">
                    <div class="input-group">
                        <input type="text" id="ai-chat-input" class="form-control" placeholder="Type your question..." style="border: 1px solid #e2e8f0; border-right: none; border-radius: 8px 0 0 8px; padding: 10px 14px; font-size: 14px; outline: none;">
                        <button id="ai-chat-send" class="btn btn-primary" style="background: #f59e0b; border: 1px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 0 16px;"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- VOICE -->
        <button id="voice-btn" class="btn btn-lg rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: #f59e0b; border: none; transition: all 0.3s ease;"><i class="fas fa-microphone text-white" style="font-size: 22px;"></i></button>
    </div>
</div>

<style>@keyframes dot-typing { 0%, 60%, 100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const langSelector = document.getElementById('lang-selector');
    const chatMessages = document.getElementById('ai-chat-messages');
    const typingInd = document.getElementById('typing-indicator');

    // CHAT
    const chatBtn = document.getElementById('ai-chat-btn');
    const chatWindow = document.getElementById('ai-chat-window');
    const openIcon = document.getElementById('chat-icon-open');
    const closeIcon = document.getElementById('chat-icon-close');
    const closeBtn = document.getElementById('ai-chat-close-btn');
    const chatInput = document.getElementById('ai-chat-input');
    const chatSend = document.getElementById('ai-chat-send');

    function toggleChat() {
        const isHidden = chatWindow.classList.contains('d-none');
        chatWindow.classList.toggle('d-none');
        openIcon.classList.toggle('d-none');
        closeIcon.classList.toggle('d-none');
        if (!isHidden) { chatMessages.scrollTop = chatMessages.scrollHeight; chatInput.focus(); }
    }

    chatBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);
    chatSend.addEventListener('click', sendChatMessage);
    chatInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') sendChatMessage(); });

    async function sendChatMessage() {
        const text = chatInput.value.trim();
        const language = langSelector.value;
        if (!text) return;

        chatMessages.appendChild(createBubble(text, 'user'));
        chatInput.value = '';
        chatMessages.scrollTop = chatMessages.scrollHeight;
        typingInd.classList.remove('d-none');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        try {
            const response = await fetch('/ai/chat-support', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ message: text, language: language })
            });
            const data = await response.json();
            typingInd.classList.add('d-none');
            if ((data.action === 'redirect' || data.action === 'open_page') && data.url) {
                chatMessages.appendChild(createBubble("🔄 " + (data.message || "Opening page..."), 'bot'));
                chatMessages.scrollTop = chatMessages.scrollHeight;
                const params = new URLSearchParams(data.data || {});
                const targetUrl = params.toString() ? `${data.url}?${params.toString()}` : data.url;
                window.location.href = targetUrl;
                return;
            }
            chatMessages.appendChild(createBubble(data.message || "I'm experiencing technical issues.", 'bot'));
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (e) {
            typingInd.classList.add('d-none');
            chatMessages.appendChild(createBubble("Network error.", 'bot'));
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    function createBubble(text, sender) {
        const div = document.createElement('div');
        div.className = 'mb-3 d-flex align-items-start';
        function formatMessage(txt) {
            if (!txt) return '';
            let cleanText = txt.replace(/\*\*/g, '').replace(/__/g, '');
            let lines = cleanText.split('\n').filter(p => p.trim() !== '');
            let listItems = [], regularText = [];
            for (let line of lines) {
                if (line.trim().startsWith('- ') || line.trim().startsWith('* ')) listItems.push(line.trim().substring(2));
                else regularText.push(line);
            }
            let html = '';
            if (regularText.length > 0) html += regularText.join('<br>');
            if (listItems.length > 0) {
                html += '<ul style="padding-left: 20px; margin-top: 5px; margin-bottom: 0;">';
                for (let item of listItems) html += `<li style="margin-bottom: 4px;">${item}</li>`;
                html += '</ul>';
            }
            return html;
        }
        if (sender === 'user') {
            div.classList.add('flex-row-reverse');
            div.innerHTML = `<div style="background: #f59e0b; padding: 10px 14px; border-radius: 12px 12px 0 12px; max-width: 80%; color: white; font-size: 14px; line-height: 1.5; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">${text}</div>`;
        } else {
            div.innerHTML = `<div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0; font-size: 12px; color: #64748b;">AI</div>
                <div style="background: white; padding: 10px 14px; border-radius: 12px 12px 12px 0; max-width: 80%; box-shadow: 0 1px 3px rgba(0,0,0,0.05); color: #334155; font-size: 14px; line-height: 1.5;">${formatMessage(text)}</div>`;
        }
        return div;
    }

    // VOICE
    const voiceBtn = document.getElementById('voice-btn');
    if (!voiceBtn) return;
    voiceBtn.addEventListener('click', function() {
        document.getElementById('voiceLaunchBtn')?.click();
    });
});


</script>

<!-- ============================================================ -->
<!-- VOICE ASSISTANT – Floating Button + Chat Window -->
<!-- ============================================================ -->
<div id="voice-assistant-container" style="position: fixed; bottom: 40px; right: 25px; z-index: 99999; display: flex; flex-direction: column; align-items: flex-end;">
    <div class="voice-chat-window" id="voiceChatWindow" style="background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); overflow: hidden; width: 380px; max-width: 90vw; max-height: 80vh; display: none; flex-direction: column; position: absolute; bottom: 75px; right: 0;">
        <div style="background: linear-gradient(135deg, #1e293b, #0f172a); padding: 14px 20px; color: white; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <i class="fas fa-microphone-alt me-2"></i> Voice Assistant
        <select id="voiceLangSelect" style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; padding: 2px 6px; font-size: 11px; margin-left: 8px;">
            <option value="en" style="background: #1e293b;">🇬🇧 English</option>
            <option value="np" style="background: #1e293b;">🇳🇵 Nepali</option>
        </select>
    </div>
    <button id="voiceCloseBtn" class="btn btn-sm btn-link text-white"><i class="fas fa-times"></i></button>
</div>
        <div class="voice-messages" id="voiceMessages" style="height: 350px; overflow-y: auto; padding: 16px; background: #f8fafc; display: flex; flex-direction: column;">
            <div class="assistant-msg">
                <div class="msg-bubble" style="background: #e5e7eb; color: #1e293b; padding: 10px 14px; border-radius: 12px; margin: 4px 0; max-width: 80%; align-self: flex-start;">
                   Tell me what you need. I can open and prefill pickup, dispatch, tracking, invoices, equipment, security, and reminders.
                </div>
            </div>
        </div>
        <div style="padding: 12px; border-top: 1px solid #e5e7eb; background: white;">
            <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 10px;">
                <button id="voiceToggleBtn" class="btn" style="background: #f59e0b; border: none; border-radius: 30px; padding: 8px 20px; color: white; font-weight: 600;"><i class="fas fa-microphone"></i> Start</button>
                <span id="voiceStatus" style="font-size: 13px; color: #64748b;">Click to speak or type below</span>
            </div>
            <div class="input-group">
                <input type="text" id="voiceTextInput" class="form-control" placeholder="Type a request, e.g. pickup from Boudha to Bhaktapur" style="border: 1px solid #e2e8f0; border-right: none; border-radius: 8px 0 0 8px; padding: 10px 12px; font-size: 14px;">
                <button id="voiceSendBtn" class="btn" style="background: #1e293b; border: 1px solid #1e293b; border-radius: 0 8px 8px 0; color: white;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
    <button id="voiceLaunchBtn" class="btn rounded-circle shadow-lg" style="width: 60px; height: 60px; background: #f59e0b; border: none; color: white; font-size: 28px; transition: all 0.2s; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);">
        <i class="fas fa-microphone-alt"></i>
    </button>
</div>

<script src="{{ asset('js/voice-assistant.js') }}"></script>
</body>
</html>
