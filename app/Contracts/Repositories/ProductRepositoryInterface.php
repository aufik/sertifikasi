<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\DTO\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function search(ProductFilterDTO $filters): LengthAwarePaginator;

    public function create(array $attributes): Product;

    public function findWithLock(string $id): Product;
}
