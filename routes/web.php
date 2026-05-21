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


//Orders
Route::get('/admin/order/create', [OrderController::class, 'create'])->middleware(['auth'])->name('admin.orders.create');
Route::post('/admin/order/store', [OrderController::class, 'store'])->middleware(['auth'])->name('admin.orders.store');
Route::get('/admin/order/list', [OrderController::class, 'list'])->middleware(['auth'])->name('admin.orders.list');
Route::get('/admin/order/invoice/{id}', [OrderController::class, 'invoice'])->middleware(['auth'])->name('order.invoice');
Route::get('/admin/orders/list', [OrderController::class, 'list'])->middleware(['auth'])->name('admin.orders.index');
Route::get('/admin/order/edit/{id}', [OrderController::class, 'edit'])->middleware(['auth'])->name('admin.orders.edit');
Route::post('/admin/order/update/{id}', [OrderController::class, 'update'])->middleware(['auth'])->name('admin.orders.update');
Route::post('/admin/order/pay/{id}', [OrderController::class, 'recordPayment'])->middleware(['auth'])->name('admin.orders.pay');
Route::delete('/admin/order/delete/{id}', [OrderController::class, 'destroy'])->middleware(['auth'])->name('admin.orders.destroy');
Route::get('/admin/orders/cancelled', [OrderController::class, 'cancelledList'])->middleware(['auth'])->name('admin.orders.cancelled');


Route::get('/admin/customer/ledger/{customer}', [CustomerController::class, 'ledger'])->middleware(['auth'])->name('admin.customer.ledger');
Route::get('/admin/customers/outstanding-dues', [CustomerController::class, 'dues'])->middleware(['auth'])->name('admin.customers.dues');
Route::post('/admin/customers/pay-dues/{customer}', [CustomerController::class, 'recordCustomerDuesPayment'])->middleware(['auth'])->name('admin.customers.pay-dues');
Route::get('/admin/customers/list/{type?}', [CustomerController::class, 'index'])->middleware(['auth'])->name('admin.customers.list');

// Expenses
Route::get('/admin/expenses', [ExpenseController::class, 'index'])->middleware(['auth'])->name('admin.expenses.index');
Route::post('/admin/expenses/store', [ExpenseController::class, 'store'])->middleware(['auth'])->name('admin.expenses.store');
Route::get('/admin/expenses/categories', [ExpenseController::class, 'categories'])->middleware(['auth'])->name('admin.expenses.categories');
Route::post('/admin/expenses/categories/store', [ExpenseController::class, 'storeCategory'])->middleware(['auth'])->name('admin.expenses.categories.store');



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
