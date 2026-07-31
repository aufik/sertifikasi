<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return ['name' => $name, 'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 999999), 'type' => fake()->randomElement(CategoryType::cases())->value, 'is_active' => true];
    }
}
