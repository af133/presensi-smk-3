<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\PresensiController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\Guru\ProfileController;
use App\Http\Controllers\Guru\AutenfikasiController as AuthGuru;
Route::get('/',[PresensiController::class,'denahIndex'])->name('denah');
Route::get('/login',[AuthGuru::class,'showLoginForm'])->name('login');
Route::post('/login/check',[AuthGuru::class,'login'])->name('guru.login.process');
Route::post('/logout',[AuthGuru::class,'logout'])->name('guru.logout');
Route::middleware(['web', 'guru'])->group(function () {
    Route::get('/monitoring', [PresensiController::class, 'monitoringIndex'])
    ->name('monitoring.index');
    // Presensi
    Route::get('/presensi',[PresensiController::class,'index'])->name('guru.dashboard');
    Route::get('/presensi/list',[PresensiController::class, 'index'])->name('guru.presensi.list');
    Route::get('/presensi/{id}/create',[PresensiController::class, 'create'])->name('guru.presensi.create');
    Route::post('/presensi/{id}/store',[PresensiController::class, 'store'])->name('guru.presensi.store');
    // Jurnal
    Route::post('/jurnal/{presenceId}',[PresensiController::class, 'storeJournal'])->name('guru.jurnal.store');
    // Report siswa presensi
    Route::get('/report', [WaliKelasController::class, 'index'])->name('guru.report.index');
    Route::get('/report/{student}/download',[WaliKelasController::class, 'downloadReport'])->name('walikelas.report.download');
    Route::get('/report/preview/{studentId}', [WaliKelasController::class, 'previewReport'])->name('walikelas.report.preview');

    // Report presensi guru
    Route::get('/report/guru',[PresensiController::class, 'reportGuru'])->name('report.index');
    Route::get('/report/guru/{teacher}/download',[PresensiController::class, 'downloadReportGuru'])->name('waka.report.download');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('guru.profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('guru.profile.update');
});
Route::fallback(function () {
    return redirect('/');
});