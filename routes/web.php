<?php

use Inertia\Inertia;
use App\Models\Table;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\User\UserMenuController;
use App\Http\Controllers\User\WaitlistController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\User\UserTableController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\User\UserReservationsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });


Route::prefix('admin')->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/dashboard/stock', [DashboardController::class, 'updateStock'])->name('admin.dashboard.stock');
    Route::post('/dashboard/waste', [DashboardController::class, 'logWaste'])->name('admin.dashboard.waste');

    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        Route::get('/menus', [MenuController::class, 'index'])->name('admin.menus');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('admin.menus.store');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('admin.menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');

        Route::get('/inventory', [InventoryController::class, 'index']);
        Route::post('/inventory', [InventoryController::class, 'store'])->name('admin.inventory.store');
        Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('admin.inventory.update');
        Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('admin.inventory.destroy');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('admin.purchases.store');

        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');

        Route::post('/orders/{order}/priority', [OrderController::class, 'togglePriority'])->name('admin.orders.priority');

                Route::get('/reservations', [ReservationController::class, 'index'])->name('admin.reservations.index');
                Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('admin.reservations.show');
                Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('admin.reservations.update');
                Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('admin.reservations.destroy');
                Route::put('/reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('admin.reservations.status');

                    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
                    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
                    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
                    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
                    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
                    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::get('/tables/create', [TableController::class, 'create'])->name('admin.tables.create');
    Route::post('/tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::get('/tables/{table}/edit', [TableController::class, 'edit'])->name('admin.tables.edit');
    Route::put('/tables/{table}', [TableController::class, 'update'])->name('admin.tables.update');
    Route::delete('/tables/{table}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
    Route::put('/tables/{table}/status', [TableController::class, 'updateStatus'])->name('admin.tables.status');
});



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [UserMenuController::class, 'index'])->name('menu');
Route::get('/reservation', [HomeController::class, 'index'])->name('reservation');

    Route::inertia('/cart', 'Cart')->name('cart.index');

        Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [UserOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/confirmation/{order}', [UserOrderController::class, 'confirmation'])->name('orders.confirmation');
        Route::get('/orders/track/{order}', [UserOrderController::class, 'track'])->name('orders.track');
        Route::get('/orders/preorder', [UserOrderController::class, 'preorder'])->name('orders.preorder');
    Route::post('/orders/preorder', [UserOrderController::class, 'storePreorder'])->name('orders.preorder.store');

        Route::get('/tables', [UserTableController::class, 'index'])->name('tables.index');
        Route::post('/tables', [UserTableController::class, 'store'])->name('tables.store');
        Route::get('/tables/available', [UserTableController::class, 'availableTables'])->name('tables.available');
        Route::get('/reservations', [UserReservationsController::class, 'index'])->name('reservations.index');

        Route::get('/waitlist', [WaitlistController::class, 'index'])->name('waitlist.index');
    Route::post('/waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');


    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');

    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

