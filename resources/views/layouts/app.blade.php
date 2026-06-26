<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM-WDC - @yield('title', 'Warehouse & Distribution Connect')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* Sidebar - Always visible, never slides */
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
        }
        
        /* Main content - Always has margin */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: #f3f4f6;
        }
        
        /* Sidebar scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #374151;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 10px;
        }
        
        /* Sidebar links */
        .sidebar-link {
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 8px;
            margin: 4px 8px;
            color: #9ca3af;
            text-decoration: none;
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
        
        .sidebar-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Section headers */
        .section-header {
            font-size: 10px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 16px 4px 16px;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-approved { background: #d1fae5; color: #059669; }
        .status-rejected { background: #fee2e2; color: #dc2626; }
        .status-assigned { background: #dbeafe; color: #2563eb; }
        .status-delivered { background: #d1fae5; color: #059669; }
        .status-on_the_way { background: #fed7aa; color: #ea580c; }
        
        /* Top bar */
        .top-bar {
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 40;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-content {
            padding: 24px;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                width: 260px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .menu-toggle {
                display: block;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 60;
                background: #f59e0b;
                color: white;
                padding: 8px 12px;
                border-radius: 8px;
                cursor: pointer;
                border: none;
            }
            .top-bar {
                padding-left: 70px;
            }
        }
        
        .menu-toggle {
            display: none;
        }
        
        @media (min-width: 769px) {
            .menu-toggle {
                display: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Mobile Menu Toggle -->
<button class="menu-toggle" onclick="toggleMobileMenu()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-warehouse text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold">KTM-WDC</h1>
                <p class="text-xs text-gray-400">Warehouse & Distribution</p>
            </div>
        </div>
    </div>
    
    <!-- User Info -->
    @auth
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div>
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                <span class="text-xs text-orange-400">{{ ucfirst(Auth::user()->role ?? 'User') }}</span>
            </div>
        </div>
    </div>
    @endauth
    
    <!-- Navigation -->
    <nav class="py-4">
        @auth
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="sidebar-link">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Admin Panel -->
        @if(Auth::user()->is_admin || Auth::user()->role == 'admin')
        <div class="section-header">Admin Panel</div>
        <a href="{{ route('admin.pending') }}" class="sidebar-link">
            <i class="fas fa-clock"></i>
            <span>Pending Approvals</span>
        </a>
        <a href="{{ route('admin.all-warehouses') }}" class="sidebar-link">
            <i class="fas fa-warehouse"></i>
            <span>All Warehouses</span>
        </a>
        <a href="{{ route('admin.requests') }}" class="sidebar-link">
            <i class="fas fa-clipboard-list"></i>
            <span>Client Requests</span>
        </a>
        <a href="{{ route('admin.vehicles') }}" class="sidebar-link">
            <i class="fas fa-truck"></i>
            <span>All Vehicles</span>
        </a>
        <a href="{{ route('admin.dispatch') }}" class="sidebar-link">
            <i class="fas fa-truck-moving"></i>
            <span>Dispatch Orders</span>
        </a>
        <a href="{{ route('admin.clients') }}" class="sidebar-link">
            <i class="fas fa-users"></i>
            <span>All Clients</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="sidebar-link">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
        @endif
        
        <!-- Client Zone -->
        @if(Auth::user()->is_client || Auth::user()->role == 'client')
        <div class="section-header">Client Zone</div>
        <a href="{{ route('my-requests.index') }}" class="sidebar-link">
            <i class="fas fa-clipboard-list"></i>
            <span>My Requests</span>
        </a>
        <a href="{{ route('my-requests.create') }}" class="sidebar-link">
            <i class="fas fa-plus-circle"></i>
            <span>New Request</span>
        </a>
        <a href="{{ route('dispatch.index') }}" class="sidebar-link">
            <i class="fas fa-truck"></i>
            <span>Dispatch Orders</span>
        </a>
        <a href="{{ route('pickup.index') }}" class="sidebar-link">
            <i class="fas fa-box-open"></i>
            <span>Pickup Requests</span>
        </a>
        <a href="{{ route('stock.index') }}" class="sidebar-link">
            <i class="fas fa-boxes"></i>
            <span>My Stock</span>
        </a>
        <a href="{{ route('profile.contacts') }}" class="sidebar-link">
            <i class="fas fa-address-book"></i>
            <span>Manage Contacts</span>
        </a>
        @endif
        
        <!-- Driver Zone -->
        @if(Auth::user()->is_driver || Auth::user()->role == 'driver')
        <div class="section-header">Driver Zone</div>
        <a href="{{ route('driver.jobs') }}" class="sidebar-link">
            <i class="fas fa-tasks"></i>
            <span>My Jobs</span>
        </a>
        <a href="{{ route('driver.available-jobs') }}" class="sidebar-link">
            <i class="fas fa-search"></i>
            <span>Available Jobs</span>
        </a>
        <a href="{{ route('driver.vehicles.index') }}" class="sidebar-link">
            <i class="fas fa-truck"></i>
            <span>My Vehicle</span>
        </a>
        <a href="{{ route('driver.earnings') }}" class="sidebar-link">
            <i class="fas fa-chart-line"></i>
            <span>My Earnings</span>
        </a>
        <a href="{{ route('driver.rates') }}" class="sidebar-link">
            <i class="fas fa-tag"></i>
            <span>My Rates</span>
        </a>
        @endif
        
        <!-- Equipment Owner Zone -->
        @if(Auth::user()->is_equipment_owner || Auth::user()->role == 'equipment_owner')
        <div class="section-header">Equipment Zone</div>
        <a href="{{ route('equipment.dashboard') }}" class="sidebar-link">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('equipment.register') }}" class="sidebar-link">
            <i class="fas fa-plus-circle"></i>
            <span>Register Equipment</span>
        </a>
        <a href="{{ route('equipment.jobs') }}" class="sidebar-link">
            <i class="fas fa-briefcase"></i>
            <span>Equipment Jobs</span>
        </a>
        <a href="{{ route('equipment.list') }}" class="sidebar-link">
            <i class="fas fa-tools"></i>
            <span>My Equipment</span>
        </a>
        @endif
        
        <!-- Property Owner Zone -->
        @if(Auth::user()->is_property_owner || Auth::user()->role == 'property_owner')
        <div class="section-header">Property Zone</div>
        <a href="{{ route('warehouses.create') }}" class="sidebar-link">
            <i class="fas fa-plus-circle"></i>
            <span>Register Warehouse</span>
        </a>
        <a href="{{ route('warehouses.index') }}" class="sidebar-link">
            <i class="fas fa-warehouse"></i>
            <span>My Warehouses</span>
        </a>
        @endif
        
        <!-- Account -->
        <div class="section-header">Account</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
        
        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="sidebar-link w-full">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
        
        @else
        <!-- Guest Links -->
        <a href="{{ route('login') }}" class="sidebar-link">
            <i class="fas fa-sign-in-alt"></i>
            <span>Login</span>
        </a>
        <a href="{{ route('register') }}" class="sidebar-link">
            <i class="fas fa-user-plus"></i>
            <span>Register</span>
        </a>
        @endauth
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
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
            <i class="fas fa-bell text-gray-500 text-xl cursor-pointer hover:text-orange-500 transition"></i>
            <span class="text-sm text-gray-600">{{ now()->format('F j, Y') }}</span>
        </div>
    </div>
    
    <!-- Page Content -->
    <div class="page-content">
        @yield('content')
    </div>
</div>

<script>
    function toggleMobileMenu() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
    }
</script>

@stack('scripts')
</body>
</html>