<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

if (! function_exists('api_error')) {
    function api_error(string $message, int $code, int $status, array $errors = []): JsonResponse
    {
        return response()->json([
            'meta' => [
                'success' => false,
                'code' => $code,
                'message' => $message,
                'request_id' => request()->headers->get('X-Request-ID'),
                'timestamp' => Carbon::now('Asia/Jakarta')->toIso8601String(),
            ],
            'errors' => $errors,
        ], $status);
    }
}
