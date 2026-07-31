# DOKUMENTASI TEKNIS (TECHNICAL SPECIFICATION)
## Modul Product Management Service — Enterprise Digital Product Catalog

### 1. Deskripsi Sistem & Arsitektur
Aplikasi Product Management Service merupakan modul backend REST API & Portal Web Admin internal yang dirancang dengan arsitektur **Clean Layered Architecture (Service-Repository Pattern)** berbasis **Laravel 12 (PHP 8.4)** dan **Inertia.js + React (TypeScript)**.

```
[ HTTP Request ]
       │
       ▼
[ Middleware Stack ]
  ├── AssignRequestId (X-Request-ID Header)
  ├── LogProductApiActivity (Technical File Log)
  ├── MonitorApiPerformance (Response Time & Slow Alert)
  └── AuditProductActivity (Business Database Audit Audit Trail)
       │
       ▼
[ Controller Layer ] (Api\ProductController / Web\ProductPageController)
       │
       ▼
[ Policy Otorisasi ] (ProductPolicy: Admin / Manager / Viewer)
       │
       ▼
[ Service Layer ] (ProductService: Aturan Bisnis & DB Transactions)
       │
       ▼
[ Repository Layer ] (ProductRepository: Eloquent Queries, Search & Filters)
       │
       ▼
[ Database Storage ] (SQLite / MySQL: categories, products, product_audits)
```

---

### 2. Skema & Model Data (Database Schema)

#### Table: `categories`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | char(26) / ULID | Primary Key | ID unik kategori |
| `name` | varchar(255) | Not Null | Nama kategori (misal: Software License, SaaS Subscription) |
| `slug` | varchar(255) | Unique, Not Null | Slug URL-friendly |
| `type` | varchar(50) | Not Null (Enum) | Tipe kategori (`digital`, `service`, `physical`) |
| `is_active` | boolean | Default true | Status aktif/non-aktif kategori |
| `timestamps` | timestamp | Nullable | `created_at`, `updated_at` |

#### Table: `products`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | char(26) / ULID | Primary Key | ID unik produk |
| `category_id` | char(26) | Foreign Key -> `categories.id` | Kategori produk |
| `name` | varchar(255) | Not Null | Nama produk digital |
| `sku` | varchar(100) | Unique, Not Null | Kode SKU unik (format `SKU-DIG-...`) |
| `description` | text | Not Null | Deskripsi rincian produk |
| `price` | integer | Not Null, Min 0 | Harga produk (Rupiah) |
| `stock` | integer | Not Null, Min 0 | Jumlah stok tersedia |
| `status` | varchar(50) | Not Null (Enum) | Status (`active`, `inactive`, `archived`) |
| `metadata` | json | Nullable | Metadata khusus (vendor, billing_model, platform) |
| `timestamps` | timestamp | Nullable | `created_at`, `updated_at` |
| `deleted_at` | timestamp | Nullable (SoftDeletes) | Tanggal soft delete |

#### Table: `product_audits`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | char(26) / ULID | Primary Key | ID audit log |
| `product_id` | char(26) | Nullable | Reference ID produk |
| `user_id` | bigint | Nullable | ID user eksekutor |
| `user_name` | varchar(255) | Nullable | Nama user saat aksi |
| `user_email` | varchar(255) | Nullable | Email user saat aksi |
| `event` | varchar(100) | Not Null | Jenis aksi (`ProductCreated`, `ProductUpdated`, `ProductDeleted`, `ProductRestocked`, `ProductRestored`, `ProductPurged`) |
| `payload` | json | Nullable | Snapshot data produk dan perubahan |
| `request_id` | varchar(255) | Nullable | Tracing ID request |
| `ip_address` | varchar(45) | Nullable | IP Address client |
| `user_agent` | text | Nullable | User Agent browser/client |
| `method` | varchar(10) | Nullable | HTTP Method |
| `path` | varchar(255) | Nullable | Route path HTTP |
| `created_at` | timestamp | Nullable | Tanggal & waktu audit dicatat |

---

### 3. REST API Endpoint Specification

Base URL: `/api/v1`

| Method | Endpoint | Description | Query / Body Params | Auth Required | Minimum Role |
|---|---|---|---|---|---|
| `GET` | `/health` | Health check service status | - | No | Public |
| `GET` | `/products` | List & Search produk | `search`, `status`, `category_id`, `sort`, `page`, `per_page` | Sanctum Bearer | Viewer |
| `POST` | `/products` | Tambah produk baru | `category_id`, `name`, `sku`, `description`, `price`, `stock`, `status` | Sanctum Bearer | Manager |
| `GET` | `/products/{id}` | Detail satu produk | - | Sanctum Bearer | Viewer |
| `PUT/PATCH` | `/products/{id}` | Update data produk | `category_id`, `name`, `price`, `stock`, `status`, dll. | Sanctum Bearer | Manager |
| `DELETE` | `/products/{id}` | Soft delete produk | - | Sanctum Bearer | Admin |
| `POST` | `/products/{id}/restock` | Tambah stok produk | `quantity` (int > 0), `reason` (string) | Sanctum Bearer | Manager |
| `GET` | `/products/trash` | Daftar produk terhapus | `search`, `page`, `per_page` | Sanctum Bearer | Admin |
| `POST` | `/products/{id}/restore` | Pulihkan dari soft delete | - | Sanctum Bearer | Admin |
| `DELETE` | `/products/{id}/purge` | Hapus permanen produk | - | Sanctum Bearer | Admin |

---

### 4. Format Output Standard JSON API

#### Respon Sukses (Standard 200 OK / 201 Created)
```json
{
  "meta": {
    "success": true,
    "code": 20000,
    "message": "Operation successful",
    "request_id": "REQ-ABC1234567",
    "timestamp": "2026-07-31T20:00:00+07:00"
  },
  "data": {
    "id": "01kyvtm5cb8ga4b8jbav4e3c59",
    "name": "NovaDesk Pro License",
    "sku": "SKU-DIG-NDP-001",
    "price": 2499000,
    "stock": 120,
    "status": "active"
  }
}
```

#### Respon Error Standar (Format Terpadu Exception Handler)
```json
{
  "meta": {
    "success": false,
    "code": 40001,
    "message": "Validation Error",
    "request_id": "REQ-ABC1234567",
    "timestamp": "2026-07-31T20:00:00+07:00"
  },
  "errors": {
    "sku": ["The sku has already been taken."]
  }
}
```

---

### 5. Konfigurasi Monitoring & Log

- **Technical Log File**: `storage/logs/product.log`
- **Ambangan Slow Request**: `SLOW_REQUEST_THRESHOLD_MS=500` di `.env` atau `config/monitoring.php`.
- **Command CLI Laporan**: `php artisan product:log-report --limit=10 --json`
