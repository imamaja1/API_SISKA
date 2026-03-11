<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogToJson
{
    public function handle(Request $request, Closure $next, string $source = 'api'): Response
    {
        $response = $next($request);
        if (strtoupper($request->method()) === 'GET') {
            return $response;
        }

        $entry = json_encode([
            'timestamp' => now()->toIso8601String(),
            'source' => $source,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => optional($request->user())->getKey(),
            'body' => $request->except(['password', 'password_confirmation', 'token', 'secret']),
            'date' => now()->toDateString(),
            'status' => $response->getStatusCode(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $filename = 'logs/json/'.$source.'-'.now()->format('Y-m').'.json';

        $dir = storage_path('logs/json');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(storage_path($filename), $entry.PHP_EOL, FILE_APPEND | LOCK_EX);

        return $response;
    }
}
