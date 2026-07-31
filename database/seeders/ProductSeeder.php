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
        // Kategori produk digital untuk perusahaan digital (Product Management Service).
        $categories = [
            ['name' => 'Software License', 'type' => 'digital'],
            ['name' => 'SaaS Subscription', 'type' => 'digital'],
            ['name' => 'Cloud Service', 'type' => 'service'],
            ['name' => 'Mobile App', 'type' => 'digital'],
            ['name' => 'API & Integration', 'type' => 'digital'],
            ['name' => 'Digital Template', 'type' => 'digital'],
            ['name' => 'Online Course', 'type' => 'digital'],
            ['name' => 'Media Asset', 'type' => 'digital'],
            ['name' => 'Support Package', 'type' => 'service'],
            ['name' => 'Hardware Bundle', 'type' => 'physical'],
        ];

        $createdCategories = [];
        foreach ($categories as $item) {
            $createdCategories[] = Category::firstOrCreate(
                ['name' => $item['name']],
                [
                    'slug' => Str::slug($item['name']).'-'.random_int(100, 999),
                    'type' => $item['type'],
                    'is_active' => true,
                ]
            );
        }

        // Produk digital spesifik & realistis untuk demo internal.
        $catalog = [
            [
                'category_name' => 'Software License',
                'products' => [
                    [
                        'name' => 'NovaDesk Pro License',
                        'sku' => 'SKU-DIG-NDP-001',
                        'desc' => 'Lisensi perpetual untuk suite produktivitas desktop multi-platform.',
                        'price' => 2499000,
                        'stock' => 120,
                    ],
                    [
                        'name' => 'SecureVault Enterprise Key',
                        'sku' => 'SKU-DIG-SVE-002',
                        'desc' => 'Lisensi enkripsi file enterprise dengan manajemen kunci terpusat.',
                        'price' => 4599000,
                        'stock' => 80,
                    ],
                    [
                        'name' => 'PixelCraft Studio SE',
                        'sku' => 'SKU-DIG-PCS-003',
                        'desc' => 'Software desain grafis profesional untuk tim kreatif digital.',
                        'price' => 1899000,
                        'stock' => 200,
                    ],
                ],
            ],
            [
                'category_name' => 'SaaS Subscription',
                'products' => [
                    [
                        'name' => 'FlowCRM Cloud Monthly',
                        'sku' => 'SKU-DIG-FCM-010',
                        'desc' => 'Langganan bulanan CRM cloud untuk sales pipeline dan customer care.',
                        'price' => 499000,
                        'stock' => 999,
                    ],
                    [
                        'name' => 'InsightBoard Analytics Annual',
                        'sku' => 'SKU-DIG-IBA-011',
                        'desc' => 'Dashboard analytics tahunan dengan konektor data multi-sumber.',
                        'price' => 8999000,
                        'stock' => 500,
                    ],
                    [
                        'name' => 'CollabSpace Team Plan',
                        'sku' => 'SKU-DIG-CST-012',
                        'desc' => 'Workspace kolaborasi dokumen, chat, dan task board untuk 25 user.',
                        'price' => 1299000,
                        'stock' => 750,
                    ],
                ],
            ],
            [
                'category_name' => 'Cloud Service',
                'products' => [
                    [
                        'name' => 'ObjectStore 1TB Plan',
                        'sku' => 'SKU-DIG-OS1-020',
                        'desc' => 'Penyimpanan objek cloud 1TB dengan redundancy multi-region.',
                        'price' => 350000,
                        'stock' => 1000,
                    ],
                    [
                        'name' => 'EdgeCDN Starter',
                        'sku' => 'SKU-DIG-CDN-021',
                        'desc' => 'Paket CDN edge untuk distribusi konten digital global.',
                        'price' => 750000,
                        'stock' => 600,
                    ],
                ],
            ],
            [
                'category_name' => 'Mobile App',
                'products' => [
                    [
                        'name' => 'PayLite Merchant App',
                        'sku' => 'SKU-DIG-PLM-030',
                        'desc' => 'Aplikasi merchant untuk penerimaan pembayaran digital QRIS & e-wallet.',
                        'price' => 299000,
                        'stock' => 1500,
                    ],
                    [
                        'name' => 'FieldTrack Ops Mobile',
                        'sku' => 'SKU-DIG-FTO-031',
                        'desc' => 'Aplikasi mobile tracking operasional lapangan real-time.',
                        'price' => 899000,
                        'stock' => 400,
                    ],
                ],
            ],
            [
                'category_name' => 'API & Integration',
                'products' => [
                    [
                        'name' => 'Payment Gateway API Pack',
                        'sku' => 'SKU-DIG-PGA-040',
                        'desc' => 'Paket kuota API gateway pembayaran untuk 100.000 transaksi/bulan.',
                        'price' => 1500000,
                        'stock' => 300,
                    ],
                    [
                        'name' => 'WhatsApp Business Connector',
                        'sku' => 'SKU-DIG-WBC-041',
                        'desc' => 'Integrasi API WhatsApp Business untuk notifikasi dan CS otomatis.',
                        'price' => 2200000,
                        'stock' => 250,
                    ],
                ],
            ],
            [
                'category_name' => 'Online Course',
                'products' => [
                    [
                        'name' => 'Laravel API Masterclass',
                        'sku' => 'SKU-DIG-LAM-050',
                        'desc' => 'Kursus online membangun REST API production-ready dengan Laravel.',
                        'price' => 549000,
                        'stock' => 2000,
                    ],
                    [
                        'name' => 'Product Ops for Digital Teams',
                        'sku' => 'SKU-DIG-POD-051',
                        'desc' => 'Pelatihan digital product operations, backlog, dan release management.',
                        'price' => 799000,
                        'stock' => 1200,
                    ],
                ],
            ],
            [
                'category_name' => 'Support Package',
                'products' => [
                    [
                        'name' => 'Premium Support 24/7',
                        'sku' => 'SKU-DIG-PS7-060',
                        'desc' => 'Paket layanan dukungan teknis premium dengan SLA 24/7.',
                        'price' => 3500000,
                        'stock' => 100,
                    ],
                    [
                        'name' => 'Onboarding Implementation Pack',
                        'sku' => 'SKU-DIG-OIP-061',
                        'desc' => 'Layanan onboarding implementasi modul Product Management Service.',
                        'price' => 5000000,
                        'stock' => 50,
                    ],
                ],
            ],
        ];

        foreach ($catalog as $group) {
            $category = collect($createdCategories)->firstWhere('name', $group['category_name']);
            if (! $category) {
                continue;
            }

            foreach ($group['products'] as $item) {
                Product::firstOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'description' => $item['desc'],
                        'price' => $item['price'],
                        'stock' => $item['stock'],
                        'status' => 'active',
                        'metadata' => [
                            'vendor' => 'Digital Internal Catalog',
                            'delivery' => 'digital_download_or_activation',
                            'billing_model' => str_contains(strtolower($item['name']), 'monthly') || str_contains(strtolower($item['name']), 'annual')
                                ? 'subscription'
                                : 'one_time',
                            'source' => 'digital_seeder',
                        ],
                    ]
                );
            }
        }

        // Lengkapi volume data untuk demo search, pagination, dan dashboard.
        foreach ($createdCategories as $category) {
            Product::factory()
                ->count(12)
                ->create([
                    'category_id' => $category->id,
                ]);
        }
    }
}
