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
        $productLines = [
            'CloudSync', 'DataForge', 'PayLink', 'SecureGate', 'AppPulse',
            'MediaHub', 'CodeNest', 'InsightAI', 'FlowOps', 'PixelStream',
        ];
        $editions = [
            'Starter', 'Standard', 'Pro', 'Business', 'Enterprise', 'Ultimate',
        ];
        $formats = [
            'License Key', 'SaaS Seat', 'API Quota Pack', 'Digital Download',
            'Activation Code', 'Cloud Credit Bundle', 'Addon Module',
        ];

        $line = fake()->randomElement($productLines);
        $edition = fake()->randomElement($editions);
        $format = fake()->randomElement($formats);
        $name = "{$line} {$edition} — {$format}";
        $sku = 'SKU-DIG-'.strtoupper(fake()->unique()->bothify('??###'));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'sku' => $sku,
            'description' => "Produk digital {$name} untuk katalog internal perusahaan. Cocok untuk lisensi software, langganan SaaS, kuota API, atau aset digital operasional.",
            'price' => fake()->numberBetween(99000, 9999000),
            'stock' => fake()->numberBetween(0, 2000),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
            'metadata' => [
                'vendor' => fake()->company(),
                'platform' => fake()->randomElement(['web', 'android', 'ios', 'desktop', 'api', 'multi']),
                'delivery' => fake()->randomElement(['license_key', 'subscription', 'download', 'api_token', 'service_activation']),
                'billing_model' => fake()->randomElement(['one_time', 'monthly', 'annual', 'usage_based']),
                'source' => 'digital_factory',
            ],
        ];
    }
}
