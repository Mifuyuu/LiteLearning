<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if ($role === 'not_admin') {
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return $next($request);
        }

        if ($role === 'teacher') {
            if (! $user || (! $user->isTeacher() && ! $user->isAdmin())) {
                abort(403);
            }

            return $next($request);
        }

        if (! $user || ! $user->{'is'.ucfirst($role)}()) {
            abort(403);
        }

        return $next($request);
    }
}
