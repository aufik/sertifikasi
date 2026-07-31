<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAudit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $this->call(ProductSeeder::class);

        // Buat log bisnis dummy agar halaman Log tidak kosong saat pertama kali dibuka.
        $products = Product::query()->take(10)->get();
        $ip = '127.0.0.1';
        $ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36';

        foreach ($products as $index => $product) {
            $reqId = 'REQ-'.strtoupper(Str::random(10));

            // Log product created
            ProductAudit::create([
                'product_id' => $product->id,
                'user_id' => $admin->id,
                'user_name' => $admin->name,
                'user_email' => $admin->email,
                'event' => 'ProductCreated',
                'payload' => [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'status' => $product->status,
                ],
                'request_id' => $reqId,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'method' => 'POST',
                'path' => '/api/v1/products',
            ]);

            // Tambahkan variasi log restock
            if ($index % 3 === 0) {
                $restockReqId = 'REQ-'.strtoupper(Str::random(10));
                ProductAudit::create([
                    'product_id' => $product->id,
                    'user_id' => $manager->id,
                    'user_name' => $manager->name,
                    'user_email' => $manager->email,
                    'event' => 'ProductRestocked',
                    'payload' => [
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'old_stock' => $product->stock - 10,
                        'added_stock' => 10,
                        'new_stock' => $product->stock,
                        'reason' => 'Stok masuk dari supplier utama',
                    ],
                    'request_id' => $restockReqId,
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'method' => 'POST',
                    'path' => "/api/v1/products/{$product->id}/restock",
                ]);
            }
        }
    }
}
