<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Product;
use App\Models\ProductAudit;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuditProductActivity
{
    /**
     * Satu pintu audit bisnis produk (middleware saja) supaya tidak dobel dari event/listener.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Snapshot sebelum handler: setelah purge produk sudah hilang dari DB.
        $productBefore = $this->snapshotProduct($request);

        $response = $next($request);

        if (! $this->shouldAudit($request, $response)) {
            return $response;
        }

        $event = $this->eventName($request);
        if ($event === null) {
            return $response;
        }

        $product = $this->resolveProduct($request, $event, $productBefore);
        if ($product === null) {
            return $response;
        }

        /** @var User|null $user */
        $user = $request->user();

        ProductAudit::create([
            'product_id' => $product['id'],
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'event' => $event,
            'payload' => $this->payload($product, $request, $event, $productBefore),
            'request_id' => $request->headers->get('X-Request-ID'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'path' => $request->path(),
        ]);

        return $response;
    }

    private function shouldAudit(Request $request, Response $response): bool
    {
        if (! $this->isProductPath($request)) {
            return false;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        return $response->isSuccessful() || $response->isRedirection();
    }

    private function isProductPath(Request $request): bool
    {
        return $request->is('products')
            || $request->is('products/*')
            || $request->is('api/v1/products')
            || $request->is('api/v1/products/*');
    }

    private function eventName(Request $request): ?string
    {
        // Aksi khusus dulu, baru create/update/delete generik.
        if ($request->isMethod('POST') && $request->is('api/v1/products/*/restock')) {
            return 'ProductRestocked';
        }

        if ($request->isMethod('POST') && $request->is('api/v1/products/*/restore')) {
            return 'ProductRestored';
        }

        if ($request->isMethod('DELETE') && $request->is('api/v1/products/*/purge')) {
            return 'ProductPurged';
        }

        if ($request->isMethod('POST') && ($request->is('products') || $request->is('api/v1/products'))) {
            return 'ProductCreated';
        }

        if (in_array($request->method(), ['PUT', 'PATCH'], true) && $this->isProductResourcePath($request)) {
            return 'ProductUpdated';
        }

        if ($request->isMethod('DELETE') && $this->isProductResourcePath($request)) {
            return 'ProductDeleted';
        }

        return null;
    }

    /** Path resource produk: /products/{id} atau /api/v1/products/{id} (bukan sub-aksi). */
    private function isProductResourcePath(Request $request): bool
    {
        return (bool) preg_match('#^(products|api/v1/products)/[^/]+$#', $request->path());
    }

    /** @return array<string, mixed>|null */
    private function snapshotProduct(Request $request): ?array
    {
        $routeProduct = $request->route('product');

        if ($routeProduct instanceof Product) {
            return $routeProduct->toArray();
        }

        if (is_scalar($routeProduct)) {
            $found = Product::withTrashed()->whereKey((string) $routeProduct)->first();

            return $found?->toArray();
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $productBefore
     * @return array<string, mixed>|null
     */
    private function resolveProduct(Request $request, string $event, ?array $productBefore): ?array
    {
        if ($event === 'ProductCreated') {
            $sku = $request->input('sku');
            if (is_string($sku) && $sku !== '') {
                $created = Product::query()->where('sku', $sku)->latest()->first();

                return $created?->toArray();
            }

            return null;
        }

        if ($event === 'ProductPurged') {
            return $productBefore;
        }

        $routeProduct = $request->route('product');

        if ($routeProduct instanceof Product) {
            $fresh = Product::withTrashed()->whereKey($routeProduct->getKey())->first();

            return $fresh?->toArray() ?? $routeProduct->toArray();
        }

        if (is_scalar($routeProduct)) {
            $found = Product::withTrashed()->whereKey((string) $routeProduct)->first();

            return $found?->toArray() ?? $productBefore;
        }

        return $productBefore;
    }

    /**
     * @param  array<string, mixed>  $product
     * @param  array<string, mixed>|null  $productBefore
     * @return array<string, mixed>
     */
    private function payload(array $product, Request $request, string $event, ?array $productBefore): array
    {
        if ($event === 'ProductRestocked') {
            $quantity = (int) $request->input('quantity', 0);
            $oldStock = isset($productBefore['stock']) ? (int) $productBefore['stock'] : null;
            $newStock = isset($product['stock']) ? (int) $product['stock'] : null;

            return array_merge($product, [
                'old_stock' => $oldStock,
                'added_quantity' => $quantity,
                'new_stock' => $newStock,
                'reason' => $request->input('reason'),
            ]);
        }

        return $product;
    }
}
