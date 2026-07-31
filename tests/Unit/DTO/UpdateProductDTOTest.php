<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use App\DTO\UpdateProductDTO;
use App\Enums\ProductStatus;
use PHPUnit\Framework\TestCase;

final class UpdateProductDTOTest extends TestCase
{
    public function test_to_array_excludes_null_values(): void
    {
        $dto = new UpdateProductDTO(null, 'Updated', null, null, 2500, null, ProductStatus::Archived);

        $this->assertSame(['name' => 'Updated', 'price' => 2500, 'status' => 'archived'], $dto->toArray());
    }
}
