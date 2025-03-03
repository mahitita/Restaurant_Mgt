<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\Admin\CategoryController;

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
// Route::get('/', function () {
//       return Inertia::render('HelloWorld');
// });

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::get('/menu', function () {
        return Inertia::render('Admin/Menu');
    })->name('admin.menu');


        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('admin.orders.store');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
   


    Route::get('/reservations', function () {
        return Inertia::render('Admin/Reservations');
    })->name('admin.reservations');

    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

});

// Route::prefix('admin')->group(function () {
//     Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
//     Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
//     Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
// });
Route::prefix('admin')->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('menu', MenuController::class)->except(['show']);
});
Route::get('/', function () {
    return Inertia::render('Home');
});
Route::get('/about', function () {
    return Inertia::render('About');
});
//Route::get('/', [HelloWorldController::class, 'index'])->name('myhome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

