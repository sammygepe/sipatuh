<?php

use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\KPIController;
use App\Http\Controllers\AtasanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\MasterController;
use Illuminate\Support\Facades\Route;

// Halaman default redirect ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Route untuk dashboard dan pencatatan
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AktivitasController::class, 'dashboard'])->name('dashboard');
    Route::post('/log/simpan', [AktivitasController::class, 'simpanLog'])->name('log.simpan');
    Route::get('/usul/form', [AktivitasController::class, 'formUsul'])->name('usul.form');
    Route::post('/usul/store', [AktivitasController::class, 'usulAktivitas'])->name('usul.store');
    Route::get('/kpi/laporan', [KPIController::class, 'laporanSaya'])->name('kpi.laporan');
    Route::get('/notifikasi/baca/{id}', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::get('/riwayat', [AktivitasController::class, 'riwayat'])->name('riwayat.aktivitas');
    
    // Admin & Atasan bisa lihat & edit master
    Route::get('/master/aktivitas', [MasterController::class, 'index'])->name('master.aktivitas');
    Route::get('/master/aktivitas/export-rutin', [MasterController::class, 'exportRutin'])->name('master.export.rutin');
    Route::get('/master/aktivitas/export-proyek', [MasterController::class, 'exportProyek'])->name('master.export.proyek');
    Route::get('/master/aktivitas/edit/{id}/{jenis}', [MasterController::class, 'edit'])->name('master.edit');
    Route::put('/master/aktivitas/update/{id}/{jenis}', [MasterController::class, 'update'])->name('master.update');
    Route::delete('/master/aktivitas/delete/{id}/{jenis}', [MasterController::class, 'delete'])->name('master.delete');
    
    // Route untuk atasan
    Route::middleware(['atasan'])->group(function () {
        Route::get('/approval', [AtasanController::class, 'pendingList'])->name('approval.list');
        Route::post('/approval/approve/{id}/{jenis}', [AtasanController::class, 'approve'])->name('approval.approve');
        Route::post('/approval/reject/{id}/{jenis}', [AtasanController::class, 'reject'])->name('approval.reject');
        Route::get('/kpi/bawahan', [KPIController::class, 'listBawahan'])->name('kpi.bawahan');
        Route::post('/kpi/simpan', [KPIController::class, 'simpanNilai'])->name('kpi.simpan');
        Route::get('/atasan/assign-aktivitas', [AtasanController::class, 'formAssignAktivitas'])->name('atasan.assign.form');
        Route::post('/atasan/assign-aktivitas', [AtasanController::class, 'assignAktivitas'])->name('atasan.assign.store');        
        Route::get('/kpi/laporan-bawahan', [KPIController::class, 'laporanBawahan'])->name('kpi.laporan.bawahan');
        Route::get('/kpi/bawahan/export', [KPIController::class, 'exportBawahan'])->name('kpi.export.bawahan');
        Route::get('/riwayat/bawahan', [AktivitasController::class, 'riwayatBawahan'])->name('riwayat.bawahan');
        Route::get('/riwayat/bawahan/export', [AktivitasController::class, 'exportRiwayatBawahan'])->name('riwayat.bawahan.export');
    });

    // Route untuk admin
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/departemen', [AdminController::class, 'kelolaDepartemen'])->name('departemen');
        Route::get('/departemen/create', [AdminController::class, 'createDepartemen'])->name('departemen.create');
        Route::post('/departemen/store', [AdminController::class, 'storeDepartemen'])->name('departemen.store');
        Route::get('/departemen/edit/{id}', [AdminController::class, 'editDepartemen'])->name('departemen.edit');
        Route::put('/departemen/update/{id}', [AdminController::class, 'updateDepartemen'])->name('departemen.update');
        Route::get('/user', [AdminController::class, 'kelolaUser'])->name('user');
        Route::get('/user/create', [AdminController::class, 'createUser'])->name('user.create');
        Route::post('/user/store', [AdminController::class, 'storeUser'])->name('user.store');
        Route::get('/user/edit/{id}', [AdminController::class, 'editUser'])->name('user.edit');
        Route::put('/user/update/{id}', [AdminController::class, 'updateUser'])->name('user.update');
        Route::get('/assign-aktivitas', [AdminController::class, 'formAssignAktivitas'])->name('assign.form');
        Route::post('/assign-aktivitas', [AdminController::class, 'assignAktivitas'])->name('assign.store');    
        Route::get('/kpi-semua', [KPIController::class, 'kpiSemua'])->name('kpi.semua');
        Route::get('/kpi-semua/export', [KPIController::class, 'exportSemua'])->name('kpi.export');
        // Route riwayat untuk ADMIN (hanya di sini)
        Route::get('/riwayat/semua', [AktivitasController::class, 'riwayatSemua'])->name('riwayat.semua');
        Route::get('/riwayat/semua/export', [AktivitasController::class, 'exportRiwayatSemua'])->name('riwayat.semua.export');
    });

});

// Auth routes (Breeze)
require __DIR__ . '/auth.php';