<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses rute berdasarkan peran pengguna.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        $allowed = array_filter(array_map(
            fn (string $role): ?UserRole => UserRole::tryFrom($role),
            $roles,
        ));

        if ($allowed === []) {
            abort(Response::HTTP_FORBIDDEN, 'Konfigurasi peran rute tidak valid.');
        }

        if ($user->hasAnyRole($allowed)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(Response::HTTP_FORBIDDEN, 'Anda tidak memiliki akses ke sumber daya ini.');
        }

        return redirect()
            ->route($user->userRole()->dashboardRoute())
            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
