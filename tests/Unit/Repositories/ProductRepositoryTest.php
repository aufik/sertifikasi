<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\DTO\ProductFilterDTO;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_name_status_category_and_sorts(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Alpha Phone', 'price' => 3000, 'status' => ProductStatus::Active]);
        Product::factory()->create(['name' => 'Beta Laptop', 'price' => 1000, 'status' => ProductStatus::Draft]);

        $repository = new ProductRepository(new Product);
        $result = $repository->search(new ProductFilterDTO('Alpha', ProductStatus::Active->value, $category->id, 10, 'price_desc'));

        $this->assertSame(1, $result->total());
        $this->assertSame('Alpha Phone', $result->items()[0]->name);
    }
}
