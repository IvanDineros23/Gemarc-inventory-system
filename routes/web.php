<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome.page');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Placeholder pages for inventory system
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/receiving-entry', 'pages.receiving-entry')->name('receiving.entry');
    Route::view('/inventory-per-supplier', 'pages.inventory-per-supplier')->name('inventory.per.supplier');
    Route::view('/inventory-report', 'pages.inventory-report')->name('inventory.report');
    Route::view('/consignment-items', 'pages.consignment-items')->name('consignment.items');
    Route::view('/reorder-level-entry', 'pages.reorder-level-entry')->name('reorder.level.entry');
    Route::view('/delivery-entry', 'pages.delivery-entry')->name('delivery.entry');
    Route::view('/stock-movement', 'pages.stock-movement')->name('stock.movement');
    Route::view('/delivery-review', 'pages.delivery-review')->name('delivery.review');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
