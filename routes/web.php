<?php

use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\User\WaitlistController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\User\UserTableController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminWaitlistController;
use App\Http\Controllers\User\UserReservationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return Inertia::render('Home', ['auth' => auth()->check() ? ['user' => auth()->user()] : null]);
})->name('home');

Route::get('/menu', [UserMenuController::class, 'index'])->name('menu.index');

Route::get('/cart', function () {
    return Inertia::render('Cart', ['auth' => auth()->check() ? ['user' => auth()->user()] : null]);
})->name('cart.index');

Route::get('/tables', [UserTableController::class, 'index'])->name('tables.index');

// Customer Auth Routes
// Route::prefix('user')->name('user.')->group(function () {
//     Route::get('/register', [RegisterController::class, 'create'])->name('register');
//     Route::post('/register', [RegisterController::class, 'store']);
//     Route::get('/login', [LoginController::class, 'create'])->name('login');
//     Route::post('/login', [LoginController::class, 'store']);
//     Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
// });

Route::prefix('user')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('user.login');
    Route::post('/login', [LoginController::class, 'store'])->name('user.login.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('user.logout');
    Route::get('/register', fn() => inertia('User/Register'))->name('user.register');
});

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Protected Routes (Authenticated Users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Protected Routes
    Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Menus
        Route::get('/menus', [MenuController::class, 'index'])->name('menus');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::get('/menus/profit-report', [MenuController::class, 'profitReport'])->name('menus.profit-report');

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{inventory}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::post('/inventory/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventory.add-stock');
        Route::get('/inventory/{inventory}/purchase-history', [InventoryController::class, 'purchaseHistory'])->name('inventory.purchase-history');
        Route::get('/inventory/stock-history', [InventoryController::class, 'stockHistory'])->name('inventory.stock-history');

        // Stocks
        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('/stocks/create', [StockController::class, 'create'])->name('stocks.create');
        Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
        Route::get('/stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
        Route::put('/stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
        Route::delete('/stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');

        // Waitlists
        Route::get('/waitlists', [AdminWaitlistController::class, 'index'])->name('waitlists.index');
        Route::put('/waitlists/{waitlist}', [AdminWaitlistController::class, 'update'])->name('waitlists.update');

        // Purchases
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

        // Orders
        Route::resource('orders', OrderController::class)
        ->only(['index']);
    Route::post('orders/{order}/toggle-priority', [OrderController::class, 'togglePriority'])
        ->name('orders.toggle-priority');
        Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('orders.updateStatus');

        // Reservations
        Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::put('/waitlists/{waitlist}', [ReservationController::class, 'updateWaitlistStatus'])->name('waitlists.update');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Tables
        Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
        Route::get('/tables/create', [TableController::class, 'create'])->name('tables.create');
        Route::post('/tables', [TableController::class, 'store'])->name('tables.store');
        Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('tables.edit');
        Route::put('/tables/{table}', [TableController::class, 'update'])->name('tables.update');
        Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('tables.destroy');
        Route::put('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('tables.updateStatus');
    });

    // Customer Protected Routes
    Route::middleware('customer')->group(function () {
        Route::get('/cart', [UserOrderController::class, 'cart'])->name('orders.cart');
        Route::post('/', [UserOrderController::class, 'store'])->name('orders.store');
        Route::get('/confirmation/{order}', [UserOrderController::class, 'confirmation'])->name('orders.confirmation');
        Route::get('/preorder', [UserOrderController::class, 'preorder'])->name('orders.preorder');
        Route::post('/preorder', [UserOrderController::class, 'storePreorder'])->name('orders.storePreorder');
        Route::get('/track/{order}', [UserOrderController::class, 'track'])->name('orders.track');
        Route::get('/my-orders', [UserOrderController::class, 'myOrders'])->name('orders.myOrders');

        Route::post('/tables', [UserTableController::class, 'store'])->name('tables.store');
        Route::get('/tables/available', [UserTableController::class, 'availableTables'])->name('tables.available');

        Route::get('/reservations', [UserReservationsController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/confirm-from-waitlist/{waitlist}', [UserReservationsController::class, 'confirmFromWaitlist'])->name('reservations.confirm-from-waitlist');
        Route::post('/reservations/store-from-waitlist/{waitlist}', [UserReservationsController::class, 'storeFromWaitlist'])->name('reservations.store-from-waitlist');

        Route::post('/waitlists', [WaitlistController::class, 'store'])->name('waitlists.store');
        Route::delete('/waitlists/{waitlist}', [WaitlistController::class, 'destroy'])->name('waitlists.destroy');
    });
});

// require __DIR__.'/auth.php';