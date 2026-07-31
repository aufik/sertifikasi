<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Product extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'sku', 'description', 'price', 'stock', 'status', 'metadata', 'search_vector'];

    protected function casts(): array
    {
        return ['status' => ProductStatus::class, 'metadata' => 'array', 'price' => 'integer', 'stock' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
