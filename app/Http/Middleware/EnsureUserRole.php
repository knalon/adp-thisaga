<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            if ($request->user() && $request->user()->hasRole('admin')) {
                return redirect('/admin');
            }

            if ($request->user() && $request->user()->hasRole('user')) {
                return redirect('/user');
            }

            return redirect('/login');
        }

        return $next($request);
    }
}
