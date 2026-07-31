<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\UpdateProductDTO;
use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product instanceof Product && (bool) $this->user()?->can('update', $product);
    }

    public function rules(): array
    {
        $id = $this->route('product') instanceof Product ? $this->route('product')->id : $this->route('product');

        return ['category_id' => ['sometimes', 'exists:categories,id'], 'name' => ['sometimes', 'string', 'max:255'], 'sku' => ['sometimes', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($id)], 'description' => ['sometimes', 'string'], 'price' => ['sometimes', 'integer', 'min:0'], 'stock' => ['sometimes', 'integer', 'min:0'], 'status' => ['sometimes', Rule::enum(ProductStatus::class)]];
    }

    public function toDto(): UpdateProductDTO
    {
        return new UpdateProductDTO($this->input('category_id'), $this->input('name'), $this->input('sku'), $this->input('description'), $this->has('price') ? $this->integer('price') : null, $this->has('stock') ? $this->integer('stock') : null, $this->has('status') ? ProductStatus::from((string) $this->input('status')) : null);
    }
}
