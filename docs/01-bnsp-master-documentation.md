# DOKUMENTASI UTAMA PRESENTASI ASESOR BNSP
## Modul Product Management Service — Perusahaan Digital

Document ID: `DOC-BNSP-2026-PRODUCT-SERVICE`  
Peserta Sertifikasi: *[Nama Peserta BNSP]*  
Skema / Kualifikasi: *Pengembang Perangkat Lunak / Software Developer (BNSP)*  
Aplikasi: **Product Management Service (Laravel 12 & Inertia React)**  
Tanggal: **31 Juli 2026**

---

## 📌 Executive Summary & Pemetaan Task Utama

Sistem ini dibangun untuk memenuhi kebutuhan perusahaan digital dalam mengelola data produk internal (lisensi software, langganan SaaS, kuota API, cloud storage, dll.) secara enterprise.

| No | Persyaratan Task | Di Mana Diterapkan / Bagian Kode | Teknologi & Arsitektur yang Digunakan |
|---|---|---|---|
| **1** | **Pengelolaan Data Produk (CRUD)** | `ProductController.php` (API), `ProductPageController.php` (Web), `ProductService.php`, `ProductRepository.php` | **Laravel 12 (PHP 8.4)**, **InertiaJS + React (TypeScript)**, Eloquent ORM, SoftDeletes (`deleted_at`), DB Transactions |
| **2** | **Pencarian Produk** | `ProductRepository::search()`, `ProductFilterDTO.php`, UI `ProductSearchInput.tsx` | Algoritma query Eloquent (`whereLike` SKU/Nama, filter status/kategori, sorting dinamis, safe pagination capping max 100) |
| **3** | **Validasi & Error Handling** | `StoreProductRequest`, `UpdateProductRequest`, `RestockProductRequest`, `bootstrap/app.php` (`withExceptions`), `ApiResponser` trait | FormRequest Validation, Rule::enum, Standardized JSON Error format (Code 40001, 40101, 40301, 40401, 422), Sanctum Auth |
| **4** | **Logging Aktivitas Modul** | `LogProductApiActivity` (log teknis), `AuditProductActivity` (audit bisnis DB) | Monolog (`storage/logs/product.log`), Database Audit Table (`product_audits`), Middleware single-point logging |
| **5** | **Pemantauan Performa Sederhana** | `MonitorApiPerformance` middleware, `AssignRequestId`, `config/monitoring.php` | Performance header (`X-Response-Time-Ms`), Tracing ID (`X-Request-ID`), Alert log slow request (default > 500ms), Health Check API |
| **6** | **Dokumentasi Teknis & Pengguna** | Folder `docs/` (`01` s/d `05`), Swagger UI (`/api/documentation`), `README.md` | Open API / Swagger 3.0 via `darkaonline/l5-swagger`, Markdown Documentation Suite |

---

## 🛠️ Teknologi & Stack Perangkat Lunak

### 1. Backend Stack
- **Framework**: Laravel 12.x running on **PHP 8.4**
- **Architecture Pattern**: Service-Repository Pattern, Data Transfer Object (DTO), Enum Pattern, Policy Authorization
- **Database & Persistence**: SQLite / MySQL, Eloquent ORM, DB Transactions (`DB::transaction`)
- **Security & Authentication**: Laravel Sanctum (Stateful API & Token), Laravel Gate/Policy (`ProductPolicy`)
- **API Documentation**: OpenAPI / Swagger 3.0 (`darkaonline/l5-swagger`)

### 2. Frontend Stack
- **Framework**: React 19 dengan **TypeScript**
- **Bridge**: **Inertia.js v2** (Monolith rasa SPA tanpa API overhead berlebih)
- **Styling**: Tailwind CSS v4, Lucide React Icons
- **Build Tool**: Vite v7

### 3. Quality, Testing & Monitoring Stack
- **Automated Testing**: PHPUnit 11 (33 Test Cases, 109 Assertions)
- **Static Analysis**: PHPStan Level 5 (`larastan/larastan`)
- **Code Style Standard**: Laravel Pint (PSR-12 / Laravel Standard)
- **Stress & Performance Test**: **k6 Stress Testing Script** (`tests/stress/k6-product-search.js`)
- **Telemetry & Debugging**: Laravel Debugbar, Laravel Telescope, Monolog Custom Channel (`product_log`)

