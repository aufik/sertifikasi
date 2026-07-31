<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\CreateProductDTO;
use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Product::class);
    }

    public function rules(): array
    {
        return ['category_id' => ['required', 'exists:categories,id'], 'name' => ['required', 'string', 'max:255'], 'sku' => ['required', 'string', 'max:100', 'unique:products,sku'], 'description' => ['required', 'string'], 'price' => ['required', 'integer', 'min:0'], 'stock' => ['required', 'integer', 'min:0'], 'status' => ['required', Rule::enum(ProductStatus::class)]];
    }

    public function toDto(): CreateProductDTO
    {
        return new CreateProductDTO($this->string('category_id')->toString(), $this->string('name')->toString(), $this->string('sku')->toString(), $this->string('description')->toString(), $this->integer('price'), $this->integer('stock'), ProductStatus::from($this->string('status')->toString()));
    }
}
