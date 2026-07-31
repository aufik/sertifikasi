<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori/Genre Buku Perpustakaan secara eksplisit
        $genres = [
            ['name' => 'Novel & Fiksi Sastra', 'type' => 'physical'],
            ['name' => 'Sains & Teknologi', 'type' => 'physical'],
            ['name' => 'Sejarah & Biografi', 'type' => 'physical'],
            ['name' => 'Buku Pelajaran & Edukasi', 'type' => 'physical'],
            ['name' => 'Ensiklopedia & Referensi', 'type' => 'physical'],
            ['name' => 'Komik & Novel Grafis', 'type' => 'physical'],
            ['name' => 'Pengembangan Diri & Karir', 'type' => 'physical'],
            ['name' => 'E-Book & Jurnal Ilmiah', 'type' => 'digital'],
            ['name' => 'Buku Audio (Audiobook)', 'type' => 'service'],
            ['name' => 'Majalah & Publikasi Berkala', 'type' => 'physical'],
        ];

        $createdCategories = [];
        foreach ($genres as $genre) {
            $createdCategories[] = Category::firstOrCreate(
                ['name' => $genre['name']],
                [
                    'slug' => Str::slug($genre['name']) . '-' . rand(100, 999),
                    'type' => $genre['type'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Buat Buku Spesifik yang Sangat Realistis
        $booksData = [
            [
                'category_name' => 'Novel & Fiksi Sastra',
                'books' => [
                    ['name' => 'Laskar Pelangi', 'sku' => 'SKU-ISBN-9789791227032', 'desc' => 'Novel inspiratif tentang perjuangan sepuluh anak Belitung dalam menuntut ilmu di SD Muhammadiyah.'],
                    ['name' => 'Bumi Manusia', 'sku' => 'SKU-ISBN-9789799731234', 'desc' => 'Mahakarya Pramoedya Ananta Toer yang mengisahkan perjuangan Minke di masa kolonial Belanda.'],
                    ['name' => 'Cantik itu Luka', 'sku' => 'SKU-ISBN-9786020312543', 'desc' => 'Novel realisme magis karya Eka Kurniawan yang menceritakan tragedi sejarah melalui kehidupan Dewi Ayu.'],
                    ['name' => 'Ronggeng Dukuh Paruk', 'sku' => 'SKU-ISBN-9789792201904', 'desc' => 'Kisah tragis penari ronggeng Srintil berlatar dukuh kecil yang terisolasi di Jawa Tengah.'],
                ],
            ],
            [
                'category_name' => 'Sains & Teknologi',
                'books' => [
                    ['name' => 'Pengantar Kecerdasan Buatan', 'sku' => 'SKU-ISBN-9786020456123', 'desc' => 'Buku panduan dasar mengenai machine learning, neural networks, dan algoritma AI modern.'],
                    ['name' => 'Belajar Pemrograman PHP 8.4 & Laravel 12', 'sku' => 'SKU-ISBN-9786020299881', 'desc' => 'Panduan komprehensif membangun aplikasi web skala enterprise menggunakan Laravel.'],
                    ['name' => 'Fisika Quantum untuk Pemula', 'sku' => 'SKU-ISBN-9786020512345', 'desc' => 'Penjelasan konsep mekanika kuantum, teori relativitas, dan partikel elementer secara populer.'],
                ],
            ],
            [
                'category_name' => 'Sejarah & Biografi',
                'books' => [
                    ['name' => 'Api Sejarah', 'sku' => 'SKU-ISBN-9786029512311', 'desc' => 'Kajian sejarah perjuangan umat Islam dan ulama dalam mempertahankan NKRI.'],
                    ['name' => 'Soekarno: Biografi Lengkap Negarawan Sejati', 'sku' => 'SKU-ISBN-9786020223121', 'desc' => 'Perjalanan hidup proklamator bangsa Indonesia sejak masa kecil hingga kepemimpinannya.'],
                ],
            ],
            [
                'category_name' => 'Ensiklopedia & Referensi',
                'books' => [
                    ['name' => 'Kamus Besar Bahasa Indonesia (Edisi V)', 'sku' => 'SKU-ISBN-9789794071823', 'desc' => 'Referensi utama ejaan dan makna kata baku bahasa Indonesia terlengkap.'],
                    ['name' => 'Ensiklopedia Geografi Dunia', 'sku' => 'SKU-ISBN-9786020351221', 'desc' => 'Kumpulan peta, fakta iklim, serta kebudayaan dari berbagai negara di penjuru dunia.'],
                ],
            ],
        ];

        // Seed buku spesifik
        foreach ($booksData as $group) {
            $category = collect($createdCategories)->firstWhere('name', $group['category_name']);
            if (! $category) {
                continue;
            }

            foreach ($group['books'] as $book) {
                Product::firstOrCreate(
                    ['sku' => $book['sku']],
                    [
                        'category_id' => $category->id,
                        'name' => $book['name'],
                        'description' => $book['desc'] . ' Buku ini sangat direkomendasikan untuk koleksi perpustakaan.',
                        'price' => rand(45000, 150000),
                        'stock' => rand(5, 30),
                        'status' => 'active',
                        'metadata' => [
                            'author' => fake()->name(),
                            'publisher' => fake()->company(),
                            'year' => rand(1995, 2026),
                            'source' => 'library_seeder',
                        ],
                    ]
                );
            }
        }

        // 3. Gunakan Factory untuk melengkapi total data buku hingga total 150 buku
        foreach ($createdCategories as $category) {
            Product::factory()
                ->count(15) // 15 buku per kategori
                ->create([
                    'category_id' => $category->id,
                ]);
        }
    }
}
