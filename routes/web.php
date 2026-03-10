<?php

use App\Http\Controllers\Accounting\AccountingController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Procurement\ProcurementController;
use App\Http\Controllers\Production\ProductionController;
use App\Http\Controllers\System\AuditTrailController;
use App\Http\Controllers\System\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// Protected
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AccountingController::class, 'dashboard'])
        ->name('dashboard');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::post('/inventory', [InventoryController::class, 'store'])
        ->name('inventory.store')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::get('/inventory/{id}/details', [InventoryController::class, 'getDetails'])
        ->name('inventory.details')
        ->middleware('role:admin,inventory_clerk,procurement_officer,sales_clerk');
    Route::get('/inventory/{id}/edit-product', action: [InventoryController::class, 'editProduct'])
        ->name('inventory.edit-product')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])
        ->name('inventory.update')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::delete('/inventory/{id}/{type}', [InventoryController::class, 'destroy'])
        ->name('inventory.destroy')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::post('/inventory/{id}/adjust-stock', [InventoryController::class, 'adjustStock'])
        ->name('inventory.adjust-stock')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::get('/inventory/stock-movements/report', [InventoryController::class, 'stockMovementsReport'])
        ->name('inventory.stock-movements-report')
        ->middleware('role:admin,inventory_clerk,procurement_officer');
    Route::get('/inventory/metrics', [InventoryController::class, 'metrics'])
        ->name('inventory.metrics')
        ->middleware('role:admin,inventory_clerk,procurement_officer,sales_clerk');

    // Procurement
    Route::get('/procurement', [ProcurementController::class, 'index'])
        ->name('procurement')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::post('/procurement/suppliers', [ProcurementController::class, 'storeSupplier'])
        ->name('procurement.supplier.store')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::post('/procurement/purchase-orders', [ProcurementController::class, 'storePurchaseOrder'])
        ->name('procurement.purchase-order.store')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::get('/procurement/purchase-orders/{purchaseOrder}/items', [ProcurementController::class, 'getPurchaseOrderItems'])
        ->name('procurement.purchase-order.items')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::post('/procurement/item/{item}/cancel', [App\Http\Controllers\Sales\SalesOrderController::class, 'cancelPurchaseItem'])
        ->name('procurement.item.cancel')
        ->middleware('role:admin,procurement_officer');

    Route::post('/procurement/purchase-orders/{purchaseOrder}/receive-stock', [ProcurementController::class, 'receiveStock'])
        ->name('procurement.purchase-order.receive-stock')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::post('/procurement/purchase-orders/{id}/receive', [ProcurementController::class, 'markReceived'])
        ->name('procurement.purchase-order.receive')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::get('/procurement/received-stock-reports', [ProcurementController::class, 'receivedStockReports'])
        ->name('procurement.received-stock-reports')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::put('/procurement/suppliers/{id}', [ProcurementController::class, 'updateSupplier'])
        ->name('procurement.supplier.update')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::put('/procurement/purchase-orders/{id}', [ProcurementController::class, 'updatePurchaseOrder'])
        ->name('procurement.purchase-order.update')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::delete('/procurement/suppliers/{id}', [ProcurementController::class, 'removeSupplier'])
        ->name('procurement.supplier.destroy')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::delete('/procurement/purchase-orders/{id}', [ProcurementController::class, 'destroyPurchaseOrder'])
        ->name('procurement.purchase-order.destroy')
        ->middleware('role:admin,procurement_officer,inventory_clerk');
    Route::get('/procurement/receipt/{orderNumber}', [ProcurementController::class, 'exportReceipt'])
        ->name('procurement.receipt')
        ->middleware('role:admin,procurement_officer,inventory_clerk');

    // Production & Work Orders
    Route::get('/production', [ProductionController::class, 'index'])
        ->name('production')
        ->middleware('role:admin,workshop_staff');
    Route::post('/production', [ProductionController::class, 'store'])
        ->name('production.store')
        ->middleware('role:admin,workshop_staff');
    Route::get('/production/{workOrder}', [ProductionController::class, 'show'])
        ->name('production.show')
        ->middleware('role:admin,workshop_staff');
    Route::put('/production/{workOrder}', [ProductionController::class, 'update'])
        ->name('production.update')
        ->middleware('role:admin,workshop_staff');
    Route::post('/production/{workOrder}/start', [ProductionController::class, 'start'])
        ->name('production.start')
        ->middleware('role:admin,workshop_staff');
    Route::post('/production/{workOrder}/complete', [ProductionController::class, 'complete'])
        ->name('production.complete')
        ->middleware('role:admin,workshop_staff');
    Route::post('/production/{workOrder}/cancel', [ProductionController::class, 'cancel'])
        ->name('production.cancel')
        ->middleware('role:admin,workshop_staff');

    // Sales
    Route::resource('sales-orders', SalesOrderController::class)
        ->middleware('role:admin,sales_clerk');
    Route::post('/sales-orders/item/{item}/cancel', [SalesOrderController::class, 'cancelItem'])
        ->name('sales-orders.item.cancel')
        ->middleware('role:admin,sales_clerk');

    Route::post('/sales-orders/{sales_order}/deliver', [SalesOrderController::class, 'deliver'])
        ->name('sales-orders.deliver')
        ->middleware('role:admin,sales_clerk');

    Route::get('/sales', [SalesOrderController::class, 'index'])
        ->name('sales')
        ->middleware('role:admin,sales_clerk');
    Route::delete('/sales/customers/{id}', [SalesOrderController::class, 'RemoveCustomer'])
        ->name('customers.delete')
        ->middleware('role:admin,sales_clerk');
    Route::get('/sales/receipt/{orderNumber}', [SalesOrderController::class, 'exportReceipt'])
        ->name('sales.receipt')
        ->middleware('role:admin,sales_clerk');
    Route::get('/sales/export/report', [SalesOrderController::class, 'exportSalesReport'])
        ->name('sales.export.report')
        ->middleware('role:admin,sales_clerk');
    Route::get('/customers/export', [CustomerController::class, 'exportCustomers'])
        ->name('customers.export')
        ->middleware('role:admin,sales_clerk');

    // Accounting
    Route::get('/accounting', [AccountingController::class, 'index'])
        ->name('accounting')
        ->middleware('role:admin,accounting_staff');
    Route::post('/accounting/transaction', [AccountingController::class, 'salesTransaction'])
        ->name('accounting.transaction.store')
        ->middleware('role:admin,accounting_staff');
    Route::get('/accounting/receipt/{transactionId}', [AccountingController::class, 'exportTransactionReceipt'])
        ->name('accounting.receipt')
        ->middleware('role:admin,accounting_staff');
    Route::get('/accounting/export/financial-report', [AccountingController::class, 'exportFinancialReport'])
        ->name('accounting.export.financial')
        ->middleware('role:admin,accounting_staff');
    Route::get('/accounting/export/transactions', [AccountingController::class, 'exportTransactionHistory'])
        ->name('accounting.export.transactions')
        ->middleware('role:admin,accounting_staff');

    // System Audit Trails
    Route::get('/audit-trails', [AuditTrailController::class, 'index'])
        ->name('audit-trails')
        ->middleware('role:admin');

    // Staff Management 
    Route::get('/staff-management', [StaffController::class, 'index'])
        ->name('staff.index')
        ->middleware('role:admin');
    Route::post('/staff-management', [StaffController::class, 'store'])
        ->name('staff.store')
        ->middleware('role:admin');
    Route::put('/staff-management/{id}', [StaffController::class, 'update'])
        ->name('staff.update')
        ->middleware('role:admin');
    Route::patch('/staff-management/{id}/reset-password', [StaffController::class, 'resetPassword'])
        ->name('staff.reset-password')
        ->middleware('role:admin');
    Route::delete('/staff-management/{id}', [StaffController::class, 'destroy'])
        ->name('staff.destroy')
        ->middleware('role:admin');
    Route::patch('/staff-management/admin/change-password', [StaffController::class, 'changeAdminPassword'])
        ->name('staff.admin.change-password')
        ->middleware('role:admin');

    // Profile
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    // Customers CRUD
    Route::resource('customers', CustomerController::class)
        ->middleware('role:admin,sales_clerk');
});
