<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;

final class EnsureBranchExists
{
    /**
     * Handle the incoming prompt.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! Branch::exists()) {
            return redirect()
                ->route('empresa.sucursales')
                ->with('warning', 'Debe configurar al menos una sucursal antes de continuar.');
        }

        return $next($request);
    }
}
