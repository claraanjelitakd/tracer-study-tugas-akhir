<?php

namespace App\Http\Controllers\Otentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * LoginController
 * 
 * Fungsi: Menangani proses otentikasi (masuk dan keluar) pengguna ke dalam sistem.
 * Tujuan: Memastikan hanya pengguna yang valid yang dapat mengakses halaman yang dilindungi (dashboard).
 */
class LoginController extends Controller
{
    /**
     * Menampilkan Halaman Login
     */
    public function tampilkanHalamanLogin()
    {
        return Inertia::render('Otentikasi/Login');
    }

    /**
     * Proses Login Pengguna
     */
    public function prosesLogin(Request $request)
    {
        $kredensial = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($kredensial)) {
            $request->session()->regenerate();
            $pengguna = Auth::user();

            if ($pengguna->must_change_password && $pengguna->role !== 'admin_biro3') {
                return redirect()->intended('/change-password');
            }

            return self::arahkanBerdasarkanPeran($pengguna->role);
        }

        return back()->withErrors([
            'username' => 'Username atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('username');
    }

    /**
     * Proses Logout Pengguna
     */
    public function prosesLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Fungsi Bantuan: Mengarahkan pengguna berdasarkan role mereka.
     */
    public static function arahkanBerdasarkanPeran($peran)
    {
        switch ($peran) {
            case 'superadmin':
                return redirect()->intended('/superadmin/dashboard');
            case 'admin_biro3':
                return redirect()->intended('/biro3/dashboard');
            case 'admin_prodi':
                return redirect()->intended('/prodi/dashboard');
            case 'alumni':
                return redirect()->intended('/alumni/dashboard');
            default:
                return redirect('/');
        }
    }
}

