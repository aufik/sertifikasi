<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ProductFactory extends Factory
{
    public function definition(): array
    {
        $subjects = ['Pemrograman', 'Matematika', 'Sejarah', 'Filsafat', 'Biologi', 'Manajemen', 'Psikologi', 'Astronomi', 'Sastra', 'Sosiologi'];
        $modifiers = ['Dasar-Dasar', 'Panduan Praktis', 'Analisis Mendalam', 'Teori Kontemporer', 'Pengantar', 'Metode Modern', 'Sejarah Ringkas', 'Studi Kasus'];

        $title = fake()->randomElement($modifiers) . ' ' . fake()->randomElement($subjects) . ' Vol. ' . fake()->numberBetween(1, 5);
        $author = fake()->name();
        $publisher = fake()->company();

        return [
            'category_id' => Category::factory(),
            'name' => $title,
            'sku' => 'SKU-ISBN-' . fake()->unique()->numberBetween(1000000000000, 9999999999999),
            'description' => "Buku referensi ilmiah berfokus pada topik {$title}. Ditulis oleh {$author} dan diterbitkan oleh {$publisher}. Sangat cocok untuk bahan ajar dan riset perpustakaan akademis.",
            'price' => fake()->numberBetween(35000, 250000),
            'stock' => fake()->numberBetween(0, 50),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
            'metadata' => [
                'author' => $author,
                'publisher' => $publisher,
                'year' => fake()->numberBetween(1990, 2026),
                'source' => 'library_factory',
            ],
        ];
    }
}
