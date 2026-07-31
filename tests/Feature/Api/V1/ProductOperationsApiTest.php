<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProductOperationsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_restock_product_and_business_audit_is_recorded(): void
    {
        $user = User::factory()->create(['role' => 'manager']);
        Sanctum::actingAs($user);
        $product = Product::factory()->create(['stock' => 10]);

        $this->withHeader('X-Request-ID', '11111111-1111-4111-8111-111111111111')
            ->postJson("/api/v1/products/{$product->id}/restock", [
                'quantity' => 7,
                'reason' => 'Warehouse replenishment',
            ])
            ->assertOk()
            ->assertJsonPath('data.stock', 17);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 17]);
        $this->assertDatabaseHas('product_audits', [
            'event' => 'ProductRestocked',
            'product_id' => $product->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'request_id' => '11111111-1111-4111-8111-111111111111',
        ]);
    }

    public function test_viewer_cannot_restock_product(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'viewer']));
        $product = Product::factory()->create(['stock' => 10]);

        $this->postJson("/api/v1/products/{$product->id}/restock", ['quantity' => 1])->assertForbidden();
    }

    public function test_restock_quantity_must_be_positive(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'manager']));
        $product = Product::factory()->create(['stock' => 10]);

        $this->postJson("/api/v1/products/{$product->id}/restock", ['quantity' => 0])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity']);
    }

    public function test_admin_restore_and_purge_generate_business_audits(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($user);
        $product = Product::factory()->create();

        $this->deleteJson("/api/v1/products/{$product->id}")->assertOk();
        $this->postJson("/api/v1/products/{$product->id}/restore")->assertOk();
        $this->deleteJson("/api/v1/products/{$product->id}")->assertOk();
        $this->deleteJson("/api/v1/products/{$product->id}/purge")->assertOk();

        $this->assertDatabaseHas('product_audits', ['event' => 'ProductRestored', 'product_id' => $product->id, 'user_id' => $user->id]);
        $this->assertDatabaseHas('product_audits', ['event' => 'ProductPurged', 'product_id' => $product->id, 'user_id' => $user->id]);
    }

    public function test_product_log_report_command_runs_successfully(): void
    {
        $this->artisan('product:log-report', ['--limit' => 3])->assertExitCode(0);
        $this->artisan('product:log-report', ['--limit' => 3, '--json' => true])->assertExitCode(0);
    }
}
