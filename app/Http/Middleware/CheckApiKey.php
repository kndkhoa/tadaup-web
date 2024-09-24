<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiKey
{
    public function handle($request, Closure $next)
    {
        // Retrieve the API key from the request header
        $apiKey = $request->header('x-api-key');
        // Compare with the one stored in .env
        if ($apiKey !== env('API_KEY')) {
            // If the key doesn't match, return an error response
            return response()->json(['error' => 'Unauthorized: Invalid API Key'], 401);
        }

        // If the key matches, continue the request
        return $next($request);
    }
}
