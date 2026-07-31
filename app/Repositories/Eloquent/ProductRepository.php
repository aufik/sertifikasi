<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\ProductRepositoryInterface;
use App\DTO\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class ProductRepository extends EloquentBaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function search(ProductFilterDTO $filters): LengthAwarePaginator
    {
        $query = $this->query()->with('category')
            ->when($filters->search, fn (Builder $q, string $s) => $q->where(fn (Builder $x) => $x->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")))
            ->when($filters->status, fn (Builder $q, string $s) => $q->where('status', $s))
            ->when($filters->categoryId, fn (Builder $q, string $id) => $q->where('category_id', $id));

        $this->applySort($query, $filters->sort);

        return $query->paginate($filters->perPage);
    }

    public function create(array $attributes): Product
    {
        /** @var Product $product */
        $product = $this->query()->create($attributes);

        return $product;
    }

    public function findWithLock(string $id): Product
    {
        /** @var Product $product */
        $product = $this->query()->whereKey($id)->lockForUpdate()->firstOrFail();

        return $product;
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'stock_asc' => $query->orderBy('stock'),
            'stock_desc' => $query->orderByDesc('stock'),
            default => $query->latest(),
        };
    }
}
