<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Product Management Service API",
    description: "Enterprise REST API Documentation untuk Modul Product Management Service Perusahaan Digital (Laravel 12)",
    contact: new OA\Contact(name: "Product Engineering Team", email: "admin@example.com")
)]
#[OA\Server(
    url: "http://127.0.0.1:9091",
    description: "Primary API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan Bearer token Sanctum untuk otentikasi API"
)]
#[OA\Tag(name: "Health", description: "API Health Check Endpoint")]
#[OA\Tag(name: "Products", description: "Pengelolaan Katalog Produk Digital (CRUD & Operations)")]
class SwaggerAnnotations
{
    #[OA\Get(
        path: "/api/v1/health",
        summary: "Health Check Endpoint",
        tags: ["Health"],
        responses: [
            new OA\Response(response: 200, description: "Status kesehatan layanan dan database")
        ]
    )]
    public function healthCheck(): void
    {
    }

    #[OA\Get(
        path: "/api/v1/products",
        summary: "Daftar & Pencarian Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", description: "Cari nama atau SKU produk", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "status", in: "query", description: "Filter status (active, inactive, archived)", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "category_id", in: "query", description: "Filter ULID kategori", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "sort", in: "query", description: "Urutan (latest, name_asc, price_asc, stock_desc, dll.)", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "per_page", in: "query", description: "Jumlah data per halaman (max 100)", required: false, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Daftar produk terpaginasi")
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: "/api/v1/products",
        summary: "Tambah Produk Digital Baru",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["category_id", "name", "sku", "description", "price", "stock", "status"],
                properties: [
                    new OA\Property(property: "category_id", type: "string", example: "01kyvtm5cb8ga4b8jbav4e3c50"),
                    new OA\Property(property: "name", type: "string", example: "NovaDesk Pro License"),
                    new OA\Property(property: "sku", type: "string", example: "SKU-DIG-NDP-001"),
                    new OA\Property(property: "description", type: "string", example: "Lisensi perpetual software desktop"),
                    new OA\Property(property: "price", type: "integer", example: 2499000),
                    new OA\Property(property: "stock", type: "integer", example: 100),
                    new OA\Property(property: "status", type: "string", example: "active")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Produk berhasil dibuat")
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: "/api/v1/products/{product}",
        summary: "Detail Satu Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Detail objek produk")
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: "/api/v1/products/{product}",
        summary: "Perbarui Data Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", example: "NovaDesk Pro License v2"),
                    new OA\Property(property: "price", type: "integer", example: 2799000)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Produk berhasil diperbarui")
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: "/api/v1/products/{product}",
        summary: "Soft Delete Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Produk dipindahkan ke trash")
        ]
    )]
    public function destroy(): void
    {
    }

    #[OA\Post(
        path: "/api/v1/products/{product}/stock",
        summary: "Adjust Stok Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "quantity", type: "integer", example: 50),
                    new OA\Property(property: "reason", type: "string", example: "Top-up kuota dari vendor")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Stok berhasil ditambah dan audit tercatat")
        ]
    )]
    public function adjustStock(): void
    {
    }

    #[OA\Get(
        path: "/api/v1/products/trashed",
        summary: "Daftar Produk Terhapus (Trash)",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Daftar produk terhapus")
        ]
    )]
    public function trashed(): void
    {
    }

    #[OA\Post(
        path: "/api/v1/products/{product}/restore",
        summary: "Pulihkan Produk Terhapus",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Produk berhasil dipulihkan")
        ]
    )]
    public function restore(): void
    {
    }

    #[OA\Delete(
        path: "/api/v1/products/{product}/force",
        summary: "Hapus Permanen Produk Digital",
        tags: ["Products"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "product", in: "path", description: "ULID produk", required: true, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Produk dihapus permanen dari basis data")
        ]
    )]
    public function forceDelete(): void
    {
    }
}
