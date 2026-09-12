<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecurityAgencyController;
use App\Http\Controllers\SecurityPersonnelController;
use App\Http\Controllers\SecurityGoodController;
use App\Http\Controllers\SecurityIncidentController;
use App\Http\Controllers\ReportController;

// ==================== ADMIN ROUTES (Admin middleware) ====================
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

    // Sidebar aliases (avoid duplication with resource route)
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

    // ==================== SECURITY AGENCIES (Admin management) ====================
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/agencies', [SecurityAgencyController::class, 'index'])->name('agencies');
        Route::get('/agencies/{agency}', [SecurityAgencyController::class, 'show'])->name('agency.show');
        Route::post('/agencies/{agency}/approve', [SecurityAgencyController::class, 'approve'])->name('agency.approve');
        Route::post('/agencies/{agency}/reject', [SecurityAgencyController::class, 'reject'])->name('agency.reject');
        Route::get('/agencies/{agency}/export', [SecurityAgencyController::class, 'export'])->name('agency.export');

        // Admin can also view personnel/goods/incidents of any agency (optional)
        Route::get('/agencies/{agency}/personnel', [SecurityPersonnelController::class, 'index'])->name('agency.personnel');
        Route::get('/agencies/{agency}/goods', [SecurityGoodController::class, 'index'])->name('agency.goods');
        Route::get('/agencies/{agency}/incidents', [SecurityIncidentController::class, 'index'])->name('agency.incidents');
    });
});

// ================================================================
// REPORTS
// ================================================================
Route::prefix('reports')->middleware(['admin'])->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/export', [ReportController::class, 'export'])->name('export');
});
