<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreateProductDTO;
use App\Enums\ProductStatus;
use PHPUnit\Framework\TestCase;

final class ProductDtoTest extends TestCase
{
    public function test_create_product_dto_serializes_to_array(): void
    {
        $dto = new CreateProductDTO('cat', 'Name', 'SKU', 'Desc', 1000, 5, ProductStatus::Active);
        $this->assertSame('active', $dto->toArray()['status']);
        $this->assertSame(1000, $dto->toArray()['price']);
    }
}
