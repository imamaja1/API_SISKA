<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSimortuaRequest
{
    /**
     * Log semua request ke storage/logs/api_simortua-YYYY-MM-DD.log
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $body = $request->except(['password', 'password_confirmation', 'token', 'secret']);

        Log::channel('api_simortua')->info('Simortua Request', [
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user'       => optional($request->user())->getKey(),
            'body'       => $body,
            'status'     => $response->getStatusCode(),
        ]);

        return $response;
    }
}
