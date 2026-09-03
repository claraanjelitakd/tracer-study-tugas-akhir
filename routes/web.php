<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;

// Landing Page
Route::get('/', function () {
    return Inertia::render('landing');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Change password routes (diizinkan saat must_change_password = true)
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Protected dashboard routes
    // Harus sudah mengganti password jika disyaratkan
    Route::middleware('must_change_password')->group(function () {
        
        // Dashboard Alumni
        Route::middleware('role:alumni')->group(function () {
            Route::get('/alumni/dashboard', function () {
                $alumni = \App\Models\Alumni::where('user_id', auth()->id())->first();
                $responsesCount = $alumni ? \App\Models\Response::where('alumni_id', $alumni->id)->count() : 0;
                return Inertia::render('alumni/dashboard', [
                    'alumni' => $alumni,
                    'responsesCount' => $responsesCount,
                ]);
            });
            Route::get('/alumni/kuesioner', [\App\Http\Controllers\QuestionnaireController::class, 'index']);
            Route::post('/alumni/kuesioner', [\App\Http\Controllers\QuestionnaireController::class, 'saveSection']);
            
            // Profile & Biodata Alumni
            Route::get('/alumni/profile', [\App\Http\Controllers\AlumniProfileController::class, 'edit'])->name('alumni.profile');
            Route::post('/alumni/profile', [\App\Http\Controllers\AlumniProfileController::class, 'update']);
        });
        
        // Dashboard Admin Biro 3
        Route::middleware('role:admin_biro3')->get('/biro3/dashboard', function () {
            return Inertia::render('biro3/dashboard');
        });
        
        // Dashboard Admin Prodi
        Route::middleware('role:admin_prodi')->get('/prodi/dashboard', function () {
            return Inertia::render('prodi/dashboard');
        });
        
        // Dashboard Superadmin
        Route::middleware('role:superadmin')->get('/superadmin/dashboard', function () {
            return Inertia::render('superadmin/dashboard');
        });
        
    });
});
