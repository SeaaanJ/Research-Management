<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!auth()->check()) {
        abort(403, 'Unauthorized');
    }

    $userRole = auth()->user()->role;

    // "God Mode": If they are super_admin, just let them through regardless of the $roles array
    if ($userRole === 'super_admin') {
        return $next($request);
    }

    if (!in_array($userRole, $roles)) {
        abort(403, 'You do not have permission to access this page.');
    }

    return $next($request);
}
}