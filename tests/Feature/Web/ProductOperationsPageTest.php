<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductOperationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_web_session_can_open_operations_pages_and_call_product_api(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        Product::factory()->create(['name' => 'API Visible Product', 'sku' => 'OPS-API-001']);

        $this->actingAs($user);

        $this->get('/operations/restock')->assertOk();
        $this->get('/operations/delete')->assertOk();
        $this->get('/operations/restore')->assertOk();
        $this->get('/operations/purge')->assertOk();

        $this->getJson('/api/v1/products?search=OPS-API-001')
            ->assertOk()
            ->assertJsonPath('data.0.sku', 'OPS-API-001');
    }
}
