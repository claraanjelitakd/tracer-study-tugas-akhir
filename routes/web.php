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
// Rute Otentikasi Sesi (Logout)
// =========================================================================
// Rute logout mendukung method POST & GET agar selalu berhasil mengarahkan pengguna kembali ke Beranda (Home)
Route::match(['get', 'post'], '/logout', [\App\Http\Controllers\Otentikasi\LoginController::class, 'prosesLogout'])->name('logout');

// =========================================================================
// Rute Terotentikasi (Authenticated)
// =========================================================================
Route::middleware('auth')->group(function () {
    
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
            Route::post('/alumni/company', [\App\Http\Controllers\Alumni\Profil\SimpanProfilController::class, 'tambahPerusahaanBaru'])->name('alumni.company.store');
            
            // Kuesioner
            Route::get('/alumni/kuesioner', [\App\Http\Controllers\Alumni\Kuesioner\KuesionerController::class, 'tampilkanKuesioner']);
            Route::post('/alumni/kuesioner', [\App\Http\Controllers\Alumni\Kuesioner\SimpanJawabanController::class, 'simpanJawabanKuesioner']);
        });
        
        // -----------------------------------------------------------------
        // Rute Admin Biro 3 (Biro Kemahasiswaan, Alumni & Pengembangan Karir)
        // -----------------------------------------------------------------
        Route::middleware('role:admin_biro3')->group(function () {
            // Dashboard Utama Biro 3
            Route::get('/biro3/dashboard', [\App\Http\Controllers\AdminBiroTiga\Dashboard\DashboardController::class, 'tampilkanDashboard'])->name('biro3.dashboard');

            // Kelola Data Alumni, Verifikasi, & Sinkronisasi Profil LinkedIn
            Route::get('/biro3/alumni', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DaftarAlumniController::class, 'tampilkanDaftarAlumni'])->name('biro3.alumni.index');
            Route::get('/biro3/alumni/{id}', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\DetailAlumniController::class, 'tampilkanDetailAlumni'])->name('biro3.alumni.show');
            Route::post('/biro3/alumni/{id}/sync-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'sinkronisasiDataLinkedin'])->name('biro3.alumni.sync');
            Route::post('/biro3/alumni/{id}/save-linkedin', [\App\Http\Controllers\AdminBiroTiga\KelolaAlumni\SinkronisasiLinkedinController::class, 'simpanDataLinkedin'])->name('biro3.alumni.save');
        });
        
        // -----------------------------------------------------------------
        // Rute Admin Prodi (Program Studi)
        // -----------------------------------------------------------------
        Route::middleware('role:admin_prodi')->group(function () {
            // Dashboard Utama Program Studi
            Route::get('/prodi/dashboard', [\App\Http\Controllers\AdminProdi\Dashboard\DashboardController::class, 'tampilkanDashboard'])->name('prodi.dashboard');
        });
        
        // -----------------------------------------------------------------
        // Rute Superadmin (Otoritas Tertinggi & Pengaturan Instrumen Kuesioner)
        // -----------------------------------------------------------------
        Route::middleware('role:superadmin')->group(function () {
            // Dashboard Utama Superadmin
            Route::get('/superadmin/dashboard', [\App\Http\Controllers\SuperAdmin\Dashboard\DashboardController::class, 'tampilkanDashboard'])->name('superadmin.dashboard');

            // Kelola Butir Pertanyaan, Opsi Jawaban, & Alur Branching Kuesioner
            Route::get('/superadmin/pertanyaan', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'index'])->name('superadmin.pertanyaan.index');
            Route::post('/superadmin/pertanyaan', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'store'])->name('superadmin.pertanyaan.store');
            Route::post('/superadmin/pertanyaan/reorder', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'reorder'])->name('superadmin.pertanyaan.reorder');
            Route::put('/superadmin/pertanyaan/{id}', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'update'])->name('superadmin.pertanyaan.update');
            Route::delete('/superadmin/pertanyaan/{id}', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'destroy'])->name('superadmin.pertanyaan.destroy');
            Route::post('/superadmin/pertanyaan/{questionId}/options', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'storeOption'])->name('superadmin.pertanyaan.options.store');
            Route::put('/superadmin/pertanyaan/options/{optionId}', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'updateOption'])->name('superadmin.pertanyaan.options.update');
            Route::delete('/superadmin/pertanyaan/options/{optionId}', [\App\Http\Controllers\SuperAdmin\KelolaPertanyaan\KelolaPertanyaanController::class, 'destroyOption'])->name('superadmin.pertanyaan.options.destroy');
        });
        
    });
});
