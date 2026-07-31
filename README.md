# Product Management Service (Laravel 12 & Inertia React)

Enterprise REST API & Admin Portal untuk Modul Product Management Service yang dibangun sesuai instruksi DIT (Daftar Instruksi Terstruktur).

## 🚀 Quick Start (Local Development)

### 1. Jalankan Database Migration & Seed
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Backend Server
```bash
php artisan serve --port=9091
```

### 3. Jalankan Frontend Vite Server (di Terminal lain)
```bash
npm run dev
```

### 4. Akses Aplikasi
- **Aplikasi Web**: `http://127.0.0.1:9091/login`
- **Kredensial Default**:
  - Email: `admin@example.com`
  - Password: `password`

---

## 🧪 Testing & Quality Commands

```bash
# Format kode (Pint)
./vendor/bin/pint

# Analisis Statis (PHPStan Level 5)
./vendor/bin/phpstan analyse --memory-limit=1G

# Jalankan Test Suite (PHPUnit)
php artisan test

# Build Frontend Production Assets
npm run build
```

---

## 📚 Dokumentasi Lengkap (Folder `docs/`)

Seluruh dokumentasi terstruktur dapat diakses di folder `docs/`:

1. `docs/01-technical-documentation.md` - Arsitektur, Entitas, Spesifikasi API & Flow.
2. `docs/02-user-guide.md` - Panduan Pengguna & Hak Akses (Role).
3. `docs/03-testing-and-quality-report.md` - Laporan Pengujian PHPUnit & k6 Stress Test.
4. `docs/04-assessor-presentation.md` - Outline Slide & Naskah Presentasi Asesor.
5. `docs/05-dit-compliance-matrix.md` - Pemetaan Matriks Pemenuhan DIT.
# sertifikasi
