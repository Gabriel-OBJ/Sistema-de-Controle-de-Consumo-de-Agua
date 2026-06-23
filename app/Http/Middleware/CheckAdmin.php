<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Verifica se o usuário autenticado possui o perfil de administrador.
     *
     * - admin     → acesso liberado
     * - leiturista → retorna 403 Forbidden
     * - não autenticado → retorna 403 Forbidden
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403, 'Acesso restrito a administradores.');
        }

        return $next($request);
    }
}
