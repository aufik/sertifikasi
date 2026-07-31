<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class EloquentBaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected readonly Model $model) {}

    public function findOrFail(string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }

    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    public function update(string $id, array $attributes): Model
    {
        $model = $this->findOrFail($id);
        $model->update($attributes);

        return $model->refresh();
    }

    public function delete(string $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
