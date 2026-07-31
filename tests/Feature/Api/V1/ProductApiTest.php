<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\CategoryType;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_products(): void
    {
        $this->getJson('/api/v1/products')->assertUnauthorized()->assertJsonPath('meta.code', 40101);
    }

    public function test_authenticated_user_can_create_and_list_products(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::query()->create(['name' => 'Electronics', 'slug' => 'electronics', 'type' => CategoryType::Physical->value]);

        $payload = ['category_id' => $category->id, 'name' => 'Laptop Pro', 'sku' => 'SKU-001', 'description' => 'Professional laptop', 'price' => 15000000, 'stock' => 8, 'status' => ProductStatus::Active->value];

        $this->postJson('/api/v1/products', $payload)->assertCreated()->assertJsonPath('meta.code', 20100)->assertJsonPath('data.sku', 'SKU-001');
        $this->getJson('/api/v1/products?search=Laptop')->assertOk()->assertJsonPath('meta.pagination.total', 1);
    }

    public function test_authenticated_user_can_update_and_soft_delete_product(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create(['stock' => 3]);

        $this->putJson("/api/v1/products/{$product->id}", ['stock' => 10])->assertOk()->assertJsonPath('data.stock', 10);
        $this->deleteJson("/api/v1/products/{$product->id}")->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
