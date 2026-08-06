<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\DispatchOrder;
use App\Models\PickupRequest;
use App\Models\WarehouseRequest;
use App\Models\Stock;
use App\Models\Vehicle;
use App\Models\Equipment;
use App\Models\EquipmentJob;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        
        // Get user data for sidebar
        $userData = [
            'user' => $user,
            'role' => $role,
            'unread_count' => Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ];
        
        switch($role) {
            case 'admin':
                return $this->adminDashboard($userData);
            case 'client':
                return $this->clientDashboard($userData);
            case 'driver':
                return $this->driverDashboard($userData);
            case 'property_owner':
                return $this->propertyOwnerDashboard($userData);
            case 'equipment_owner':
                return $this->equipmentOwnerDashboard($userData);
            default:
                return $this->defaultDashboard($userData);
        }
    }
    
    /**
     * Admin Dashboard
     */
    private function adminDashboard($userData)
    {
        $stats = [
            'warehouses' => Warehouse::count(),
            'approved_warehouses' => Warehouse::where('status', 'approved')->count(),
            'pending_warehouses' => Warehouse::where('status', 'pending')->count(),
            'clients' => User::where('role', 'client')->count(),
            'drivers' => User::where('role', 'driver')->count(),
            'property_owners' => User::where('role', 'property_owner')->count(),
            'equipment_owners' => User::where('role', 'equipment_owner')->count(),
            'vehicles' => Vehicle::count(),
            'total_dispatches' => DispatchOrder::count(),
            'pending_dispatches' => DispatchOrder::where('status', 'pending')->count(),
            'completed_dispatches' => DispatchOrder::where('status', 'delivered')->count(),
            'total_revenue' => DispatchOrder::sum('base_price') ?? 0,
            'pending_requests' => WarehouseRequest::where('status', 'pending')->count(),
        ];
        
        $recentWarehouses = Warehouse::orderBy('created_at', 'desc')->limit(5)->get();
        $recentDispatches = DispatchOrder::with('client')->orderBy('created_at', 'desc')->limit(5)->get();
        $recentRequests = WarehouseRequest::with('client')->orderBy('created_at', 'desc')->limit(5)->get();
        
        // Get notifications
        $notifications = Notification::where('user_id', $userData['user']->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'recentWarehouses' => $recentWarehouses,
            'recentDispatches' => $recentDispatches,
            'recentRequests' => $recentRequests,
            'notifications' => $notifications,
            'role' => 'admin',
        ]));
    }
    
    /**
     * Client Dashboard
     */
    private function clientDashboard($userData)
    {
        $user = $userData['user'];
        $userId = $user->id;
        
        // Get invoice IDs linked to this client via warehouse_requests
        $invoiceIds = DB::table('invoices')
            ->join('warehouse_requests', 'invoices.warehouse_request_id', '=', 'warehouse_requests.id')
            ->where('warehouse_requests.client_id', $userId)
            ->pluck('invoices.id');
        
        $stats = [
            'active_requests' => WarehouseRequest::where('client_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
            'dispatches' => DispatchOrder::where('client_id', $userId)->count(),
            'pending_dispatches' => DispatchOrder::where('client_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'completed_dispatches' => DispatchOrder::where('client_id', $userId)
                ->where('status', 'delivered')
                ->count(),
            'pickups' => PickupRequest::where('client_id', $userId)->count(),
            'stock_items' => Stock::where('user_id', $userId)->count(),
            'pending_invoices' => Invoice::whereIn('id', $invoiceIds)
                ->where('status', 'pending')
                ->count(),
            'total_spent' => DispatchOrder::where('client_id', $userId)
                ->where('status', 'delivered')
                ->sum('base_price') ?? 0,
        ];
        
        $recentRequests = WarehouseRequest::where('client_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentDispatches = DispatchOrder::where('client_id', $userId)
            ->with('driver')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentPickups = PickupRequest::where('client_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentStocks = Stock::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $recentInvoices = Invoice::whereIn('id', $invoiceIds)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $availableWarehouses = Warehouse::where('status', 'approved')
            ->limit(6)
            ->get();
        
        // Get notifications
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'recentDispatches' => $recentDispatches,
            'recentPickups' => $recentPickups,
            'recentStocks' => $recentStocks,
            'recentInvoices' => $recentInvoices,
            'availableWarehouses' => $availableWarehouses,
            'notifications' => $notifications,
            'role' => 'client',
        ]));
    }
    
    /**
     * Driver Dashboard
     */
    private function driverDashboard($userData)
    {
        $user = $userData['user'];
        $userId = $user->id;
        
        // Get vehicle count using driver_id
        $vehicleCount = Vehicle::where('driver_id', $userId)->count();
        
       $stats = [
    'available_jobs' => DispatchOrder::where('status', 'pending')->count(),
    'active_jobs' => DispatchOrder::where('driver_id', $userId)
        ->whereIn('status', ['assigned', 'picked_up', 'on_the_way'])
        ->count(),
    'completed_jobs' => DispatchOrder::where('driver_id', $userId)
        ->where('status', 'delivered')
        ->count(),
    'total_earnings' => DispatchOrder::where('driver_id', $userId)
        ->where('status', 'delivered')
        ->sum('driver_earning') ?? 0,
    'total_distance' => DispatchOrder::where('driver_id', $userId)
        ->where('status', 'delivered')
        ->sum('total_distance') ?? 0,
    'rating' => DispatchOrder::where('driver_id', $userId)
        ->where('client_rating', '>', 0)
        ->avg('client_rating') ?? 0,
    'vehicles' => $vehicleCount,
    'has_active_rate' => DB::table('driver_rates')
        ->where(function($query) use ($userId) {
            $query->where('driver_id', $userId)
                  ->orWhere('user_id', $userId);
        })
        ->where('is_active', true)
        ->exists(),
];

        
        // Recent data
        $activeJobs = DispatchOrder::where('driver_id', $userId)
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $completedJobs = DispatchOrder::where('driver_id', $userId)
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->limit(10)
            ->get();
            
        $availableJobs = DispatchOrder::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $recentEarnings = DispatchOrder::where('driver_id', $userId)
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->limit(10)
            ->get(['id', 'driver_earning as amount', 'delivered_at as created_at']);
            
        $weeklyEarnings = $this->getDriverWeeklyEarnings($userId);
        
        // Get notifications
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'activeJobs' => $activeJobs,
            'completedJobs' => $completedJobs,
            'availableJobs' => $availableJobs,
            'recentEarnings' => $recentEarnings,
            'weeklyEarnings' => $weeklyEarnings,
            'notifications' => $notifications,
            'role' => 'driver',
        ]));
    }
    
    /**
     * Property Owner Dashboard
     */
    private function propertyOwnerDashboard($userData)
    {
        $user = $userData['user'];
        $userId = $user->id;
        
        $stats = [
            'my_properties' => Warehouse::where('user_id', $userId)->count(),
            'approved_properties' => Warehouse::where('user_id', $userId)
                ->where('status', 'approved')
                ->count(),
            'pending_properties' => Warehouse::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'total_requests' => WarehouseRequest::whereHas('warehouse', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->count(),
            'pending_requests' => WarehouseRequest::whereHas('warehouse', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'pending')->count(),
            'approved_requests' => WarehouseRequest::whereHas('warehouse', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('status', 'approved')->count(),
            'total_revenue' => $this->getPropertyOwnerRevenue($userId),
        ];
        
        $myWarehouses = Warehouse::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $recentRequests = WarehouseRequest::whereHas('warehouse', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $recentDispatches = DispatchOrder::whereHas('warehouse', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get notifications
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'myWarehouses' => $myWarehouses,
            'recentRequests' => $recentRequests,
            'recentDispatches' => $recentDispatches,
            'notifications' => $notifications,
            'role' => 'property_owner',
        ]));
    }
    
    /**
     * Equipment Owner Dashboard
     */
    private function equipmentOwnerDashboard($userData)
    {
        $user = $userData['user'];
        $userId = $user->id;
        
        $stats = [
            'my_equipment' => Equipment::where('user_id', $userId)->count(),
            'available_equipment' => Equipment::where('user_id', $userId)
                ->where('status', 'available')
                ->count(),
            'job_requests' => EquipmentJob::where('owner_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'active_jobs' => EquipmentJob::where('owner_id', $userId)
                ->whereIn('status', ['accepted', 'in_progress'])
                ->count(),
            'completed_jobs' => EquipmentJob::where('owner_id', $userId)
                ->where('status', 'completed')
                ->count(),
            'total_earnings' => EquipmentJob::where('owner_id', $userId)
                ->where('status', 'completed')
                ->sum('price') ?? 0,
            'total_revenue' => EquipmentJob::where('owner_id', $userId)
                ->where('status', 'completed')
                ->sum('amount') ?? 0,
        ];
        
        $myEquipment = Equipment::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $jobRequests = EquipmentJob::where('owner_id', $userId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $activeJobs = EquipmentJob::where('owner_id', $userId)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $completedJobs = EquipmentJob::where('owner_id', $userId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();
            
        $recentEarnings = EquipmentJob::where('owner_id', $userId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get(['id', 'price as amount', 'completed_at as created_at']);
        
        // Get notifications
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'myEquipment' => $myEquipment,
            'jobRequests' => $jobRequests,
            'activeJobs' => $activeJobs,
            'completedJobs' => $completedJobs,
            'recentEarnings' => $recentEarnings,
            'notifications' => $notifications,
            'role' => 'equipment_owner',
        ]));
    }
    
    /**
     * Default Dashboard (fallback)
     */
    private function defaultDashboard($userData)
    {
        $stats = [
            'total_warehouses' => Warehouse::count(),
            'total_vehicles' => Vehicle::count(),
            'total_drivers' => User::where('role', 'driver')->count(),
        ];
        
        // Get notifications
        $notifications = Notification::where('user_id', $userData['user']->id)
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get();
        
        return view('dashboard.index', array_merge($userData, [
            'stats' => $stats,
            'notifications' => $notifications,
            'role' => $userData['role'] ?? 'default',
        ]));
    }
    
    /**
     * Get driver weekly earnings
     */
    private function getDriverWeeklyEarnings($driverId)
    {
        $earnings = [];
        
        // Get last 7 days of earnings
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayEarnings = DispatchOrder::where('driver_id', $driverId)
                ->whereDate('delivered_at', $date)
                ->where('status', 'delivered')
                ->sum('driver_earning') ?? 0;
            
            $earnings[] = [
                'day' => $date->format('D'),
                'date' => $date->format('Y-m-d'),
                'amount' => $dayEarnings,
                'formatted_amount' => number_format($dayEarnings, 2),
            ];
        }
        
        return $earnings;
    }
    
    /**
     * Get property owner revenue
     */
    private function getPropertyOwnerRevenue($ownerId)
    {
        return DispatchOrder::whereHas('warehouse', function($q) use ($ownerId) {
            $q->where('user_id', $ownerId);
        })->where('status', 'delivered')
          ->sum('base_price') ?? 0;
    }
}