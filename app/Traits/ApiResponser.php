<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

trait ApiResponser
{
    protected function successResponse(mixed $data = null, string $message = 'Operation successful', int $code = 20000, int $status = 200): JsonResponse
    {
        return response()->json(['meta' => $this->meta(true, $code, $message), 'data' => $data], $status);
    }

    protected function paginatedResponse(LengthAwarePaginator $paginator, string $message = 'Operation successful'): JsonResponse
    {
        return $this->paginatedItemsResponse($paginator, $paginator->items(), $message);
    }

    /** @param array<int, mixed> $items */
    protected function paginatedItemsResponse(LengthAwarePaginator $paginator, array $items, string $message = 'Operation successful'): JsonResponse
    {
        $meta = $this->meta(true, 20000, $message);
        $meta['pagination'] = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ];

        return response()->json(['meta' => $meta, 'data' => $items]);
    }

    protected function errorResponse(string $message, int $code, int $status, array $errors = []): JsonResponse
    {
        return response()->json(['meta' => $this->meta(false, $code, $message), 'errors' => $errors], $status);
    }

    private function meta(bool $success, int $code, string $message): array
    {
        return ['success' => $success, 'code' => $code, 'message' => $message, 'request_id' => request()->headers->get('X-Request-ID'), 'timestamp' => Carbon::now('Asia/Jakarta')->toIso8601String()];
    }
}
