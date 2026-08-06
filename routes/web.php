<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\PickupRequestController;
use App\Http\Controllers\ClientRequestHandler;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EquipmentJobController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\StockController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientEquipmentController;
use App\Http\Controllers\ClientProposalController;
use App\Http\Controllers\DriverRateController;
use App\Http\Controllers\DriverVehicleController;
use App\Http\Controllers\EquipmentRequestController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PropertyOwnerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\PdfController;

// ================================================================
// 1. TEST & DEBUG ROUTES (Remove in production)
// ================================================================
Route::get('/simple-login', fn() => view('auth.simple-login'));
Route::get('/test-login', function () {
    $user = \App\Models\User::where('email', 'admin@ktmwdc.com')->first();
    if (!$user) return "User not found! Please run the seeder.";
    Auth::login($user);
    return "Logged in as: " . Auth::user()->name . " (ID: " . Auth::id() . ")";
});
Route::get('/check-login-url', fn() => response()->json([
    'route_exists' => Route::has('login'),
    'route_url' => route('login')
]));
Route::get('/session-test', fn() => response()->json([
    'session_id' => session()->getId(),
    'test_value' => session('test'),
    'csrf_token' => csrf_token(),
    'session_driver' => config('session.driver')
]));
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    return 'All caches cleared!';
});

// ================================================================
// 2. PUBLIC ROUTES
// ================================================================
Route::get('/', fn() => redirect()->route('dashboard'))->name('home');
Route::get('/ping', fn() => 'pong');
Route::get('/simple-create', fn() => view('warehouses.simple-create'))->name('simple.create');
Route::get('/test-pickup', fn() => 'Pickup test is working!')->name('test.pickup');
Route::get('/test-mail', function () {
    Mail::send([], [], function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email from KTM-WDC')
                ->html('<h1>🙏 Namaste!</h1><p>This is a test email from your KTM-WDC system.</p><p>If you see this, your email configuration is working!</p>');
    });
    return 'Test email sent! Check your Mailtrap inbox.';
})->name('test.mail');
Route::get('/test-invoice', function () {
    $dispatch = \App\Models\DispatchOrder::first();
    if (!$dispatch) return "No dispatch orders found. Please create a dispatch first.";
    $service = new \App\Services\InvoiceService();
    $invoice = $service->generateDispatchInvoice($dispatch);
    if ($invoice) return redirect()->route('invoices.show', $invoice);
    return "Failed to generate invoice";
})->middleware('auth')->name('test.invoice');
Route::get('/invoice/verify/{invoiceNumber}', [InvoiceController::class, 'verify'])->name('invoice.verify');

