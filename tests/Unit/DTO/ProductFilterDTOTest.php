<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use App\DTO\ProductFilterDTO;
use PHPUnit\Framework\TestCase;

final class ProductFilterDTOTest extends TestCase
{
    public function test_from_array_caps_per_page_and_sets_sort(): void
    {
        $dto = ProductFilterDTO::fromArray(['search' => 'laptop', 'per_page' => 500, 'sort' => 'price_desc']);

        $this->assertSame('laptop', $dto->search);
        $this->assertSame(100, $dto->perPage);
        $this->assertSame('price_desc', $dto->sort);
    }
}
