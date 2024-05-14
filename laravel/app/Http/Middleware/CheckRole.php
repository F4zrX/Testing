<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah pengguna sedang login
        if (Auth::check()) {
            // Ambil peran pengguna
            $userRole = Auth::user()->role->name;

            // Cek apakah peran pengguna termasuk dalam daftar peran yang diizinkan
            if (in_array($userRole, $roles)) {
                // Jika sesuai, lanjutkan ke permintaan berikutnya
                return $next($request);
            }
        }

        // Jika tidak sesuai, kembalikan pengguna ke halaman terakhir atau halaman login
        return redirect()->route('login')->with('error', 'You do not have permission to access this page.');
    }
}
