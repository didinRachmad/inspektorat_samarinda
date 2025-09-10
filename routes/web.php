<?php

use App\Http\Controllers\Admin\Setting\RoleController;
use App\Http\Controllers\Admin\Setting\PermissionController;
use App\Http\Controllers\Admin\Setting\UserManagementController;
use App\Http\Controllers\Admin\Setting\MenuController;
use App\Http\Controllers\Admin\Setting\ApprovalRouteController;
use App\Http\Controllers\Admin\Master\AuditiController;
use App\Http\Controllers\Admin\Master\KodeRekomendasiController;
use App\Http\Controllers\Admin\Master\KodeTemuanController;
use App\Http\Controllers\Admin\OngkirController;
use App\Http\Controllers\Admin\Pelaksanaan\KkaController;
use App\Http\Controllers\Admin\Pelaksanaan\LhaController;
use App\Http\Controllers\Admin\Pelaksanaan\TemuanController;
use App\Http\Controllers\Admin\Perencanaan\NonPkptController;
use App\Http\Controllers\Admin\Perencanaan\PkptController;
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

Route::prefix('auditi')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [AuditiController::class, 'index'])
            ->name('auditi.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [AuditiController::class, 'data'])
            ->name('auditi.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [AuditiController::class, 'create'])
            ->name('auditi.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [AuditiController::class, 'store'])
            ->name('auditi.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{auditi}', [AuditiController::class, 'edit'])
            ->name('auditi.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{auditi}', [AuditiController::class, 'update'])
            ->name('auditi.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{auditi}', [AuditiController::class, 'destroy'])
            ->name('auditi.destroy')
            ->middleware('menu.permission:destroy');
        Route::get('/getAuditi', [AuditiController::class, 'getAuditi'])
            ->name('auditi.getAuditi');
    });

Route::prefix('kode_temuan')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [KodeTemuanController::class, 'index'])
            ->name('kode_temuan.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [KodeTemuanController::class, 'data'])
            ->name('kode_temuan.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [KodeTemuanController::class, 'create'])
            ->name('kode_temuan.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [KodeTemuanController::class, 'store'])
            ->name('kode_temuan.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{kode_temuan}', [KodeTemuanController::class, 'edit'])
            ->name('kode_temuan.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{kode_temuan}', [KodeTemuanController::class, 'update'])
            ->name('kode_temuan.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{kode_temuan}', [KodeTemuanController::class, 'destroy'])
            ->name('kode_temuan.destroy')
            ->middleware('menu.permission:destroy');
        Route::get('/getAuditi', [KodeTemuanController::class, 'getAuditi'])
            ->name('kode_temuan.getAuditi');
    });

Route::prefix('kode_rekomendasi')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [KodeRekomendasiController::class, 'index'])
            ->name('kode_rekomendasi.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [KodeRekomendasiController::class, 'data'])
            ->name('kode_rekomendasi.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [KodeRekomendasiController::class, 'create'])
            ->name('kode_rekomendasi.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [KodeRekomendasiController::class, 'store'])
            ->name('kode_rekomendasi.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{kode_rekomendasi}', [KodeRekomendasiController::class, 'edit'])
            ->name('kode_rekomendasi.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{kode_rekomendasi}', [KodeRekomendasiController::class, 'update'])
            ->name('kode_rekomendasi.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{kode_rekomendasi}', [KodeRekomendasiController::class, 'destroy'])
            ->name('kode_rekomendasi.destroy')
            ->middleware('menu.permission:destroy');
        Route::get('/getAuditi', [KodeRekomendasiController::class, 'getAuditi'])
            ->name('kode_rekomendasi.getAuditi');
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

Route::prefix('non_pkpt')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [NonPkptController::class, 'index'])
            ->name('non_pkpt.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [NonPkptController::class, 'data'])
            ->name(name: 'non_pkpt.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [NonPkptController::class, 'create'])
            ->name('non_pkpt.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [NonPkptController::class, 'store'])
            ->name('non_pkpt.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{pkpt}', [NonPkptController::class, 'edit'])
            ->name('non_pkpt.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{pkpt}', [NonPkptController::class, 'update'])
            ->name('non_pkpt.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{pkpt}', [NonPkptController::class, 'destroy'])
            ->name('non_pkpt.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('lha')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [LhaController::class, 'index'])
            ->name('lha.index')
            ->middleware('menu.permission:index');
        Route::get('/lha/{lha}', [LhaController::class, 'show'])
            ->name('lha.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [LhaController::class, 'data'])
            ->name(name: 'lha.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [LhaController::class, 'create'])
            ->name('lha.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [LhaController::class, 'store'])
            ->name('lha.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{lha}', [LhaController::class, 'edit'])
            ->name('lha.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{lha}', [LhaController::class, 'update'])
            ->name('lha.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{lha}', [LhaController::class, 'destroy'])
            ->name('lha.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('kka')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [KkaController::class, 'index'])
            ->name('kka.index')
            ->middleware('menu.permission:index');
        Route::get('/create', [KkaController::class, 'create'])
            ->name('kka.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [KkaController::class, 'store'])
            ->name('kka.store')
            ->middleware('menu.permission:store');
        Route::delete('/destroy/{kka}', [KkaController::class, 'destroy'])
            ->name('kka.destroy')
            ->middleware('menu.permission:destroy');
    });


Route::prefix('temuan')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [TemuanController::class, 'index'])
            ->name('temuan.index')
            ->middleware('menu.permission:index');
        Route::get('/temuan/{temuan}', [TemuanController::class, 'show'])
            ->name('temuan.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [TemuanController::class, 'data'])
            ->name(name: 'temuan.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [TemuanController::class, 'create'])
            ->name('temuan.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [TemuanController::class, 'store'])
            ->name('temuan.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{temuan}', [TemuanController::class, 'edit'])
            ->name('temuan.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{temuan}', [TemuanController::class, 'update'])
            ->name('temuan.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{temuan}', [TemuanController::class, 'destroy'])
            ->name('temuan.destroy')
            ->middleware('menu.permission:destroy');
    });


require __DIR__ . '/auth.php';
