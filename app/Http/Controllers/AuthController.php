<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLogin()
    {
        return Inertia::render('auth/login');
    }

    /**
     * Proses otentikasi user.
     */
    public function login(Request $request)
    {
        // Validasi input dari user
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Coba melakukan autentikasi dengan kredensial yang diberikan
        if (Auth::attempt($credentials)) {
            // Cegah serangan session fixation dengan generate session ID baru
            $request->session()->regenerate();
            
            // Ambil data user yang sedang login
            $user = Auth::user();

            // Pengecekan apakah user diwajibkan mengganti password
            // Biasanya ini berlaku untuk alumni yang baru pertama kali login
            if ($user->must_change_password) {
                // Arahkan ke halaman ganti password secara paksa
                return redirect()->intended('/change-password');
            }

            // Jika tidak perlu ganti password, arahkan ke dashboard sesuai rolenya
            return $this->redirectByRole($user->role);
        }

        // Jika autentikasi gagal, kembalikan ke form login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('username');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        // Hapus sesi autentikasi user saat ini
        Auth::logout();
        
        // Hancurkan data session yang ada
        $request->session()->invalidate();
        
        // Regenerasi token CSRF untuk keamanan form
        $request->session()->regenerateToken();

        // Arahkan kembali ke landing page
        return redirect('/');
    }

    /**
     * Menampilkan form ubah password wajib.
     */
    public function showChangePassword()
    {
        // Hanya bisa diakses jika user sudah login dan must_change_password bernilai true
        if (!Auth::user()->must_change_password) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return Inertia::render('auth/change-password');
    }

    /**
     * Proses update password.
     */
    public function changePassword(Request $request)
    {
        // Ambil data user yang sedang login
        $user = Auth::user();

        // Keamanan tambahan: Jika user mengakses form ini tapi must_change_password false, tolak dan kembalikan ke dashboard
        if (!$user->must_change_password) {
            return $this->redirectByRole($user->role);
        }

        // Validasi input password baru (minimal 8 karakter dan harus dikonfirmasi)
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Simpan password baru ke database dengan di-hash
        // Set must_change_password menjadi false agar user tidak diminta ganti password lagi
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        // Setelah berhasil, arahkan ke dashboard yang sesuai
        return $this->redirectByRole($user->role);
    }

    /**
     * Helper untuk mengarahkan pengguna berdasarkan role mereka.
     */
    private function redirectByRole($role)
    {
        switch ($role) {
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
