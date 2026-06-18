<?php
use App\Http\Controllers\Admin\AutentifkasiController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminSubjectController;
use App\Http\Controllers\Admin\AdminSiswaController;
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
    Route::post('/roles/update/{id}', [AdminController::class, 'roleUpdate'])->name('admin.roles.update');
    Route::post('/roles/delete/{id}', [AdminController::class, 'destroyRole'])->name('admin.roles.delete');

    // Route untuk manajemen guru
    Route::get('/guru', [AdminController::class, 'guruIndex'])->name('admin.guru.index');
    Route::get('/guru/edit/{id}', [AdminController::class, 'editGuru'])->name('admin.guru.edit');
    Route::post('/guru/update/{id}', [AdminController::class, 'updateGuru'])->name('admin.guru.update');
    Route::post('/guru/delete/{id}', [AdminController::class, 'destroyGuru'])->name('admin.guru.delete');
    Route::get('/guru/create', [AdminController::class, 'createGuru'])->name('admin.guru.create');
    Route::post('/guru/store', [AdminController::class, 'storeGuru'])->name('admin.guru.store');
    Route::post('/admin/guru/status/{id}', [AdminController::class, 'updateStatus'])->name('admin.guru.update-status');
    Route::post('/import', [AdminController::class, 'import'])->name('admin.guru.import');
    // admin.guru.import
    
    // Route untuk manajemen jam pelajaran 
    Route::get('/times', [AdminScheduleController::class, 'index'])->name('admin.times.index');
    Route::post('/times/store', [AdminScheduleController::class, 'store'])->name('admin.times.store');
    Route::post('/times/update/{id}', [AdminScheduleController::class, 'update'])->name('admin.times.update');
    Route::post('/times/delete/{id}', [AdminScheduleController::class, 'destroy'])->name('admin.times.delete');

    
    

    // Subjects
    Route::get('/subjects', [AdminSubjectController::class, 'subjectIndex'])->name('admin.subjects.index');
    Route::post('/subjects/store', [AdminSubjectController::class, 'subjectStore'])->name('admin.subjects.store');
    Route::post('/subjects/update/{id}', [AdminSubjectController::class, 'subjectUpdate'])->name('admin.subjects.update');
    Route::post('/subjects/delete/{id}', [AdminSubjectController::class, 'subjectDestroy'])->name('admin.subjects.delete');

    // Classrooms
    Route::get('/classrooms', [AdminSubjectController::class, 'classroomIndex'])->name('admin.classrooms.index');
    Route::post('/classrooms/store', [AdminSubjectController::class, 'classroomStore'])->name('admin.classrooms.store');
    Route::post('/classrooms/update/{id}', [AdminSubjectController::class, 'classroomUpdate'])->name('admin.classrooms.update');
    Route::post('/classrooms/delete/{id}', [AdminSubjectController::class, 'classroomDestroy'])->name('admin.classrooms.delete');

    // Rombels
    Route::get('/rombels', [AdminSubjectController::class, 'rombelsIndex'])->name('admin.rombels.index');
    Route::post('/rombels/store', [AdminSubjectController::class, 'rombelsStore'])->name('admin.rombels.store');
    Route::post('/rombels/update/{id}', [AdminSubjectController::class, 'rombelsUpdate'])->name('admin.rombels.update');
    Route::post('/rombels/delete/{id}', [AdminSubjectController::class, 'rombelsDestroy'])->name('admin.rombels.delete');
    Route::get('/rombels/{id}', [AdminSubjectController::class, 'show'])->name('admin.rombels.show');
    Route::post('/rombels/{id}/add-student', [AdminSubjectController::class, 'addStudent'])->name('admin.rombels.add-student');
    Route::post('/rombels/{id}/add-student', [AdminSubjectController::class, 'addStudent'])->name('admin.rombels.add-student');
    Route::delete('/rombels/{id}/remove-student/{student_id}', [AdminSubjectController::class, 'removeStudent'])->name('admin.rombels.remove-student');

    // Rombel Management (Batch)
    Route::post('/rombels/{id}/bulk-add', [AdminSubjectController::class, 'bulkAdd'])->name('admin.rombels.bulk-add');
    Route::delete('/rombels/{id}/bulk-remove', [AdminSubjectController::class, 'bulkRemove'])->name('admin.rombels.bulk-remove');
    Route::post('/rombels/{id}/bulk-move', [AdminSubjectController::class, 'bulkMove'])->name('admin.rombels.bulk-move');
    // Route untuk manajemen hari
    Route::get('/days', [AdminScheduleController::class, 'daysIndex'])->name('admin.days.index');
    
    // Manage Jadwal per Hari
    Route::get('/days/manage/{day_id}', [AdminScheduleController::class, 'manage'])->name('admin.days.manage');
    Route::post('/days/manage/store', [AdminScheduleController::class, 'manageStore'])->name('admin.schedules.store');
    Route::post('/days/manage/update/{id}', [AdminScheduleController::class, 'manageUpdate'])->name('admin.days.manage.update');
    Route::post('/days/manage/delete/{id}', [AdminScheduleController::class, 'manageDestroy'])->name('admin.days.manage.delete');

    //  Manajement Siswa
    Route::get('students', [AdminSiswaController::class, 'index'])->name('admin.students.index');
    Route::post('students', [AdminSiswaController::class, 'store'])->name('admin.students.store');
    Route::put('students/{id}', [AdminSiswaController::class, 'update'])->name('admin.students.update');
    Route::delete('students/{id}', [AdminSiswaController::class, 'destroy'])->name('admin.students.delete');

    // Academi Year
    Route::get('/academic-years', [AdminScheduleController::class, 'yearIndex'])->name('admin.academic-years.index');
    Route::post('/academic-years/store', [AdminScheduleController::class, 'yearStore'])->name('admin.academic-years.store');
    Route::post('/academic-years/update/{academicYear}', [AdminScheduleController::class, 'yearUpdate'])->name('admin.academic-years.update');
    Route::post('/academic-years/delete/{academicYear}', [AdminScheduleController::class, 'yearDestroy'])->name('admin.academic-years.destroy');

    // Edit Profile
    Route::get('/profile', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile/update', [AdminController::class, 'update'])->name('admin.profile.update');
    // Logut
    Route::post('/logout', [AutentifkasiController::class, 'logout'])->name('admin.logout');
});