<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function findOrFail(string $id): Model;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function create(array $attributes): Model;

    public function update(string $id, array $attributes): Model;

    public function delete(string $id): bool;
}
