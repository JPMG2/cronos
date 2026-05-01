<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureCompanyExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Company::exists()) {
            return redirect()
                ->route('empresa.datos')
                ->with('warning', 'Debe configurar la empresa antes de continuar !');
        }

        return $next($request);
    }
}