// ================================================================
// 3. AUTHENTICATION ROUTES
// ================================================================
Auth::routes();
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ================================================================
// 4. PROTECTED ROUTES (Authentication required)
// ================================================================
Route::middleware(['auth'])->group(function () {

    // -------------------- DASHBOARD --------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // -------------------- PROFILE --------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/contacts', [ProfileController::class, 'contacts'])->name('contacts');
        Route::post('/contacts', [ProfileController::class, 'addContact'])->name('contacts.store');
        Route::delete('/contacts/{id}', [ProfileController::class, 'destroyContact'])->name('contacts.destroy');
    });

    // -------------------- NOTIFICATIONS --------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // -------------------- TRACKING --------------------
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/', [TrackingController::class, 'index'])->name('index');          // Unified tracking dashboard
        Route::get('/incoming', [TrackingController::class, 'incoming'])->name('incoming');
        Route::get('/outgoing', [TrackingController::class, 'outgoing'])->name('outgoing');
        Route::get('/pickups', [TrackingController::class, 'pickups'])->name('pickups');
        Route::get('/shipment/{id}', [TrackingController::class, 'trackShipment'])->name('shipment');
        Route::post('/location/{id}', [TrackingController::class, 'updateLocation'])->name('location');
        Route::post('/status/{id}', [TrackingController::class, 'updateStatus'])->name('status');
    });

    // -------------------- WAREHOUSE (Owner / Client) --------------------
    Route::resource('warehouses', WarehouseController::class);
    Route::post('/save-preferred-location', [ClientRequestHandler::class, 'savePreferredLocation'])->name('save-preferred-location');
    Route::post('/warehouses/verify-kataho', [WarehouseController::class, 'verifyKataho'])->name('warehouses.verify-kataho');
    Route::post('/warehouses/get-kataho-from-coords', [WarehouseController::class, 'getKatahoFromCoords'])->name('warehouses.get-kataho-from-coords');

    // -------------------- CLIENT WAREHOUSE REQUESTS --------------------
    Route::prefix('my-requests')->name('my-requests.')->group(function () {
        Route::get('/', [ClientRequestHandler::class, 'index'])->name('index');
        Route::get('/create', [ClientRequestHandler::class, 'create'])->name('create');
        Route::post('/', [ClientRequestHandler::class, 'store'])->name('store');
        Route::get('/{id}', [ClientRequestHandler::class, 'show'])->name('show');
    });

    // -------------------- STOCK --------------------
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/store', [StockController::class, 'store'])->name('store');
        Route::get('/{id}', [StockController::class, 'show'])->name('show');
        Route::get('/{id}/qr-code', [StockController::class, 'viewQrCode'])->name('qr-code');
        Route::get('/{id}/download-qr', [StockController::class, 'downloadQrCode'])->name('download-qr');
        Route::get('/{id}/document/{type}', [StockController::class, 'downloadDocument'])->name('download-document');
    });

    // -------------------- BOXES (QR) --------------------
    Route::prefix('boxes')->name('boxes.')->group(function () {
        Route::get('/', [BoxController::class, 'index'])->name('index');
        Route::get('/create', [BoxController::class, 'create'])->name('create');
        Route::post('/', [BoxController::class, 'store'])->name('store');
        Route::get('/{id}/qr', [BoxController::class, 'getQR'])->name('qr');
        Route::get('/{id}/track', [BoxController::class, 'track'])->name('track');
        Route::get('/{id}/print', [BoxController::class, 'printLabel'])->name('print');
        Route::get('/{id}/documents', [BoxController::class, 'getDocuments'])->name('documents');
    });

    // -------------------- DISPATCH --------------------
    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('/', [DispatchController::class, 'index'])->name('index');
        Route::get('/direct-create', [DispatchController::class, 'directCreate'])->name('direct-create');
        Route::get('/create/{requestId}', [DispatchController::class, 'create'])->name('create');
        Route::post('/store', [DispatchController::class, 'store'])->name('store');
        Route::get('/{id}', [DispatchController::class, 'show'])->name('show');
        Route::post('/calculate-price', [DispatchController::class, 'calculatePriceAjax'])->name('calculate-price');
        Route::get('/{id}/track', [DispatchController::class, 'track'])->name('track');
        Route::post('/{id}/status', [DispatchController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/rate', [DispatchController::class, 'rate'])->name('rate');
        Route::post('/{id}/enable-tracking', [DispatchController::class, 'enableTracking'])->name('enable-tracking');
        Route::post('/{id}/update-location', [DispatchController::class, 'updateLocation'])->name('update-location');
        Route::post('/stop/{id}/status', [DispatchController::class, 'updateStopStatus'])->name('stop-status');
    });

    // -------------------- PICKUP --------------------
    Route::prefix('pickup')->name('pickup.')->group(function () {
        Route::get('/', [PickupRequestController::class, 'index'])->name('index');
        Route::get('/create', [PickupRequestController::class, 'create'])->name('create');
        Route::get('/direct-create', [PickupRequestController::class, 'directCreate'])->name('direct-create');
        Route::post('/store', [PickupRequestController::class, 'store'])->name('store');
        Route::post('/calculate-price', [PickupRequestController::class, 'calculatePriceAjax'])->name('calculate-price');
        Route::get('/{id}', [PickupRequestController::class, 'show'])->name('show');
    });

    // -------------------- INVOICES --------------------
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
    });
    Route::get('/client/invoices', [InvoiceController::class, 'clientIndex'])->name('invoices.client-index');

    // -------------------- CLIENT ZONE --------------------
    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/driver-rates', [ClientController::class, 'driverRates'])->name('driver.rates');
        Route::get('/reports', [ClientController::class, 'reports'])->name('reports');
        Route::get('/my-stock', [ClientRequestHandler::class, 'myStock'])->name('my-stock');
        Route::get('/my-insurance', [ClientRequestHandler::class, 'myInsurance'])->name('my-insurance');
        Route::get('/pickups', [PickupController::class, 'index'])->name('pickups.index');
        Route::get('/proposals', [ClientProposalController::class, 'index'])->name('proposals');

        Route::prefix('proposals')->name('proposals.')->group(function () {
            Route::get('/', [ClientProposalController::class, 'index'])->name('index');
            Route::get('/create/{requestId}', [ClientProposalController::class, 'create'])->name('create');
            Route::post('/', [ClientProposalController::class, 'store'])->name('store');
            Route::get('/{id}', [ClientProposalController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ClientProposalController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ClientProposalController::class, 'update'])->name('update');
            Route::delete('/{id}', [ClientProposalController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/accept', [ClientProposalController::class, 'accept'])->name('accept');
            Route::post('/{id}/reject', [ClientProposalController::class, 'reject'])->name('reject');
            Route::post('/{id}/negotiate', [ClientProposalController::class, 'negotiate'])->name('negotiate');
        });

        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/requests', [ClientEquipmentController::class, 'index'])->name('requests');
            Route::get('/request', [ClientEquipmentController::class, 'create'])->name('request');
            Route::post('/request', [ClientEquipmentController::class, 'store'])->name('request.store');
            Route::post('/request/{id}/accept', [ClientEquipmentController::class, 'acceptQuote'])->name('request.accept');
            Route::post('/request/{id}/reject', [ClientEquipmentController::class, 'rejectQuote'])->name('request.reject');
            Route::post('/request/{id}/negotiate', [ClientEquipmentController::class, 'negotiatePrice'])->name('request.negotiate');
        });
    });

    // -------------------- DRIVER ZONE --------------------
    Route::prefix('driver')->name('driver.')->group(function () {
        Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('dashboard');
        Route::get('/jobs', [DriverController::class, 'jobs'])->name('jobs');
        Route::get('/available-jobs', [DriverController::class, 'availableJobs'])->name('available-jobs');
        Route::post('/jobs/{id}/accept', [DriverController::class, 'accept'])->name('jobs.accept');
        Route::post('/jobs/{id}/start', [DriverController::class, 'start'])->name('jobs.start');
        Route::post('/jobs/{id}/deliver', [DriverController::class, 'deliver'])->name('jobs.deliver');
        Route::post('/jobs/{id}/cancel', [DriverController::class, 'cancel'])->name('jobs.cancel');
        Route::post('/jobs/{order}/proof', [DriverController::class, 'uploadProof'])->name('jobs.proof');
        Route::get('/pickups', [DriverController::class, 'pickupJobs'])->name('pickups');
        Route::post('/pickups/{id}/accept', [DriverController::class, 'acceptPickup'])->name('accept-pickup');
        Route::post('/pickups/{id}/start', [DriverController::class, 'startPickup'])->name('start-pickup');
        Route::post('/pickups/{id}/complete', [DriverController::class, 'completePickup'])->name('complete-pickup');
        Route::post('/pickups/{id}/cancel', [DriverController::class, 'cancelPickup'])->name('cancel-pickup');
        Route::post('/propose-price/{id}', [DriverController::class, 'proposePrice'])->name('propose-price');
        Route::post('/accept-counter/{proposalId}', [DriverController::class, 'acceptCounter'])->name('accept-counter');
        Route::post('/repropose/{proposalId}', [DriverController::class, 'repropose'])->name('repropose');
        Route::get('/earnings', [DriverController::class, 'earnings'])->name('earnings');
        Route::get('/rates', [DriverRateController::class, 'index'])->name('rates');
        Route::post('/rates', [DriverRateController::class, 'store'])->name('rates.store');
        Route::post('/rates/extend', [DriverRateController::class, 'extend'])->name('rates.extend');
        Route::get('/rates/history', [DriverRateController::class, 'history'])->name('rates.history');

        Route::prefix('vehicles')->name('vehicles.')->group(function () {
            Route::get('/', [DriverVehicleController::class, 'index'])->name('index');
            Route::get('/register', [DriverVehicleController::class, 'create'])->name('create');
            Route::post('/', [DriverVehicleController::class, 'store'])->name('store');
            Route::get('/{id}', [DriverVehicleController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [DriverVehicleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DriverVehicleController::class, 'update'])->name('update');
            Route::delete('/{id}', [DriverVehicleController::class, 'destroy'])->name('destroy');
        });
    });

    // -------------------- EQUIPMENT OWNER ZONE --------------------
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/dashboard', [EquipmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/register', [EquipmentController::class, 'create'])->name('register');
        Route::post('/', [EquipmentController::class, 'store'])->name('store');
        Route::get('/list', [EquipmentController::class, 'list'])->name('list');
        Route::get('/{id}/edit', [EquipmentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EquipmentController::class, 'update'])->name('update');
        Route::delete('/{id}', [EquipmentController::class, 'destroy'])->name('destroy');

        // Equipment Jobs
        Route::get('/jobs', [EquipmentJobController::class, 'index'])->name('jobs');
        Route::prefix('jobs')->name('jobs.')->group(function () {
            Route::get('/', [EquipmentJobController::class, 'index'])->name('index');
            Route::get('/requests', [EquipmentJobController::class, 'requests'])->name('requests');
            Route::get('/active', [EquipmentJobController::class, 'active'])->name('active');
            Route::get('/history', [EquipmentJobController::class, 'history'])->name('history');
            Route::get('/earnings', [EquipmentJobController::class, 'earnings'])->name('earnings');
            Route::get('/{id}', [EquipmentJobController::class, 'show'])->name('show');
            Route::post('/{id}/accept', [EquipmentJobController::class, 'accept'])->name('accept');
            Route::post('/{id}/reject', [EquipmentJobController::class, 'reject'])->name('reject');
            Route::post('/{id}/propose-price', [EquipmentJobController::class, 'proposePrice'])->name('propose-price');
            Route::post('/{id}/start', [EquipmentJobController::class, 'start'])->name('start');
            Route::post('/{id}/complete', [EquipmentJobController::class, 'complete'])->name('complete');
            Route::post('/{id}/cancel', [EquipmentJobController::class, 'cancel'])->name('cancel');
        });
    });

    // -------------------- EQUIPMENT REQUESTS (Client side) --------------------
    Route::prefix('equipment-requests')->name('equipment-requests.')->group(function () {
        Route::get('/', [EquipmentRequestController::class, 'index'])->name('index');
        Route::get('/create', [EquipmentRequestController::class, 'create'])->name('create');
        Route::post('/', [EquipmentRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [EquipmentRequestController::class, 'show'])->name('show');
        Route::put('/{id}', [EquipmentRequestController::class, 'update'])->name('update');
        Route::delete('/{id}', [EquipmentRequestController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [EquipmentRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [EquipmentRequestController::class, 'reject'])->name('reject');
        Route::post('/{id}/fulfill', [EquipmentRequestController::class, 'fulfill'])->name('fulfill');
        Route::post('/{id}/return', [EquipmentRequestController::class, 'return'])->name('return');
    });

    // -------------------- PROPERTY OWNER ZONE --------------------
    Route::prefix('property')->name('property.')->group(function () {
        Route::get('/pending', [PropertyOwnerController::class, 'pending'])->name('pending');
        Route::get('/approved', [PropertyOwnerController::class, 'approved'])->name('approved');
        Route::get('/rejected', [PropertyOwnerController::class, 'rejected'])->name('rejected');
        Route::get('/analytics', [PropertyOwnerController::class, 'analytics'])->name('analytics');
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [PropertyOwnerController::class, 'requests'])->name('index');
            Route::post('/{id}/approve', [PropertyOwnerController::class, 'approveRequest'])->name('approve');
            Route::post('/{id}/reject', [PropertyOwnerController::class, 'rejectRequest'])->name('reject');
        });
        Route::prefix('warehouses')->name('warehouses.')->group(function () {
            Route::get('/{id}', [PropertyOwnerController::class, 'showWarehouse'])->name('show');
            Route::get('/{id}/edit', [PropertyOwnerController::class, 'editWarehouse'])->name('edit');
            Route::put('/{id}', [PropertyOwnerController::class, 'updateWarehouse'])->name('update');
            Route::delete('/{id}', [PropertyOwnerController::class, 'destroyWarehouse'])->name('destroy');
        });
    });

    // -------------------- PAYMENT --------------------
    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/khalti/init', [PaymentController::class, 'khaltiInit'])->name('khalti.init');
        Route::get('/khalti/verify', [PaymentController::class, 'khaltiVerify'])->name('khalti.verify');
        Route::post('/esewa/init', [PaymentController::class, 'esewaInit'])->name('esewa.init');
        Route::get('/esewa/success', [PaymentController::class, 'esewaSuccess'])->name('esewa.success');
        Route::get('/esewa/failure', [PaymentController::class, 'esewaFailure'])->name('esewa.failure');
        Route::get('/success', [PaymentController::class, 'success'])->name('success');
        Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');
        Route::get('/history', [PaymentController::class, 'history'])->name('history');
    });

    // -------------------- AI ROUTES --------------------
    Route::post('/ai/voice-command', [AIController::class, 'handleVoice'])->name('ai.voice.command');
    Route::post('/ai/chat-support', [AIController::class, 'chatSupport'])->name('ai.chat');
    Route::post('/dispatch/calculate-price', [DispatchController::class, 'calculatePriceAjax'])->name('dispatch.calculate-price');
    Route::post('/dispatch/recommend-drivers', [DispatchController::class, 'getDriverRecommendations'])->name('dispatch.recommend-drivers');
    Route::post('/pickup/recommend-drivers', [PickupRequestController::class, 'getDriverRecommendations'])->name('pickup.recommend-drivers');
    Route::post('/equipment-requests/recommend', [EquipmentRequestController::class, 'recommendEquipment'])->name('equipment-requests.recommend');

    // -------------------- WAREHOUSE SEARCH --------------------
    Route::get('/warehouses/search', [WarehouseController::class, 'search'])->name('warehouses.search');

    // -------------------- PDF GENERATION --------------------
    Route::prefix('pdf')->name('pdf.')->group(function () {
        Route::get('/warehouse/{id}', [PdfController::class, 'downloadWarehouse'])->name('warehouse');
        Route::get('/dispatch/{id}', [PdfController::class, 'downloadDispatch'])->name('dispatch');
        Route::get('/invoice/{id}', [PdfController::class, 'downloadInvoice'])->name('invoice');
    });

    // -------------------- ADMIN ROUTES (Admin middleware) --------------------
    Route::prefix('admin')->middleware(['admin'])->name('admin.')->group(function () {
        // Warehouse Management
        Route::get('/pending', [AdminController::class, 'pending'])->name('pending');
        Route::post('/approve/{warehouse}', [AdminController::class, 'approve'])->name('approve');
        Route::post('/reject/{warehouse}', [AdminController::class, 'reject'])->name('reject');
        Route::get('/all-warehouses', [AdminController::class, 'allWarehouses'])->name('all-warehouses');
        Route::get('/warehouse-tenants', [AdminController::class, 'warehouseTenants'])->name('warehouse-tenants');
        Route::get('/warehouse-documents', [AdminController::class, 'warehouseDocuments'])->name('warehouse-documents');
        Route::get('/warehouse/{id}/details', [AdminController::class, 'getWarehouseDetails'])->name('warehouse.details');
        Route::get('/warehouse/{id}/show', [AdminController::class, 'showWarehouse'])->name('warehouse.show');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');

        // Warehouse CRUD
        Route::get('/warehouses/create', [AdminController::class, 'createWarehouse'])->name('warehouses.create');
        Route::post('/warehouses', [AdminController::class, 'storeWarehouse'])->name('warehouses.store');
        Route::get('/warehouses/{id}/show', [AdminController::class, 'showWarehouse'])->name('warehouses.show');
        Route::get('/warehouses/{id}/edit', [AdminController::class, 'editWarehouse'])->name('warehouses.edit');
        Route::put('/warehouses/{id}', [AdminController::class, 'updateWarehouse'])->name('warehouses.update');
        Route::delete('/warehouses/{id}', [AdminController::class, 'destroyWarehouse'])->name('warehouses.destroy');

        // Sidebar aliases
        Route::get('/warehouses/pending', [AdminController::class, 'pending'])->name('warehouses.pending');
        Route::get('/warehouses/all', [AdminController::class, 'allWarehouses'])->name('warehouses.all');
        Route::get('/warehouses', [AdminController::class, 'allWarehouses'])->name('warehouses.index');

        // Request Management
        Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
        Route::post('/requests/{id}/assign', [AdminController::class, 'assign'])->name('requests.assign');

        // Stock
        Route::get('/stocks', [AdminController::class, 'manageStock'])->name('stocks');
        Route::get('/pending-stocks', [AdminController::class, 'pendingStocks'])->name('pending-stocks');
        Route::post('/stocks/{stockId}/verify', [AdminController::class, 'verifyStock'])->name('verify.stock');

        // Vehicles
        Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('vehicles');
        Route::post('/vehicles', [AdminController::class, 'storeVehicle'])->name('vehicles.store');
        Route::delete('/vehicles/{id}', [AdminController::class, 'deleteVehicle'])->name('vehicles.delete');
        Route::get('/vehicles/list', [AdminController::class, 'vehicles'])->name('vehicles.list');

        // Dispatch & Orders
        Route::get('/dispatch', [AdminController::class, 'dispatchOrders'])->name('dispatch');
        Route::post('/dispatch/{orderId}/assign', [AdminController::class, 'assignVehicle'])->name('dispatch.assign');
        Route::get('/pickup-requests', [AdminController::class, 'pickupRequests'])->name('pickup');
        Route::get('/dispatches', [AdminController::class, 'dispatchOrders'])->name('dispatches.index');

        // Equipment (Admin manages)
        Route::get('/equipment', [AdminController::class, 'equipment'])->name('equipment.index');
        Route::get('/equipment-jobs', [AdminController::class, 'equipmentJobs'])->name('equipment-jobs');
        Route::get('/equipment-list', [AdminController::class, 'equipmentList'])->name('equipment-list');
        Route::get('/equipment/{id}/edit', [AdminController::class, 'editEquipment'])->name('equipment.edit');
        Route::put('/equipment/{id}', [AdminController::class, 'updateEquipment'])->name('equipment.update');
        Route::delete('/equipment/{id}', [AdminController::class, 'destroyEquipment'])->name('equipment.destroy');
        Route::get('/equipments', [AdminController::class, 'equipmentList'])->name('equipments.index');
        Route::get('/equipment/{id}/jobs', [AdminController::class, 'showEquipmentJobs'])->name('equipment.jobs');

        // Users
        Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
        Route::get('/drivers', [AdminController::class, 'drivers'])->name('drivers');
        Route::get('/drivers/{id}', [AdminController::class, 'showDriver'])->name('drivers.show');
        Route::get('/drivers/{id}/edit', [AdminController::class, 'editDriver'])->name('drivers.edit');
        Route::put('/drivers/{id}', [AdminController::class, 'updateDriver'])->name('drivers.update');
        Route::delete('/drivers/{id}', [AdminController::class, 'destroyDriver'])->name('drivers.destroy');
        Route::get('/equipment-owners', [AdminController::class, 'equipmentOwners'])->name('equipment-owners');
        Route::get('/property-owners', [AdminController::class, 'propertyOwners'])->name('property-owners');
        Route::get('/clients/list', [AdminController::class, 'clients'])->name('clients.list');
        Route::get('/clients/{id}', [AdminController::class, 'showClient'])->name('clients.show');
        Route::get('/drivers/list', [AdminController::class, 'drivers'])->name('drivers.list');

        // Financial
        Route::get('/invoices', [AdminController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/list', [AdminController::class, 'invoices'])->name('invoices.list');
        Route::get('/invoices/admin', [InvoiceController::class, 'adminIndex'])->name('invoices.admin');
        Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::post('/invoices/{invoice}/resend-email', [InvoiceController::class, 'resendEmail'])->name('invoices.resend-email');

        // Margins
        Route::get('/margin-tiers', [AdminController::class, 'marginTiers'])->name('margin-tiers');
        Route::post('/margin-tiers', [AdminController::class, 'storeMarginTier'])->name('margin-tiers.store');
        Route::put('/margin-tiers/{id}', [AdminController::class, 'updateMarginTier'])->name('margin-tiers.update');
        Route::delete('/margin-tiers/{id}', [AdminController::class, 'destroyMarginTier'])->name('margin-tiers.destroy');
        Route::get('/margins', [AdminController::class, 'marginTiers'])->name('margins.index');
        Route::get('/margins/tiers', [AdminController::class, 'marginTiers'])->name('margins.tiers');

        // Reports & Insurance
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/generate', [AdminController::class, 'reports'])->name('reports.generate');
        Route::get('/partner-earnings', [AdminController::class, 'partnerEarnings'])->name('partner-earnings');
        Route::get('/insurance-list', [AdminController::class, 'insuranceList'])->name('insurance.list');

        // Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

        // Predictive Analytics
        Route::get('/analytics/predictive', [AdminController::class, 'predictiveAnalytics'])->name('analytics.predictive');
    });
});

// ================================================================
// 5. REPORTS (Outside auth group, but with auth+admin middleware)
// ================================================================
Route::prefix('reports')->middleware(['auth', 'admin'])->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/export', [ReportController::class, 'export'])->name('export');
});

// ================================================================
// 6. TEST EMAIL & FALLBACK
// ================================================================
Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email from KTM-WDC system.', function($message) {
            $message->to('kiran.kwdc@gmail.com')
                    ->subject('[TEST] KTM-WDC Email Test');
        });
        return "✅ Test email sent successfully to kiran.kwdc@gmail.com!";
    } catch (\Exception $e) {
        return "❌ Email failed: " . $e->getMessage();
    }
});


Route::get('/test-maps', function () {
    $service = new \App\Services\GoogleMapsService();
    $result = $service->getTrafficDuration(27.7172, 85.3240, 27.7000, 85.3200);
    dd($result);
});


Route::fallback(function () {
    return view('errors.404');
});