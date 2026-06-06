<?php
use App\Http\Controllers\Admin\AutentifkasiController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
//  udah diprefix admin di bootstrap/app.php, jadi gak perlu lagi di sini
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AutentifkasiController::class, 'loginIndex'])->name('admin.login');
    Route::post('/login', [AutentifkasiController::class, 'loginProcess'])->name('admin.login.process');
});
Route::middleware(['web', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Role Management untuk guru 
    Route::get('/roles', [AdminController::class, 'indexRole'])->name('admin.roles.index');
    Route::post('/roles/store', [AdminController::class, 'storeRole'])->name('admin.roles.store');
    Route::post('/roles/update/{id}', [AdminController::class, 'updateRole'])->name('admin.roles.update');
    Route::post('/roles/delete/{id}', [AdminController::class, 'destroyRole'])->name('admin.roles.delete');

    // Route untuk manajemen guru
    Route::get('/guru', [AdminController::class, 'guruIndex'])->name('admin.guru.index');
    Route::get('/guru/edit/{id}', [AdminController::class, 'editGuru'])->name('admin.guru.edit');
    Route::post('/guru/update/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
    Route::post('/guru/delete/{id}', [AdminController::class, 'destroyGuru'])->name('admin.guru.delete');
    Route::get('/guru/create', [AdminController::class, 'createGuru'])->name('admin.guru.create');
    Route::post('/guru/store', [AdminController::class, 'storeGuru'])->name('admin.guru.store');
});