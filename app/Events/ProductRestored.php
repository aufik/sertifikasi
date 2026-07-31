<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ProductRestored
{
    use Dispatchable, SerializesModels;

    /** @param array<string, mixed> $context */
    public function __construct(public readonly Product $product, public readonly array $context = []) {}
}
