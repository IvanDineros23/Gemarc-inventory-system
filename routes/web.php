<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome.page');







Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Placeholder pages for inventory system
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/receiving-entry', [\App\Http\Controllers\ReceivingController::class, 'index'])->name('receiving.entry');
    // Receiving endpoints (store and export)
    Route::post('/receiving', [\App\Http\Controllers\ReceivingController::class, 'store'])->name('receiving.store');
    Route::post('/receiving/manual', [\App\Http\Controllers\ReceivingController::class, 'manualStore'])->name('receiving.manual');
    Route::get('/receiving/export', [\App\Http\Controllers\ReceivingController::class, 'export'])->name('receiving.export');
    Route::put('/receiving/{receiving}', [\App\Http\Controllers\ReceivingController::class, 'update'])->name('receiving.update');
    Route::delete('/receiving/{receiving}', [\App\Http\Controllers\ReceivingController::class, 'destroy'])->name('receiving.destroy');
    Route::get('/inventory-per-supplier', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.per.supplier');
    Route::get('/inventory-per-supplier/print', [\App\Http\Controllers\InventoryController::class, 'print'])->name('inventory.per.supplier.print');
    Route::get('/inventory-report', [\App\Http\Controllers\InventoryReportController::class, 'index'])->name('inventory.report');
    Route::get('/inventory-report/print', [\App\Http\Controllers\InventoryReportController::class, 'print'])->name('inventory.report.print');
    Route::get('/inventory-report/download', [\App\Http\Controllers\InventoryReportController::class, 'download'])->name('inventory.report.download');
    Route::get('/consignment-items', [\App\Http\Controllers\ConsignmentController::class, 'index'])->name('consignment.items');
    // Re-order level entry should display low-stock notifications (fast-moving highlights)
    Route::get('/reorder-level-entry', [\App\Http\Controllers\ReorderController::class, 'index'])->name('reorder.level.entry');
    // Delivery entry remains a simple placeholder page
    Route::view('/delivery-entry', 'pages.delivery-entry')->name('delivery.entry');
    Route::get('/stock-movement', [\App\Http\Controllers\StockMovementController::class, 'index'])->name('stock.movement');
    Route::view('/delivery-review', 'pages.delivery-review')->name('delivery.review');
    Route::get('/product-management', [\App\Http\Controllers\ProductManagementController::class, 'index'])->name('product.management');
    Route::post('/product-management', [\App\Http\Controllers\ProductManagementController::class, 'store'])->name('product.management.store');
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard/summary', [\App\Http\Controllers\DashboardController::class, 'summary'])->name('api.dashboard.summary');
    Route::get('/api/dashboard/low-stock', [\App\Http\Controllers\DashboardController::class, 'lowStock'])->name('api.dashboard.lowstock');
    Route::get('/api/dashboard/receivings', [\App\Http\Controllers\DashboardController::class, 'receivingsSeries'])->name('api.dashboard.receivings');
    Route::get('/api/dashboard/top-suppliers', [\App\Http\Controllers\DashboardController::class, 'topSuppliers'])->name('api.dashboard.topsuppliers');
    Route::get('/api/dashboard/stock-value', [\App\Http\Controllers\DashboardController::class, 'stockValueTrend'])->name('api.dashboard.stockvalue');
    Route::get('/dashboard/export-low-stock', [\App\Http\Controllers\DashboardController::class, 'exportLowStock'])->name('dashboard.export.lowstock');
    // Live product search for AJAX live-search on pages like Receiving Entry
    Route::get('/product-search', [\App\Http\Controllers\ProductManagementController::class, 'search'])->name('product.search');
    Route::get('/product-management/{product}/edit', [\App\Http\Controllers\ProductManagementController::class, 'edit'])->name('product.management.edit');
    Route::put('/product-management/{product}', [\App\Http\Controllers\ProductManagementController::class, 'update'])->name('product.management.update');
    Route::delete('/product-management/{product}', [\App\Http\Controllers\ProductManagementController::class, 'destroy'])->name('product.management.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
