<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ApiErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_not_found_error_is_standardized(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->getJson('/api/v1/products/01HN0NOTFOUND00000000000000')
            ->assertNotFound()
            ->assertJsonPath('meta.success', false)
            ->assertJsonPath('meta.code', 40401)
            ->assertJsonStructure(['meta' => ['request_id', 'timestamp']]);
    }

    public function test_unauthenticated_and_forbidden_errors_are_standardized(): void
    {
        $product = Product::factory()->create();
        $this->getJson('/api/v1/products')->assertUnauthorized()->assertJsonPath('meta.code', 40101);

        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));
        $this->deleteJson("/api/v1/products/{$product->id}")->assertForbidden()->assertJsonPath('meta.code', 40301);
    }
}
