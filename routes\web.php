<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LowStockController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Root redirect
Route::get('/', function () {
    return redirect()->route('pos.index');
});

// Protected POS & Management Routes
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard & Reports
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/custom-stats', [DashboardController::class, 'getCustomPeriodStats'])->name('dashboard.custom-stats');

    // 2. POS Terminal (Billing Screen)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // 3. Products Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products-update-direct', [ProductController::class, 'updateDirect'])->name('products.update.direct');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/quick-restock', [ProductController::class, 'quickRestockSubmit'])->name('products.restock.direct');
    Route::post('/products/{product}/restock', [ProductController::class, 'quickRestock'])->name('products.restock');
    
    // Bulk product management & Excel import/export
    Route::get('/products-bulk-add', [ProductController::class, 'bulkAddView'])->name('products.bulk_add');
    Route::post('/products-bulk-store', [ProductController::class, 'bulkStore'])->name('products.bulk_store');
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export_csv');
    Route::post('/products/import/csv', [ProductController::class, 'importCsv'])->name('products.import_csv');
    Route::get('/products/barcodes/print', [ProductController::class, 'barcodesSheet'])->name('products.barcodes');

    // 4. Inventory Management & Logs
    Route::get('/inventory/logs', [InventoryController::class, 'index'])->name('inventory.logs');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::post('/inventory/quick-update', [InventoryController::class, 'quickStockUpdate'])->name('inventory.quick_update');
    Route::post('/inventory/quick-adjust', [InventoryController::class, 'quickAdjustDelta'])->name('inventory.quick_adjust');
    Route::put('/inventory/logs/{inventoryLog}', [InventoryController::class, 'update'])->name('inventory.logs.update');
    Route::delete('/inventory/logs/{inventoryLog}', [InventoryController::class, 'destroy'])->name('inventory.logs.destroy');

    // 5. Low Stock Alerts
    Route::get('/inventory/low-stock', [LowStockController::class, 'index'])->name('inventory.low_stock');

    // 6. Sales History & Invoices
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'thermalReceipt'])->name('sales.receipt');

    // 7. Product Returns & Exchanges
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::get('/returns/{productReturn}/receipt', [ReturnController::class, 'thermalReceipt'])->name('returns.receipt');

    // 8. Expenses & Cash Drawer Ledger
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // 9. Users & Credentials
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // 10. Shop Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
