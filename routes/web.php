<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Setting\RoleController;
use App\Http\Controllers\Admin\Setting\PermissionController;
use App\Http\Controllers\Admin\Setting\UserManagementController;
use App\Http\Controllers\Admin\Setting\MenuController;
use App\Http\Controllers\Admin\Setting\ApprovalRouteController;
use App\Http\Controllers\Admin\Master\AuditiController;
use App\Http\Controllers\Admin\Master\IrbanwilController;
use App\Http\Controllers\Admin\Master\JenisPengawasanController;
use App\Http\Controllers\Admin\Master\KodeRekomendasiController;
use App\Http\Controllers\Admin\Master\KodeTemuanController;
use App\Http\Controllers\Admin\Master\MandatoryController;
use App\Http\Controllers\Admin\Pelaksanaan\KkaController;
use App\Http\Controllers\Admin\Pelaksanaan\LhpController;
use App\Http\Controllers\Admin\Pelaksanaan\TindakLanjutTemuanController;
use App\Http\Controllers\Admin\Perencanaan\NonPkptController;
use App\Http\Controllers\Admin\Perencanaan\PkptController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\Setting\SettingAnggaranController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RegulasiController;
use App\Http\Controllers\Admin\FaqController;
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

Route::middleware('auth')->group(function () {
    Route::get('/search', [SearchController::class, 'search'])->name('search');
    Route::get('/search/all', [SearchController::class, 'searchAll'])->name('search.all');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', function (\Illuminate\Http\Request $request) {
        $notifId = $request->input('id');
        if ($notifId) {
            $notification = auth()->user()->unreadNotifications()->find($notifId);
            if ($notification) {
                $notification->markAsRead();
            }
        }
        return response()->json(['status' => true]);
    })->name('notifications.markAsRead');
});

Route::prefix('dashboard')
    ->middleware(['auth'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard.index')
            ->middleware('auth');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index')->middleware('menu.permission:index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('menu.permission:update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy')->middleware('menu.permission:destroy');
});

// ============== SETTING ===============

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


Route::prefix('setting_anggaran')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [SettingAnggaranController::class, 'index'])
            ->name('setting_anggaran.index')
            ->middleware('menu.permission:index');
        Route::put('/update/{anggaran}', [SettingAnggaranController::class, 'update'])
            ->name('setting_anggaran.update')
            ->middleware('menu.permission:update');
        Route::get('/anggaran', [SettingAnggaranController::class, 'getAnggaran'])->name('setting_anggaran.getAnggaran');
    });


// ============== Master ===============

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

