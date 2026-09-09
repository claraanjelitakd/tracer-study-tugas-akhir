<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Memeriksa apakah role pengguna saat ini sesuai dengan yang diizinkan untuk rute tersebut.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  mixed  ...$roles  Daftar role yang diizinkan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login sebelum mengecek role
        if (! Auth::check()) {
            return redirect('/login');
        }

        // Ambil data user saat ini
        $user = Auth::user();

        // Cek apakah role user ada di dalam daftar role yang diizinkan untuk rute ini
        if (! in_array($user->role, $roles)) {
            // Jika tidak memiliki akses (role tidak cocok), tampilkan halaman error 403 (Forbidden)
            return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Jika lolos pengecekan, lanjutkan request ke rute tujuan
        return $next($request);
    }
}
