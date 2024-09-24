<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiKeyOpen
{
    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');
        // Logic for handling 'open' API key or other verification
        if ($apiKey !== env('API_KEY_OPEN')) {
            return response()->json(['error' => 'Unauthorized - Invalid API Key'], 401);
        }

        return $next($request);
    }
}
