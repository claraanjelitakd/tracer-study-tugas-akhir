<?php

use Illuminate\Support\Facades\Route;

// =========================================================================
// Rute Tamu (Guest)
// =========================================================================
Route::get('/', [\App\Http\Controllers\Tamu\BerandaController::class, 'tampilkanBeranda']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Otentikasi\LoginController::class, 'tampilkanHalamanLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Otentikasi\LoginController::class, 'prosesLogin']);
});

// =========================================================================
// Rute Terotentikasi (Authenticated)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Otentikasi\LoginController::class, 'prosesLogout'])->name('logout');
    
    // Ganti kata sandi wajib
    Route::get('/change-password', [\App\Http\Controllers\Otentikasi\UbahKataSandiController::class, 'tampilkanUbahKataSandi'])->name('change-password');
    Route::post('/change-password', [\App\Http\Controllers\Otentikasi\UbahKataSandiController::class, 'prosesUbahKataSandi']);

    // Rute yang mengharuskan password bawaan sudah diganti
    Route::middleware('must_change_password')->group(function () {
        
        // -----------------------------------------------------------------
        // Rute Alumni
        // -----------------------------------------------------------------
        Route::middleware('role:alumni')->group(function () {
            // Dashboard
            Route::get('/alumni/dashboard', [\App\Http\Controllers\Alumni\Dashboard\DashboardController::class, 'tampilkanDashboard']);
            
            // Profil
            Route::get('/alumni/profile', [\App\Http\Controllers\Alumni\Profil\ProfilController::class, 'tampilkanHalamanProfil'])->name('alumni.profile');
            Route::post('/alumni/profile', [\App\Http\Controllers\Alumni\Profil\SimpanProfilController::class, 'simpanPerubahanProfil']);
            
            // Kuesioner
            Route::get('/alumni/kuesioner', [\App\Http\Controllers\Alumni\Kuesioner\KuesionerController::class, 'tampilkanKuesioner']);
            Route::post('/alumni/kuesioner', [\App\Http\Controllers\Alumni\Kuesioner\SimpanJawabanController::class, 'simpanJawabanKuesioner']);
        });
        
        // -----------------------------------------------------------------
        // Rute Admin Biro 3
        // -----------------------------------------------------------------
        Route::middleware('role:admin_biro3')->group(function () {
            // Dashboard Utama Biro 3
            Route::get('/biro3/dashboard', [\App\Http\Controllers\AdminBiroTiga\Dashboard\DashboardController::class, 'tampilkanDashboard'])->name('biro3.dashboard');

            // Kelola Alumni & Sinkronisasi
            Route::get('/biro3/alumni', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DaftarAlumniController::class, 'tampilkanDaftarAlumni'])->name('biro3.alumni.index');
            Route::get('/biro3/alumni/{id}', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DetailAlumniController::class, 'tampilkanDetailAlumni'])->name('biro3.alumni.show');
            Route::post('/biro3/alumni/{id}/sync-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'sinkronisasiDataLinkedin'])->name('biro3.alumni.sync');
            Route::post('/biro3/alumni/{id}/save-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'simpanDataLinkedin'])->name('biro3.alumni.save');

            // Kelola Pertanyaan & Opsi Kuesioner
            Route::get('/biro3/pertanyaan', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'index'])->name('biro3.pertanyaan.index');
            Route::post('/biro3/pertanyaan', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'store'])->name('biro3.pertanyaan.store');
            Route::post('/biro3/pertanyaan/reorder', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'reorder'])->name('biro3.pertanyaan.reorder');
            Route::put('/biro3/pertanyaan/{id}', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'update'])->name('biro3.pertanyaan.update');
            Route::delete('/biro3/pertanyaan/{id}', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'destroy'])->name('biro3.pertanyaan.destroy');
            Route::post('/biro3/pertanyaan/{questionId}/options', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'storeOption'])->name('biro3.pertanyaan.options.store');
            Route::put('/biro3/pertanyaan/options/{optionId}', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'updateOption'])->name('biro3.pertanyaan.options.update');
            Route::delete('/biro3/pertanyaan/options/{optionId}', [\App\Http\Controllers\AdminBiroTiga\KelolaPertanyaan\KelolaPertanyaanController::class, 'destroyOption'])->name('biro3.pertanyaan.options.destroy');
        });
        
        // -----------------------------------------------------------------
        // Rute Admin Prodi
        // -----------------------------------------------------------------
        Route::middleware('role:admin_prodi')->group(function () {
            Route::get('/prodi/dashboard', [\App\Http\Controllers\AdminProdi\Dashboard\DashboardController::class, 'tampilkanDashboard']);
        });
        
        // -----------------------------------------------------------------
        // Rute Superadmin
        // -----------------------------------------------------------------
        Route::middleware('role:superadmin')->group(function () {
            Route::get('/superadmin/dashboard', [\App\Http\Controllers\SuperAdmin\Dashboard\DashboardController::class, 'tampilkanDashboard']);
        });
        
    });
});