---

## 🎯 Detail Jawaban Pemetaan Per Bagian DIT (Daftar Instruksi Terstruktur)

### Bagian A: Analisis & Perancangan Perangkat Lunak

#### A.1 Tools & Pustaka
- **Backend**: `laravel/framework` v12, `inertiajs/inertia-laravel` v3, `darkaonline/l5-swagger` v11, `laravel/sanctum` v4.
- **Frontend**: `@vitejs/plugin-react`, `@tailwindcss/vite`, `lucide-react`, `axios`.
- **Quality**: `phpunit/phpunit` v11, `larastan/larastan` v3, `laravel/pint`.

#### A.2 Kebutuhan Data & Tipe Produk
Produk diklasifikasikan ke dalam 3 tipe kategori via Enum `CategoryType`:
1. `digital`: Software License, SaaS Subscription, Mobile App, API & Integration, Digital Template, Online Course, Media Asset.
2. `service`: Cloud Service, Support Package.
3. `physical`: Hardware Bundle.

Skema entitas `products`: `id` (ULID), `category_id`, `name`, `sku` (format `SKU-DIG-...`), `description`, `price`, `stock`, `status` (`active`, `inactive`, `archived`), `metadata` (JSON), `created_at`, `updated_at`, `deleted_at`.

#### A.3 UX & Alur Penggunaan
- **Guest**: Halaman Login (`/login`) dengan kredensial demo cepat.
- **Dashboard (`/dashboard`)**: Ringkasan total produk, statistik stok, chart distribusi per kategori, dan real-time audit trail.
- **Katalog Produk (`/products`)**: Data grid dinamis, filter kategori/status, modal detail, form create/edit responsive.
- **Operasi Khusus (`/operations/*`)**: Restock, Soft Delete, Restore, dan Purge (hapus permanen) khusus Admin.
- **Log Aktivitas (`/logs/user-activity`)**: Log bisnis keseluruhan user dengan pencarian & pagination.

#### A.4 Diagram Entitas & Arsitektur
- **Entity Relationship**: `Category (1) ---> (*) Product (1) ---> (*) ProductAudit`
- **Arsitektur Pemrosesan Request**:
  `Client (React/Inertia/API) -> Middleware Stack (AssignRequestId -> LogProductApiActivity -> MonitorApiPerformance -> AuditProductActivity) -> Controller -> Policy Check -> Service -> Repository -> Database / Event / Cache`

#### A.5 Subrutin & Functional Decomposition
- `ProductService::create()`: Membuat produk di DB transaction, membersihkan cache.
- `ProductService::restock()`: Mengisi stok dengan locking DB `lockForUpdate()`, mencatat log stok lama vs baru.
- `ProductService::purge()`: Menghapus permanen produk dari trash secara aman.
- `ApiResponser`: Trait reusable untuk penataan format JSON response standar.

---

### Bagian B: Implementasi Modul Perangkat Lunak

#### B.1 Principles & OOP
- Menjaga prinsip **SOLID**: Service bertugas untuk aturan bisnis, Repository untuk query data, DTO untuk lalu lintas data, Policy untuk otorisasi.
- Penggunaan **Strict Typing** (`declare(strict_types=1);`), `final class`, dan Enums (`ProductStatus`, `CategoryType`).

#### B.2 Algoritma CRUD & Pencarian
Pencarian diimplementasikan pada `ProductRepository::search()`:
```php
$query->when($filters->search, fn (Builder $q, string $s) => 
    $q->where(fn (Builder $x) => 
        $x->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")
    )
);
```
Mendukung multi-column sort (`name`, `price`, `stock`, `created_at`) dan limit pagination aman (`min 1`, `max 100`).

#### B.3 Error Handling & Debbuging
- Seluruh exception API ditangkap di `bootstrap/app.php` dan diubah ke JSON standar:
  - `ValidationException` -> Code `40001` (HTTP 422)
  - `ModelNotFoundException` -> Code `40401` (HTTP 404)
  - `AuthenticationException` -> Code `40101` (HTTP 401)
  - `AuthorizationException` -> Code `40301` (HTTP 403)

