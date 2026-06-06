<?php
use App\Http\Controllers\Admin\AutentifkasiController;
//  udah diprefix admin di bootstrap/app.php, jadi gak perlu lagi di sini
Route::get('tes', [AutentifkasiController::class, 'index'])->name('admin.tes');