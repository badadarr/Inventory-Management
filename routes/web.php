<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesPointController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'pageTitle' => 'Home',
    ]);
})->name('home');

// Contact
Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::middleware('auth')->group(function () {
    // Dashboard - All roles
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Profile - All roles
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.update.image');
    
    // Users - Super Admin & Admin only
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Master Data - Super Admin & Admin only
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('unit-types', UnitTypeController::class);
        Route::apiResource('suppliers', SupplierController::class);
        Route::apiResource('sales', SalesController::class);
    });

    // Employees - Super Admin, Admin & Finance
    Route::middleware('role:super_admin,admin,finance')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
    });

    // Products - Super Admin, Admin & Warehouse
    Route::middleware('role:super_admin,admin,warehouse')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Customers - Super Admin, Admin & Sales
    Route::middleware('role:super_admin,admin,sales')->group(function () {
        Route::resource('customers', CustomerController::class);
    });

    // Finance - Super Admin, Admin & Finance
    Route::middleware('role:super_admin,admin,finance')->group(function () {
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('salaries', SalaryController::class);
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    });

    // Orders - Super Admin, Admin, Sales & Warehouse
    Route::middleware('role:super_admin,admin,sales,warehouse')->group(function () {
        Route::apiResource('orders', OrderController::class);
        Route::put('orders/{order}/settle', [OrderController::class, 'settle'])->name('orders.settle');
        Route::put('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    });

    // POS - Super Admin, Admin & Sales
    Route::middleware('role:super_admin,admin,sales')->group(function () {
        Route::get('pos', [CartController::class, 'index'])->name('carts.index');
        Route::post('carts/{productId}', [CartController::class, 'addToCart'])->name('carts.store');
        Route::put('carts/{cart}', [CartController::class, 'updateQuantity'])->name('carts.update');
        Route::delete('carts/{cart}', [CartController::class, 'delete'])->name('carts.delete');
        Route::delete('carts/delete/all', [CartController::class, 'deleteForUser'])->name('carts.delete.all');
        Route::put('carts/{cart}/increment', [CartController::class, 'incrementQuantity'])->name('carts.increment');
        Route::put('carts/{cart}/decrement', [CartController::class, 'decrementQuantity'])->name('carts.decrement');
    });

    // Settings - Super Admin & Admin only
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Sales Points - Super Admin, Admin & Sales
    Route::middleware('role:super_admin,admin,sales')->group(function () {
        Route::get('sales-points', [SalesPointController::class, 'index'])->name('sales-points.index');
        Route::post('sales-points', [SalesPointController::class, 'store'])->name('sales-points.store');
    });

    // Reports
    Route::middleware('role:super_admin,admin,finance')->group(function () {
        Route::get('reports/outstanding', [ReportController::class, 'outstanding'])->name('reports.outstanding');
    });
    
    Route::middleware('role:super_admin,admin,sales')->group(function () {
        Route::get('reports/top-customers', [ReportController::class, 'topCustomers'])->name('reports.top-customers');
    });
});

require __DIR__ . '/auth.php';
