<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockTransferController;
use Illuminate\Support\Facades\Route;

// Public Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware(['auth', 'branch.active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/branch/switch', [AuthController::class, 'switchBranch'])->name('branch.switch');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS & Penjualan
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::get('/receipt/{id}', [PosController::class, 'receipt'])->name('receipt');
    });
    Route::get('/sales', [PosController::class, 'sales'])->name('sales.index');

    // Produksi & BOM
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::get('/create', [ProductionController::class, 'create'])->name('create');
        Route::post('/calc', [ProductionController::class, 'calculatePreview'])->name('calc');
        Route::post('/', [ProductionController::class, 'store'])->name('store');
        Route::get('/{id}/finish', [ProductionController::class, 'finishForm'])->name('finish.form');
        Route::post('/{id}/finish', [ProductionController::class, 'finishPost'])->name('finish.post');
    });

    // Resep
    Route::prefix('recipes')->name('recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');
        Route::get('/{id}', [RecipeController::class, 'show'])->name('show');
    });

    // Inventaris
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/ledger', [InventoryController::class, 'ledger'])->name('ledger');
    });

    // Transfer Stok Antarcabang
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [StockTransferController::class, 'index'])->name('index');
        Route::get('/create', [StockTransferController::class, 'create'])->name('create');
        Route::post('/', [StockTransferController::class, 'store'])->name('store');
        Route::post('/{id}/ship', [StockTransferController::class, 'ship'])->name('ship');
        Route::get('/{id}/receive', [StockTransferController::class, 'receiveForm'])->name('receive.form');
        Route::post('/{id}/receive', [StockTransferController::class, 'receivePost'])->name('receive.post');
    });

    // Pembelian (Procurement)
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{id}/receive', [PurchaseController::class, 'receiveForm'])->name('receive.form');
        Route::post('/{id}/receive', [PurchaseController::class, 'receivePost'])->name('receive.post');
        Route::post('/{id}/pay', [PurchaseController::class, 'payPost'])->name('pay');
    });

    // Keuangan & Kas
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/cash', [FinanceController::class, 'cashIndex'])->name('cash');
        Route::post('/expenses', [FinanceController::class, 'storeExpense'])->name('expenses.store');
    });

    // Akuntansi
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('/journals', [AccountingController::class, 'journals'])->name('journals');
        Route::get('/coa', [AccountingController::class, 'chartOfAccounts'])->name('coa');
    });

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Master Data
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/ingredients', [MasterController::class, 'ingredients'])->name('ingredients');
        Route::get('/products', [MasterController::class, 'products'])->name('products');
    });

    // Cabang
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
});
