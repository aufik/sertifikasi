<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class MonitorApiPerformance
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);
        $response = $next($request);
        $durationMs = round((microtime(true) - $startedAt) * 1000, 2);
        $response->headers->set('X-Response-Time-Ms', (string) $durationMs);

        $context = [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'request_id' => $request->headers->get('X-Request-ID'),
        ];

        if ($durationMs >= (float) config('monitoring.slow_request_threshold_ms', 500)) {
            Log::channel('product_log')->warning('Slow API request detected.', $context);
        }

        return $response;
    }
}
