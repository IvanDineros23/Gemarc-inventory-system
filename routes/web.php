<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return view('welcome');
})->name('welcome.page');

/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // =============================================
    // Dashboard Routes
    // =============================================
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API endpoints for charts and statistics
    Route::get('/api/dashboard/summary', [\App\Http\Controllers\DashboardController::class, 'summary'])->name('api.dashboard.summary');
    Route::get('/api/dashboard/low-stock', [\App\Http\Controllers\DashboardController::class, 'lowStock'])->name('api.dashboard.lowstock');
    Route::get('/api/dashboard/receivings', [\App\Http\Controllers\DashboardController::class, 'receivingsSeries'])->name('api.dashboard.receivings');
    Route::get('/api/dashboard/top-suppliers', [\App\Http\Controllers\DashboardController::class, 'topSuppliers'])->name('api.dashboard.topsuppliers');
    Route::get('/api/dashboard/stock-value', [\App\Http\Controllers\DashboardController::class, 'stockValueTrend'])->name('api.dashboard.stockvalue');
    
    // Dashboard export low stock items (CSV/PDF)
    Route::get('/dashboard/export-low-stock', [\App\Http\Controllers\InventoryExportController::class, 'exportLowStock'])->name('dashboard.export.lowstock');

    // =============================================
    // Product Management Routes
    // =============================================
    Route::get('/product-management', [\App\Http\Controllers\ProductManagementController::class, 'index'])->name('product.management');
    Route::post('/product-management', [\App\Http\Controllers\ProductManagementController::class, 'store'])->name('product.management.store');
    Route::get('/product-management/{product}/edit', [\App\Http\Controllers\ProductManagementController::class, 'edit'])->name('product.management.edit');
    Route::put('/product-management/{product}', [\App\Http\Controllers\ProductManagementController::class, 'update'])->name('product.management.update');
    Route::delete('/product-management/{product}', [\App\Http\Controllers\ProductManagementController::class, 'destroy'])->name('product.management.destroy');
    
    // AJAX product search for live search functionality
    Route::get('/product-search', [\App\Http\Controllers\ProductManagementController::class, 'search'])->name('product.search');

    // =============================================
    // Receiving Entry Routes
    // =============================================
    Route::get('/receiving-entry', [\App\Http\Controllers\ReceivingController::class, 'index'])->name('receiving.entry');
    Route::post('/receiving', [\App\Http\Controllers\ReceivingController::class, 'store'])->name('receiving.store');
    Route::post('/receiving/manual', [\App\Http\Controllers\ReceivingController::class, 'manualStore'])->name('receiving.manual');
    Route::put('/receiving/{receiving}', [\App\Http\Controllers\ReceivingController::class, 'update'])->name('receiving.update');
    Route::delete('/receiving/{receiving}', [\App\Http\Controllers\ReceivingController::class, 'destroy'])->name('receiving.destroy');
    Route::get('/receiving/export', [\App\Http\Controllers\ReceivingController::class, 'export'])->name('receiving.export');

    // =============================================
    // Delivery Entry Routes
    // =============================================
    Route::get('/delivery-entry', [\App\Http\Controllers\DeliveryEntryController::class, 'index'])->name('delivery.entry');
    Route::post('/delivery', [\App\Http\Controllers\DeliveryEntryController::class, 'store'])->name('delivery.store');
    
    // Delivery print/export routes
    Route::get('/delivery/print/{dr_number}', [\App\Http\Controllers\DeliveryEntryController::class, 'print'])->name('delivery.print');
    Route::get('/delivery/print/sample/{id}', [\App\Http\Controllers\DeliveryEntryController::class, 'printBySampleId'])->name('delivery.print.sample');
    Route::get('/delivery/print/pdf/{identifier}', [\App\Http\Controllers\DeliveryEntryController::class, 'printPdf'])->name('delivery.print.pdf');

    // =============================================
    // Delivery Review Routes
    // =============================================
    Route::get('/delivery-review', [\App\Http\Controllers\DeliveryReviewController::class, 'index'])->name('delivery.review');
    Route::get('/delivery-review/id/{id}', [\App\Http\Controllers\DeliveryReviewController::class, 'detailsBySampleId'])->name('delivery.review.details.id');
    Route::get('/delivery-review/{dr_number}', [\App\Http\Controllers\DeliveryReviewController::class, 'details'])->name('delivery.review.details');
    Route::post('/delivery-review/{dr_number}/approve', [\App\Http\Controllers\DeliveryReviewController::class, 'approve'])->name('delivery.review.approve');

    // =============================================
    // Inventory Reports Routes
    // =============================================
    // Inventory per supplier
    Route::get('/inventory-per-supplier', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.per.supplier');
    Route::get('/inventory-per-supplier/print', [\App\Http\Controllers\InventoryController::class, 'print'])->name('inventory.per.supplier.print');
    
    // Inventory report (main report)
    Route::get('/inventory-report', [\App\Http\Controllers\InventoryReportController::class, 'index'])->name('inventory.report');
    Route::get('/inventory-report/print', [\App\Http\Controllers\InventoryReportController::class, 'print'])->name('inventory.report.print');
    Route::get('/inventory-report/download', [\App\Http\Controllers\InventoryReportController::class, 'download'])->name('inventory.report.download');

    // =============================================
    // Stock Management Routes
    // =============================================
    // Stock movement tracking
    Route::get('/stock-movement', [\App\Http\Controllers\StockMovementController::class, 'index'])->name('stock.movement');
    
    // Re-order level entry (low-stock alerts and fast-moving items)
    Route::get('/reorder-level-entry', [\App\Http\Controllers\ReorderController::class, 'index'])->name('reorder.level.entry');
    
    // Consignment items management
    Route::get('/consignment-items', [\App\Http\Controllers\ConsignmentController::class, 'index'])->name('consignment.items');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Profile & Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // =============================================
    // User Profile Routes
    // =============================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =============================================
    // Database Backup & Restore Routes
    // =============================================
    Route::post('/admin/db/backup', [\App\Http\Controllers\DatabaseController::class, 'backup'])->name('db.backup');
    Route::post('/admin/db/restore', [\App\Http\Controllers\DatabaseController::class, 'restore'])->name('db.restore');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
