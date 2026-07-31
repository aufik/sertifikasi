<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\CreateProductDTO;
use App\DTO\UpdateProductDTO;
use App\Enums\ProductStatus;
use App\Events\ProductCreated;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_dispatches_event_and_persists_product(): void
    {
        Event::fake([ProductCreated::class]);
        $category = Category::factory()->create();
        $service = app(ProductService::class);

        $product = $service->create(new CreateProductDTO($category->id, 'Service Product', 'SVC-001', 'Description', 1000, 10, ProductStatus::Active));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'sku' => 'SVC-001']);
        Event::assertDispatched(ProductCreated::class);
    }

    public function test_update_delete_restore_and_purge_product(): void
    {
        $service = app(ProductService::class);
        $product = Product::factory()->create(['stock' => 5]);

        $updated = $service->update($product->id, new UpdateProductDTO(null, null, null, null, null, 20, null));
        $this->assertSame(20, $updated->stock);

        $this->assertTrue($service->delete($product->id));
        $this->assertSoftDeleted('products', ['id' => $product->id]);

        $restored = $service->restore($product->id);
        $this->assertNull($restored->deleted_at);

        $this->assertTrue($service->purge($product->id));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
