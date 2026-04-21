<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isBanned()) {
                $ban = $user->activeBan;

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('banned', [
                    'reason'      => $ban->reason,
                    'type'        => $ban->type,
                    'banned_until' => $ban->banned_until,
                    'remaining'   => $ban->getRemainingTime(),
                ]);
            }
        }

        return $next($request);
    }
}