<?php

use App\Http\Controllers\Admin\Setting\RoleController;
use App\Http\Controllers\Admin\Setting\PermissionController;
use App\Http\Controllers\Admin\Setting\UserManagementController;
use App\Http\Controllers\Admin\Setting\MenuController;
use App\Http\Controllers\Admin\Setting\ApprovalRouteController;
use App\Http\Controllers\Admin\Master\CustomersController;
use App\Http\Controllers\Admin\Master\ProductsController;
use App\Http\Controllers\Admin\OngkirController;
use App\Http\Controllers\Admin\PkptController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\Transaksi\DeliveryOrdersController;
use App\Http\Controllers\Admin\Transaksi\SalesOrdersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout')->middleware('auth');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
});

Route::get('/search', [SearchController::class, 'search'])->middleware('auth')->name('search');
Route::get('/search/all', [SearchController::class, 'searchAll'])->middleware('auth')->name('search.all');

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')->middleware('menu.permission:index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('menu.permission:update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('menu.permission:destroy');
});

Route::prefix('users')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])
            ->name('users.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [UserManagementController::class, 'data'])
            ->name(name: 'users.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [UserManagementController::class, 'create'])
            ->name('users.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [UserManagementController::class, 'store'])
            ->name('users.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{user}', [UserManagementController::class, 'edit'])
            ->name('users.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{user}', [UserManagementController::class, 'update'])
            ->name('users.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{user}', [UserManagementController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('menu.permission:destroy');
        Route::post('/reset-password/{user}', [UserManagementController::class, 'resetPassword'])
            ->name('users.reset-password')
            ->middleware('menu.permission:update');

        Route::get('/getUsersByRole/{role}', [UserManagementController::class, 'getUsersByRole'])
            ->name('users.getUsersByRole');
    });

Route::prefix('roles')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->name('roles.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [RoleController::class, 'data'])
            ->name(name: 'roles.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [RoleController::class, 'create'])
            ->name('roles.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [RoleController::class, 'store'])
            ->name('roles.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{role}', [RoleController::class, 'edit'])
            ->name('roles.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{role}', [RoleController::class, 'update'])
            ->name('roles.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{role}', [RoleController::class, 'destroy'])
            ->name('roles.destroy')
            ->middleware('menu.permission:destroy');
        // Route untuk assign permission ke role
        Route::get('/{role}/menu-permissions', [RoleController::class, 'menuPermissions'])
            ->name('roles.menu-permissions')
            ->middleware('menu.permission:edit');
        Route::post('/{role}/menu-permissions', [RoleController::class, 'assignMenuPermissions'])
            ->name('roles.assign-menu-permissions')
            ->middleware('menu.permission:update');

        Route::get('/getRoles', [RoleController::class, 'getRoles'])
            ->name('roles.getRoles');
    });

Route::prefix('permissions')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->name('permissions.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [PermissionController::class, 'data'])
            ->name(name: 'permissions.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [PermissionController::class, 'create'])
            ->name('permissions.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [PermissionController::class, 'store'])
            ->name('permissions.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{permission}', [PermissionController::class, 'edit'])
            ->name('permissions.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{permission}', [PermissionController::class, 'update'])
            ->name('permissions.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{permission}', [PermissionController::class, 'destroy'])
            ->name('permissions.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('approval_routes')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [ApprovalRouteController::class, 'index'])
            ->name('approval_routes.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [ApprovalRouteController::class, 'data'])
            ->name(name: 'approval_routes.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [ApprovalRouteController::class, 'create'])
            ->name('approval_routes.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [ApprovalRouteController::class, 'store'])
            ->name('approval_routes.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{approval_route}', [ApprovalRouteController::class, 'edit'])
            ->name('approval_routes.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{approval_route}', [ApprovalRouteController::class, 'update'])
            ->name('approval_routes.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{approval_route}', [ApprovalRouteController::class, 'destroy'])
            ->name('approval_routes.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('menus')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [MenuController::class, 'index'])
            ->name('menus.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [MenuController::class, 'data'])
            ->name(name: 'menus.data')
            ->middleware('menu.permission:index');
        Route::get('/approve/{menu}', [MenuController::class, 'approve'])
            ->name('menus.approve')
            ->middleware('menu.permission:approve');
        Route::get('/create', [MenuController::class, 'create'])
            ->name('menus.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [MenuController::class, 'store'])
            ->name('menus.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{menu}', [MenuController::class, 'edit'])
            ->name('menus.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{menu}', [MenuController::class, 'update'])
            ->name('menus.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{menu}', [MenuController::class, 'destroy'])
            ->name('menus.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('master_products')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [ProductsController::class, 'index'])
            ->name('master_products.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [ProductsController::class, 'data'])
            ->name('master_products.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [ProductsController::class, 'create'])
            ->name('master_products.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [ProductsController::class, 'store'])
            ->name('master_products.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{product}', [ProductsController::class, 'edit'])
            ->name('master_products.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{product}', [ProductsController::class, 'update'])
            ->name('master_products.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{product}', [ProductsController::class, 'destroy'])
            ->name('master_products.destroy')
            ->middleware('menu.permission:destroy');
        Route::get('/getProducts', [ProductsController::class, 'getProducts'])
            ->name('master_products.getProducts');
    });

Route::prefix('master_customers')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [CustomersController::class, 'index'])
            ->name('master_customers.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [CustomersController::class, 'data'])
            ->name('master_customers.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [CustomersController::class, 'create'])
            ->name('master_customers.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [CustomersController::class, 'store'])
            ->name('master_customers.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{customer}', [CustomersController::class, 'edit'])
            ->name('master_customers.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{customer}', [CustomersController::class, 'update'])
            ->name('master_customers.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{customer}', [CustomersController::class, 'destroy'])
            ->name('master_customers.destroy')
            ->middleware('menu.permission:destroy');
        Route::get('/getCustomers', [CustomersController::class, 'getCustomers'])
            ->name('master_customers.getCustomers');
    });

Route::prefix('transaksi_sales_orders')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [SalesOrdersController::class, 'index'])
            ->name('transaksi_sales_orders.index')
            ->middleware('menu.permission:index');
        Route::get('/transaksi_sales_orders/{salesOrder}', [SalesOrdersController::class, 'show'])
            ->name('transaksi_sales_orders.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [SalesOrdersController::class, 'data'])
            ->name('transaksi_sales_orders.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [SalesOrdersController::class, 'create'])
            ->name('transaksi_sales_orders.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [SalesOrdersController::class, 'store'])
            ->name('transaksi_sales_orders.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{salesOrder}', [SalesOrdersController::class, 'edit'])
            ->name('transaksi_sales_orders.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{salesOrder}', [SalesOrdersController::class, 'update'])
            ->name('transaksi_sales_orders.update')
            ->middleware('menu.permission:update');
        Route::post('/approve/{salesOrder}', [SalesOrdersController::class, 'approve'])
            ->name('transaksi_sales_orders.approve')
            ->middleware('menu.permission:approve');
        Route::post('/approve/{salesOrder}/revise', [SalesOrdersController::class, 'revise'])
            ->name('transaksi_sales_orders.revise')
            ->middleware('menu.permission:approve');
        Route::post('/approve/{salesOrder}/reject', [SalesOrdersController::class, 'reject'])
            ->name('transaksi_sales_orders.reject')
            ->middleware('menu.permission:approve');
        Route::delete('/destroy/{salesOrder}', [SalesOrdersController::class, 'destroy'])
            ->name('transaksi_sales_orders.destroy')
            ->middleware('menu.permission:destroy');

        Route::get('/getSalesOrders', [SalesOrdersController::class, 'getSalesOrders'])
            ->name('transaksi_sales_orders.getSalesOrders');
        Route::get('/getSalesOrderDetail/{salesOrder}', [SalesOrdersController::class, 'getSalesOrderDetail'])
            ->name('transaksi_sales_orders.getSalesOrderDetail');
    });

Route::prefix('transaksi_delivery_orders')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [DeliveryOrdersController::class, 'index'])
            ->name('transaksi_delivery_orders.index')
            ->middleware('menu.permission:index');
        Route::get('/transaksi_delivery_orders/{deliveryOrder}', [DeliveryOrdersController::class, 'show'])
            ->name('transaksi_delivery_orders.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [DeliveryOrdersController::class, 'data'])
            ->name('transaksi_delivery_orders.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [DeliveryOrdersController::class, 'create'])
            ->name('transaksi_delivery_orders.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [DeliveryOrdersController::class, 'store'])
            ->name('transaksi_delivery_orders.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{deliveryOrder}', [DeliveryOrdersController::class, 'edit'])
            ->name('transaksi_delivery_orders.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{deliveryOrder}', [DeliveryOrdersController::class, 'update'])
            ->name('transaksi_delivery_orders.update')
            ->middleware('menu.permission:update');
        Route::post('/approve/{deliveryOrder}', [DeliveryOrdersController::class, 'approve'])
            ->name('transaksi_delivery_orders.approve')
            ->middleware('menu.permission:approve');
        Route::post('/approve/{deliveryOrder}/revise', [DeliveryOrdersController::class, 'revise'])
            ->name('transaksi_delivery_orders.revise')
            ->middleware('menu.permission:approve');
        Route::post('/approve/{deliveryOrder}/reject', [DeliveryOrdersController::class, 'reject'])
            ->name('transaksi_delivery_orders.reject')
            ->middleware('menu.permission:approve');
        Route::delete('/destroy/{deliveryOrder}', [DeliveryOrdersController::class, 'destroy'])
            ->name('transaksi_delivery_orders.destroy')
            ->middleware('menu.permission:destroy');
    });


Route::get('/biteship/areas', [OngkirController::class, 'getAreas'])->middleware('auth')->name('biteship.areas');
Route::post('/biteship/cek-ongkir', [OngkirController::class, 'cekOngkir'])->middleware('auth')->name('biteship.cek-ongkir');


Route::prefix('pkpt')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [PkptController::class, 'index'])
            ->name('pkpt.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [PkptController::class, 'data'])
            ->name(name: 'pkpt.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [PkptController::class, 'create'])
            ->name('pkpt.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [PkptController::class, 'store'])
            ->name('pkpt.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{pkpt}', [PkptController::class, 'edit'])
            ->name('pkpt.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{pkpt}', [PkptController::class, 'update'])
            ->name('pkpt.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{pkpt}', [PkptController::class, 'destroy'])
            ->name('pkpt.destroy')
            ->middleware('menu.permission:destroy');
    });


require __DIR__ . '/auth.php';
