<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['name', 'slug', 'type', 'is_active'];

    protected function casts(): array
    {
        return ['type' => CategoryType::class, 'is_active' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
