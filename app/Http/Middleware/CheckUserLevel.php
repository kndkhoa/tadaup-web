<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserLevel
{
    public function handle(Request $request, Closure $next, ...$levels)
    {
        if (!auth()->check() || !auth()->user()->hasLevel($levels)) {
            // If user is not logged in or doesn't have the required level
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
