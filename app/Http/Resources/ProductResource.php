<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;

final class ProductResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'name' => $product->name,
            'sku' => $product->sku,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
            'status' => (string) $product->getRawOriginal('status'),
            'created_at' => $product->created_at?->toIso8601String(),
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];
    }
}
