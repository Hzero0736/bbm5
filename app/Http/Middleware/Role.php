<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Role
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || !$request->user()->roles->contains('nama', $role)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
