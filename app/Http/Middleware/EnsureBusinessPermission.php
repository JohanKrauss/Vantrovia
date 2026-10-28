<?php

namespace App\Http\Middleware;

use App\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessPermission
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Platform Admin
        |--------------------------------------------------------------------------
        |
        | Un administrador de Vantrovia puede acceder a cualquier negocio.
        |
        */

        if ($user->isPlatformAdmin()) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener negocio
        |--------------------------------------------------------------------------
        */

        $business = $request->route('business');

        if (! $business) {
            abort(
                404,
                'No se pudo determinar el negocio.'
            );
        }

        if (! $business instanceof Business) {
            $business = Business::findOrFail(
                $business
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar permiso
        |--------------------------------------------------------------------------
        */

        if (
            ! $user->hasBusinessPermission(
                $business,
                $permission
            )
        ) {
            abort(
                403,
                'No tienes permiso para realizar esta acción.'
            );
        }

        return $next($request);
    }
}
