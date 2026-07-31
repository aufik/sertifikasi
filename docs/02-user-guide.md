# PANDUAN PENGGUNA (USER GUIDE & ROLE MANUAL)
## Modul Product Management Service — Aplikasi Catalog Internal

---

## 1. Akun & Kredensial Akses Pengujian

Sistem menyediakan 3 akun default mewakili 3 tingkatan peran (role) penguna:

| Role | Email Login | Password | Hak Akses Utama |
|---|---|---|---|
| **Administrator** | `admin@example.com` | `password` | **Full Access**: Akses penuh ke semua fitur CRUD, Restock, Soft Delete, Restore, Purge, Dashboard, dan User Activity Logs. |
| **Manager** | `manager@example.com` | `password` | **Operations**: Bisa membuat produk, mengedit produk, dan melakukan Restock stok. *Tidak bisa* menghapus, restore, atau purge. |
| **Viewer** | `viewer@example.com` | `password` | **Read-Only**: Hanya bisa melihat katalog produk, detail produk, dashboard, dan log aktivitas. |

---

## 2. Alur Penggunaan Fitur Web Portal

### 2.1 Halaman Login (`/login`)
1. Akses browser ke `http://127.0.0.1:9091/login`.
2. Gunakan petunjuk kredensial cepat yang tertera di kartu login.
3. Klik **Masuk ke Portal Admin**.

### 2.2 Dashboard (`/dashboard`)
1. Menampilkan **Total Produk Digital**, **Total Stok Digital**, dan **Kategori Aktif**.
2. **Chart Distribusi**: Grafik visual persentase produk per tipe (digital, service, physical).
3. **Recent Activity Log**: Panel audit trail aktivitas terbaru real-time yang mencatat siapa, kapan, dan aksi apa yang dilakukan.

### 2.3 Manajemen Katalog Produk (`/products`)
1. **Pencarian & Filter**:
   - Ketik nama produk atau SKU pada kolom pencarian (misal: `FlowCRM` atau `SKU-DIG`).
   - Pilih filter status (`Active`, `Inactive`, `Archived`) atau Filter Kategori.
2. **Tambah Produk (`/products/create`)**:
   - Klik tombol **+ Tambah Produk Baru** (Hanya Role Admin & Manager).
   - Isi Form (Kategori, Nama Produk, SKU Unik, Deskripsi, Harga Rupiah, Stok, Status).
   - Klik **Simpan Produk**.
3. **Edit Produk (`/products/{id}/edit`)**:
   - Klik icon pensil / Edit pada baris produk.
   - Perbarui data yang ingin diubah lalu simpan.

### 2.4 Operasi Khusus Produk (`/operations/*`)
- **Restock Stok (`/operations/restock`)**: Pilihlah produk, masukkan jumlah stok tambahan dan alasan penambahan stok.
- **Soft Delete (`/operations/delete`)**: Menghapus produk dari katalog aktif dan memindahkannya ke Trash (Khusus Admin).
- **Restore Produk (`/operations/restore`)**: Mengembalikan produk yang berada di Trash ke katalog aktif (Khusus Admin).
- **Purge Produk (`/operations/purge`)**: Menghapus produk secara permanen dari database (Khusus Admin).

### 2.5 User Activity Logs (`/logs/user-activity`)
1. Diakses via Sidebar link **User Activity Logs**.
2. Menampilkan riwayat transaksi bisnis lengkap (waktu, jenis event, nama produk, executor user, IP address, request ID).
3. Dilengkapi pencarian log berdasarkan nama user, event, atau request ID.
