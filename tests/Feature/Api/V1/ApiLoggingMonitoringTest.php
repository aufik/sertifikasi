<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\CategoryType;
use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ApiLoggingMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_response_contains_request_and_response_time_headers(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertHeader('X-Request-ID')
            ->assertHeader('X-Response-Time-Ms');
    }

    public function test_product_create_generates_audit_record(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::query()->create(['name' => 'Audit', 'slug' => 'audit', 'type' => CategoryType::Physical->value]);

        $this->postJson('/api/v1/products', [
            'category_id' => $category->id,
            'name' => 'Audit Product',
            'sku' => 'AUD-001',
            'description' => 'Audit product',
            'price' => 1000,
            'stock' => 10,
            'status' => ProductStatus::Active->value,
        ])->assertCreated();

        $this->assertDatabaseHas('product_audits', ['event' => 'ProductCreated']);
        // Pastikan middleware menulis tepat 1 baris (bukan dobel dari event listener).
        $this->assertSame(1, \App\Models\ProductAudit::query()->where('event', 'ProductCreated')->count());
    }
}
