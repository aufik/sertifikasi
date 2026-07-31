<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_product_pages(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $this->actingAs($user)->get('/products')->assertOk();
        $this->actingAs($user)->get('/products/create')->assertOk();
        $this->actingAs($user)->get("/products/{$product->id}")->assertOk();
        $this->actingAs($user)->get("/products/{$product->id}/edit")->assertOk();
    }
}
