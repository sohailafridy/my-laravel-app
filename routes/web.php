<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', [TestController::class, 'index'])->name('test');



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [AdminController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/order/create', [OrderController::class, 'create'])->middleware(['auth'])->name('admin.orders.create');

//product routes/inventory routes
Route::get('/admin/stock/products/{id?}', [InventoryController::class, 'index'])->middleware(['auth'])->name('admin.products');
Route::post('/admin/stock/products', [InventoryController::class, 'store'])->middleware(['auth'])->name('admin.products.store');
Route::get('/admin/stock/restock', [InventoryController::class, 'restock'])->middleware(['auth'])->name('admin.restock');
Route::post('/admin/stock/restock', [InventoryController::class, 'restockStore'])->middleware(['auth'])->name('admin.restock.store');
Route::get('/admin/stock/purchases/{sku?}', [InventoryController::class, 'purchases'])->middleware(['auth'])->name('admin.purchases');
Route::get('/admin/stock/purchases/reverse/{id}', [InventoryController::class, 'reversePurchase'])->middleware(['auth'])->name('admin.purchases.reverse');
Route::get('/admin/stock/movement', [InventoryController::class, 'productMovement'])->middleware(['auth'])->name('admin.product.movement');
Route::get('/admin/stock/summary', [InventoryController::class, 'productSummary'])->middleware(['auth'])->name('admin.product.summary');
Route::get('/admin/stock/product-ledger/{id}', [InventoryController::class, 'productLedger'])->middleware(['auth'])->name('admin.product.ledger');


require __DIR__.'/auth.php';
