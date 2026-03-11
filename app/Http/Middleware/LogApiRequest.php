<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    /**
     * Log semua request non-GET ke storage/logs/api_requests-YYYY-MM-DD.log
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Ambil body request, sensor field sensitif
        $body = $request->except(['password', 'password_confirmation', 'token', 'secret']);

        Log::channel('api_requests')->info('API Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user' => optional($request->user())->getKey(),
            'body' => $body,
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
