<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ProductStatus;

final readonly class UpdateProductDTO
{
    public function __construct(public ?string $categoryId, public ?string $name, public ?string $sku, public ?string $description, public ?int $price, public ?int $stock, public ?ProductStatus $status) {}

    public function toArray(): array
    {
        return array_filter(['category_id' => $this->categoryId, 'name' => $this->name, 'sku' => $this->sku, 'description' => $this->description, 'price' => $this->price, 'stock' => $this->stock, 'status' => $this->status?->value], fn ($v) => $v !== null);
    }
}