#### B.4 Hak Akses (Role Matrix Otorisasi)
Dikelola di `ProductPolicy.php`:

| Role | View Catalogue | Create Product | Update Product | Restock | Soft Delete | Restore | Purge (Permanent Delete) |
|---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| **Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Manager** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Viewer** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

### Bagian C: Logging, Monitoring & Evaluasi Performa

#### C.1 Dual Logging Architecture
1. **Log Teknis File (`storage/logs/product.log`)**:
   Dikelola oleh `LogProductApiActivity` middleware. Mencatat method, URL, status code, query parameter, dan user context.
2. **Log Bisnis Database (`product_audits` Table)**:
   Dikelola oleh `AuditProductActivity` middleware. Mencatat event `ProductCreated`, `ProductUpdated`, `ProductDeleted`, `ProductRestocked`, `ProductRestored`, `ProductPurged` secara presisi **1x per HTTP mutation request**.

#### C.2 Pemantauan Performa & Slow Request Alert
- Middleware `MonitorApiPerformance` menghitung waktu eksekusi request dalam milidetik (`$durationMs`).
- Menyuntikkan header HTTP `X-Response-Time-Ms` pada setiap response API.
- Apabila waktu respon $\ge 500\text{ms}$ (dapat diatur di `config/monitoring.php`), sistem otomatis mencatat peringatan `WARNING: Slow API request detected` ke log file.

#### C.3 Benchmark & Stress Testing
Diuji menggunakan **k6** (`tests/stress/k6-product-search.js`) dengan skenario target 50 Virtual Users (VU) bersamaan:
- **P95 Response Time**: $< 800\text{ms}$
- **Failure Rate**: $< 1\%$

---

### Bagian D: Dokumentasi & Penyajian untuk Asesor BNSP

Struktur dokumentasi lengkap yang telah disiapkan di folder `docs/`:
1. `docs/01-technical-documentation.md` — Spesifikasi Teknis & Arsitektur.
2. `docs/02-user-guide.md` — Panduan Pengguna & Manajemen Role.
3. `docs/03-testing-and-quality-report.md` — Laporan Pengujian PHPUnit, PHPStan & k6 Stress Test.
4. `docs/04-assessor-presentation.md` — Panduan Presentasi, Naskah Alur Demo, dan Jawaban Pertanyaan Asesor.
5. `docs/05-dit-compliance-matrix.md` — Matriks Bukti Pemenuhan Terhadap Seluruh Poin DIT A-D.

---

## 💡 Panduan Langkah Demo Saat Presentasi di Depan Asesor

Saat sesi wawancara / demonstrasi, jalankan alur demo berikut:

1. **Persiapan Terminal & Server**:
   ```bash
   php artisan migrate:fresh --seed
   php artisan serve --port=9091
   npm run dev
   ```
2. **Demo Web UI (`http://127.0.0.1:9091`)**:
   - Login menggunakan `admin@example.com` / `password`.
   - Tunjukkan Dashboard: perhatikan statistik produk digital, chart kategori, dan widget audit log.
   - Buka menu **Products**: lakukan pencarian produk (misal ketik `FlowCRM` atau `SKU-DIG`), filter kategori, lalu buat 1 produk baru.
   - Buka menu **Operations**: lakukan Restock produk dan Soft Delete.
   - Buka menu **User Activity Logs**: perhatikan aktivitas yang baru dilakukan kerekam **tepat 1 kali**.
3. **Demo API & Swagger Documentation**:
   - Buka URL `/api/documentation` untuk menampilkan UI Swagger OpenAPI 3.0.
   - Tunjukkan endpoint `GET /api/v1/products` dan `POST /api/v1/products/{product}/restock`.
4. **Demo Code Quality & Command Report**:
   - Jalankan unit & feature test: `php artisan test` (tunjukkan 33 PASS).
   - Jalankan laporan log CLI: `php artisan product:log-report --limit=5`.

---

*Dokumen ini disusun khusus sebagai panduan utama sertifikasi kompetensi BNSP.*
