<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('inventory', InventoryController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SalesController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('receipts', ReceiptController::class);

    Route::get('/payments/purchase-order/{id}', [PaymentController::class, 'getPurchaseOrder'])->name('payments.purchase-order');
    Route::get('/receipts/sales-order/{id}', [ReceiptController::class, 'getSalesOrder'])->name('receipts.sales-order');
    Route::get('/inventory/item/{id}', [InventoryController::class, 'getItem'])->name('inventory.item');

    Route::post('/inventory/{inventory}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
    Route::get('/inventory/{inventory}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
    Route::put('/inventory/{inventory}/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
    Route::delete('/inventory/{inventory}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');

    Route::get('/stock/in', [StockController::class, 'stockIn'])->name('stock.in');
    Route::get('/stock/out', [StockController::class, 'stockOut'])->name('stock.out');
    Route::get('/stock/low-stock', [StockController::class, 'lowStock'])->name('stock.low-stock');

    Route::get('/reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');
});