<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userRole = strtolower($user->role ?? '');

        // Normalisasi list roles yang diminta
        $allowedRoles = array_map('strtolower', $roles);

        // Jika user adalah pikol / admin, atau role user terdaftar di allowedRoles
        if (in_array($userRole, ['pikol', 'admin']) || in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak - Anda tidak memiliki izin untuk mengakses halaman ini (Khusus PIKOL).');
    }
}