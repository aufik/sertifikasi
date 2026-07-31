<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\DTO\ProductFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ProductPageController extends Controller
{
    public function __construct(private readonly ProductService $service) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);
        $paginator = $this->service->list(ProductFilterDTO::fromArray($request->query()));

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($paginator->items())->resolve(),
            'pagination' => ['current_page' => $paginator->currentPage(), 'per_page' => $paginator->perPage(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total(), 'has_more' => $paginator->hasMorePages()],
            'filters' => $request->only(['search', 'status', 'category_id', 'sort']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Products/Create', ['categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug', 'type'])]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);
        $this->service->create($request->toDto());

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        return Inertia::render('Products/Show', ['product' => ProductResource::make($product)->resolve()]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Products/Edit', ['product' => ProductResource::make($product)->resolve(), 'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug', 'type'])]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);
        $this->service->update($product->id, $request->toDto());

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $this->service->delete($product->id);

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
