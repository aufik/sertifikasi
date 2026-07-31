<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProductValidationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_requires_valid_fields(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/v1/products', [])
            ->assertUnprocessable()
            ->assertJsonPath('meta.success', false)
            ->assertJsonStructure(['errors' => ['category_id', 'name', 'sku', 'description', 'price', 'stock', 'status']]);
    }

    public function test_duplicate_sku_invalid_status_and_negative_values_return_validation_error(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::query()->create(['name' => 'A', 'slug' => 'a', 'type' => CategoryType::Physical->value]);
        Product::factory()->create(['sku' => 'SKU-DUP']);

        $this->postJson('/api/v1/products', [
            'category_id' => $category->id,
            'name' => 'Invalid',
            'sku' => 'SKU-DUP',
            'description' => 'Invalid product',
            'price' => -1,
            'stock' => -1,
            'status' => 'invalid',
        ])->assertUnprocessable()->assertJsonPath('meta.code', 40001);
    }
}
