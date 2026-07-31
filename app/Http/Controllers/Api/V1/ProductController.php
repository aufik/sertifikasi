<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\ProductFilterDTO;
use App\Http\Requests\RestockProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProductController extends BaseController
{
    public function __construct(private readonly ProductService $service) {}

    /** Ambil daftar produk aktif untuk katalog dan halaman operasi, lengkap dengan pencarian dan pagination. */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);
        $paginator = $this->service->list(ProductFilterDTO::fromArray($request->query()));
        $items = $paginator->getCollection()->map(fn ($p) => ProductResource::make($p)->resolve())->values()->all();

        return $this->paginatedItemsResponse($paginator, $items);
    }

    /** Ambil daftar produk yang sudah masuk trash supaya admin bisa restore atau hapus permanen. */
    public function trashed(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));
        $search = $request->query('search');

        $paginator = Product::onlyTrashed()
            ->when(is_string($search) && $search !== '', fn ($query) => $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")))
            ->latest('deleted_at')
            ->paginate($perPage);

        $items = $paginator->getCollection()->map(fn ($p) => ProductResource::make($p)->resolve())->values()->all();

        return $this->paginatedItemsResponse($paginator, $items);
    }

    /** Simpan produk baru setelah data lolos validasi dan role user diizinkan. */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);
        $product = $this->service->create($request->toDto());

        return $this->successResponse(ProductResource::make($product)->resolve(), 'Product created', 20100, 201);
    }

    /** Tampilkan detail satu produk dalam format JSON standar. */
    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->successResponse(ProductResource::make($product)->resolve());
    }

    /** Ubah data produk, lalu serahkan proses aman transaksinya ke service. */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        return $this->successResponse(ProductResource::make($this->service->update($product->id, $request->toDto()))->resolve(), 'Product updated');
    }

    /** Hapus produk secara soft delete dan catat aktivitasnya sebagai log bisnis. */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);
        $this->service->delete($product->id);

        return $this->successResponse(null, 'Product deleted');
    }

    /** Tambah stok produk dari menu operasi dan catat event ProductRestocked. */
    public function restock(RestockProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('restock', $product);

        return $this->successResponse(ProductResource::make($this->service->restock($product->id, $request->toDto()))->resolve(), 'Product restocked');
    }

    /** Pulihkan produk dari trash dan catat event ProductRestored di audit bisnis. */
    public function restore(string $product): JsonResponse
    {
        $model = Product::withTrashed()->findOrFail($product);
        $this->authorize('restore', $model);

        return $this->successResponse(ProductResource::make($this->service->restore($product))->resolve(), 'Product restored');
    }

    /** Hapus permanen produk dari trash setelah event ProductPurged dicatat. */
    public function purge(string $product): JsonResponse
    {
        $model = Product::withTrashed()->findOrFail($product);
        $this->authorize('forceDelete', $model);
        $this->service->purge($product);

        return $this->successResponse(null, 'Product purged');
    }
}
