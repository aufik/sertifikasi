<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProductSearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filter_sort_and_pagination_work(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Alpha Phone', 'sku' => 'SKU-ALPHA', 'status' => ProductStatus::Active, 'price' => 2000]);
        Product::factory()->create(['name' => 'Beta Laptop', 'sku' => 'SKU-BETA', 'status' => ProductStatus::Draft, 'price' => 1000]);

        $this->getJson("/api/v1/products?search=SKU-ALPHA&status=active&category_id={$category->id}&sort=price_desc&per_page=1")
            ->assertOk()
            ->assertJsonPath('meta.pagination.total', 1)
            ->assertJsonPath('data.0.sku', 'SKU-ALPHA');
    }

    public function test_empty_search_returns_success_with_empty_data(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));

        $this->getJson('/api/v1/products?search=not-found')
            ->assertOk()
            ->assertJsonPath('meta.success', true)
            ->assertJsonCount(0, 'data');
    }
}