Route::prefix('irbanwil')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [IrbanwilController::class, 'index'])
            ->name('irbanwil.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [IrbanwilController::class, 'data'])
            ->name('irbanwil.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [IrbanwilController::class, 'create'])
            ->name('irbanwil.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [IrbanwilController::class, 'store'])
            ->name('irbanwil.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{irbanwil}', [IrbanwilController::class, 'edit'])
            ->name('irbanwil.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{irbanwil}', [IrbanwilController::class, 'update'])
            ->name('irbanwil.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{irbanwil}', [IrbanwilController::class, 'destroy'])
            ->name('irbanwil.destroy')
            ->middleware('menu.permission:destroy');
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

Route::prefix('mandatory')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [MandatoryController::class, 'index'])
            ->name('mandatory.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [MandatoryController::class, 'data'])
            ->name('mandatory.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [MandatoryController::class, 'create'])
            ->name('mandatory.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [MandatoryController::class, 'store'])
            ->name('mandatory.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{mandatory}', [MandatoryController::class, 'edit'])
            ->name('mandatory.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{mandatory}', [MandatoryController::class, 'update'])
            ->name('mandatory.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{mandatory}', [MandatoryController::class, 'destroy'])
            ->name('mandatory.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('jenis_pengawasan')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [JenisPengawasanController::class, 'index'])
            ->name('jenis_pengawasan.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [JenisPengawasanController::class, 'data'])
            ->name('jenis_pengawasan.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [JenisPengawasanController::class, 'create'])
            ->name('jenis_pengawasan.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [JenisPengawasanController::class, 'store'])
            ->name('jenis_pengawasan.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{jenis_pengawasan}', [JenisPengawasanController::class, 'edit'])
            ->name('jenis_pengawasan.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{jenis_pengawasan}', [JenisPengawasanController::class, 'update'])
            ->name('jenis_pengawasan.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{jenis_pengawasan}', [JenisPengawasanController::class, 'destroy'])
            ->name('jenis_pengawasan.destroy')
            ->middleware('menu.permission:destroy');
    });

// ============== PERENCANAAN ===============

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

Route::prefix('lhp')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [LhpController::class, 'index'])
            ->name('lhp.index')
            ->middleware('menu.permission:index');
        Route::get('show/{lhp}', [LhpController::class, 'show'])
            ->name('lhp.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [LhpController::class, 'data'])
            ->name(name: 'lhp.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [LhpController::class, 'create'])
            ->name('lhp.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [LhpController::class, 'store'])
            ->name('lhp.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{lhp}', [LhpController::class, 'edit'])
            ->name('lhp.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{lhp}', [LhpController::class, 'update'])
            ->name('lhp.update')
            ->middleware('menu.permission:update');
        Route::patch('/approve/{lhp}', [LhpController::class, 'approve'])
            ->name('lhp.approve')
            ->middleware('menu.permission:approve');
        Route::delete('/destroy/{lhp}', [LhpController::class, 'destroy'])
            ->name('lhp.destroy')
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

Route::prefix('tindak_lanjut_temuan')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [TindakLanjutTemuanController::class, 'index'])
            ->name('tindak_lanjut_temuan.index')
            ->middleware('menu.permission:index');
        Route::get('show/{tindak_lanjut_temuan}', [TindakLanjutTemuanController::class, 'show'])
            ->name('tindak_lanjut_temuan.show')
            ->middleware('menu.permission:show');
        Route::get('/data', [TindakLanjutTemuanController::class, 'data'])
            ->name(name: 'tindak_lanjut_temuan.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [TindakLanjutTemuanController::class, 'create'])
            ->name('tindak_lanjut_temuan.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [TindakLanjutTemuanController::class, 'store'])
            ->name('tindak_lanjut_temuan.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{tindak_lanjut_temuan}', [TindakLanjutTemuanController::class, 'edit'])
            ->name('tindak_lanjut_temuan.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{tindak_lanjut_temuan}', [TindakLanjutTemuanController::class, 'update'])
            ->name('tindak_lanjut_temuan.update')
            ->middleware('menu.permission:update');
        Route::patch('/approve/{tindak_lanjut_temuan}', [TindakLanjutTemuanController::class, 'approve'])
            ->name('tindak_lanjut_temuan.approve')
            ->middleware('menu.permission:approve');
        Route::delete('/destroy/{tindak_lanjut_temuan}', [TindakLanjutTemuanController::class, 'destroy'])
            ->name('tindak_lanjut_temuan.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('regulasi')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [RegulasiController::class, 'index'])
            ->name('regulasi.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [RegulasiController::class, 'data'])
            ->name('regulasi.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [RegulasiController::class, 'create'])
            ->name('regulasi.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [RegulasiController::class, 'store'])
            ->name('regulasi.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{regulasi}', [RegulasiController::class, 'edit'])
            ->name('regulasi.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{regulasi}', [RegulasiController::class, 'update'])
            ->name('regulasi.update')
            ->middleware('menu.permission:update');
        Route::get('/download/{regulasi}', [RegulasiController::class, 'download'])
            ->name('regulasi.download')
            ->middleware('menu.permission:show');
        Route::delete('/destroy/{regulasi}', [RegulasiController::class, 'destroy'])
            ->name('regulasi.destroy')
            ->middleware('menu.permission:destroy');
    });

Route::prefix('faq')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', [FaqController::class, 'index'])
            ->name('faq.index')
            ->middleware('menu.permission:index');
        Route::get('/data', [FaqController::class, 'data'])
            ->name('faq.data')
            ->middleware('menu.permission:index');
        Route::get('/create', [FaqController::class, 'create'])
            ->name('faq.create')
            ->middleware('menu.permission:create');
        Route::post('/store', [FaqController::class, 'store'])
            ->name('faq.store')
            ->middleware('menu.permission:store');
        Route::get('/edit/{faq}', [FaqController::class, 'edit'])
            ->name('faq.edit')
            ->middleware('menu.permission:edit');
        Route::put('/update/{faq}', [FaqController::class, 'update'])
            ->name('faq.update')
            ->middleware('menu.permission:update');
        Route::delete('/destroy/{faq}', [FaqController::class, 'destroy'])
            ->name('faq.destroy')
            ->middleware('menu.permission:destroy');
    });

require __DIR__ . '/auth.php';
