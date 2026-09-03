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
            // API Tambahan
            Route::get('/api/companies', [\App\Http\Controllers\Api\CompanyController::class, 'search'])->name('api.companies.search');
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
            // Kelola Alumni & Sinkronisasi
            Route::get('/biro3/alumni', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DaftarAlumniController::class, 'tampilkanDaftarAlumni'])->name('biro3.alumni.index');
            Route::get('/biro3/alumni/{id}', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DetailAlumniController::class, 'tampilkanDetailAlumni'])->name('biro3.alumni.show');
            Route::post('/biro3/alumni/{id}/sync-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'sinkronisasiDataLinkedin'])->name('biro3.alumni.sync');
            Route::post('/biro3/alumni/{id}/save-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'simpanDataLinkedin'])->name('biro3.alumni.save');
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
