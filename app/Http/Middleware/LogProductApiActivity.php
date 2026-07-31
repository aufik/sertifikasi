<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class LogProductApiActivity
{
    /**
     * Middleware ini mencatat aktivitas teknis API produk secara otomatis.
     * Jadi service/controller tidak perlu memanggil Log::channel(...) manual untuk log teknis biasa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->is('api/v1/products*')) {
            return $response;
        }

        Log::channel('product_log')->info($this->message($request), [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'product_id' => $request->route('product'),
            'search' => $request->query('search'),
            'status_filter' => $request->query('status'),
            'category_id' => $request->query('category_id'),
            'per_page' => $request->query('per_page'),
            'request_id' => $request->headers->get('X-Request-ID'),
            'user_id' => $request->user()?->id,
            'user_email' => $request->user()?->email,
        ]);

        return $response;
    }

    /** Tentukan pesan log yang mudah dibaca berdasarkan method dan pola URL API. */
    private function message(Request $request): string
    {
        if ($request->isMethod('GET') && $request->is('api/v1/products/trash')) {
            return 'Product trash list accessed.';
        }

        if ($request->isMethod('GET') && $request->is('api/v1/products')) {
            return 'Product search/list accessed.';
        }

        if ($request->isMethod('POST') && $request->is('api/v1/products/*/restock')) {
            return 'Product restock API executed.';
        }

        if ($request->isMethod('POST') && $request->is('api/v1/products/*/restore')) {
            return 'Product restore API executed.';
        }

        if ($request->isMethod('DELETE') && $request->is('api/v1/products/*/purge')) {
            return 'Product purge API executed.';
        }

        if ($request->isMethod('POST') && $request->is('api/v1/products')) {
            return 'Product create API executed.';
        }

        if (in_array($request->method(), ['PUT', 'PATCH'], true) && $request->is('api/v1/products/*')) {
            return 'Product update API executed.';
        }

        if ($request->isMethod('DELETE') && $request->is('api/v1/products/*')) {
            return 'Product delete API executed.';
        }

        return 'Product API activity executed.';
    }
}
