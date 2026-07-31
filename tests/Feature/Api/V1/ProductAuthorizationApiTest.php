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

final class ProductAuthorizationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_can_view_but_cannot_create_or_update(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));
        $product = Product::factory()->create();

        $this->getJson('/api/v1/products')->assertOk();
        $this->postJson('/api/v1/products', [])->assertForbidden()->assertJsonPath('meta.code', 40301);
        $this->putJson("/api/v1/products/{$product->id}", ['stock' => 1])->assertForbidden();
    }

    public function test_manager_can_create_and_update_but_cannot_delete(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'manager']));
        $category = Category::query()->create(['name' => 'Managed', 'slug' => 'managed', 'type' => CategoryType::Physical->value]);
        $payload = ['category_id' => $category->id, 'name' => 'Managed Product', 'sku' => 'MNG-001', 'description' => 'Managed', 'price' => 1000, 'stock' => 10, 'status' => ProductStatus::Active->value];

        $id = $this->postJson('/api/v1/products', $payload)->assertCreated()->json('data.id');
        $this->putJson("/api/v1/products/{$id}", ['stock' => 5])->assertOk();
        $this->deleteJson("/api/v1/products/{$id}")->assertForbidden();
    }

    public function test_admin_can_delete_restore_and_purge(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $product = Product::factory()->create();

        $this->deleteJson("/api/v1/products/{$product->id}")->assertOk();
        $this->postJson("/api/v1/products/{$product->id}/restore")->assertOk();
        $this->deleteJson("/api/v1/products/{$product->id}/purge")->assertOk();
    }
}
