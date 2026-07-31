import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
import os

def create_styled_document(doc_title, doc_subtitle, metadata_rows):
    doc = Document()
    
    # Page setup - Margins 1 inch (72pt)
    for section in doc.sections:
        section.top_margin = Inches(1)
        section.bottom_margin = Inches(1)
        section.left_margin = Inches(1)
        section.right_margin = Inches(1)
        section.page_width = Inches(8.5)
        section.page_height = Inches(11)

    # Styling helper functions
    def set_cell_bg(cell, hex_color):
        tcPr = cell._element.get_or_add_tcPr()
        shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
        tcPr.append(shd)

    def set_cell_margins(cell, top=140, bottom=140, left=180, right=180):
        tcPr = cell._element.get_or_add_tcPr()
        tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
        tcPr.append(tcMar)

    def set_cell_border(cell, **kwargs):
        tcPr = cell._element.get_or_add_tcPr()
        tcBorders = parse_xml(f'<w:tcBorders {nsdecls("w")}/>')
        for edge in ('top', 'left', 'bottom', 'right', 'insideH', 'insideV'):
            edge_data = kwargs.get(edge)
            if edge_data:
                tag = f'w:{edge}'
                element = parse_xml(f'<{tag} {nsdecls("w")} w:val="{edge_data.get("val", "single")}" w:sz="{edge_data.get("sz", "4")}" w:space="0" w:color="{edge_data.get("color", "auto")}"/>')
                tcBorders.append(element)
            else:
                tag = f'w:{edge}'
                element = parse_xml(f'<{tag} {nsdecls("w")} w:val="none"/>')
                tcBorders.append(element)
        tcPr.append(tcBorders)

    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(20)
        p.paragraph_format.space_after = Pt(8)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(16)
        run.font.bold = True
        run.font.color.rgb = RGBColor(30, 41, 59) # Slate 800
        
        pBdr = parse_xml(f'<w:pBdr {nsdecls("w")}><w:bottom w:val="single" w:sz="16" w:space="4" w:color="4F46E5"/></w:pBdr>')
        p._element.get_or_add_pPr().append(pBdr)
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(13)
        run.font.bold = True
        run.font.color.rgb = RGBColor(79, 70, 229) # Indigo 600
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(11)
        run.font.bold = True
        run.font.color.rgb = RGBColor(14, 165, 233) # Sky 600
        return p

    def add_p(text="", bold_prefix="", italic_suffix=""):
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            r_pre = p.add_run(bold_prefix)
            r_pre.font.name = 'Calibri'
            r_pre.font.size = Pt(10.5)
            r_pre.font.bold = True
            r_pre.font.color.rgb = RGBColor(30, 41, 59)
        if text:
            r_text = p.add_run(text)
            r_text.font.name = 'Calibri'
            r_text.font.size = Pt(10.5)
            r_text.font.color.rgb = RGBColor(51, 65, 85)
        if italic_suffix:
            r_it = p.add_run(italic_suffix)
            r_it.font.name = 'Calibri'
            r_it.font.size = Pt(10)
            r_it.font.italic = True
            r_it.font.color.rgb = RGBColor(100, 116, 139)
        return p

    def add_bullet(text, bold_prefix=""):
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            r_pre = p.add_run(bold_prefix)
            r_pre.font.name = 'Calibri'
            r_pre.font.size = Pt(10.5)
            r_pre.font.bold = True
            r_pre.font.color.rgb = RGBColor(30, 41, 59)
        r_text = p.add_run(text)
        r_text.font.name = 'Calibri'
        r_text.font.size = Pt(10.5)
        r_text.font.color.rgb = RGBColor(51, 65, 85)
        return p

    def add_ss_placeholder(title, instructions, caption):
        tbl = doc.add_table(rows=1, cols=1)
        tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
        tbl.autofit = False
        
        cell = tbl.cell(0, 0)
        cell.width = Inches(6.3)
        set_cell_bg(cell, "F8FAFC")
        set_cell_margins(cell, top=180, bottom=180, left=240, right=240)
        
        set_cell_border(cell, 
                        left={'val': 'single', 'sz': '24', 'color': '4F46E5'},
                        top={'val': 'single', 'sz': '4', 'color': 'CBD5E1'},
                        bottom={'val': 'single', 'sz': '4', 'color': 'CBD5E1'},
                        right={'val': 'single', 'sz': '4', 'color': 'CBD5E1'})
        
        p = cell.paragraphs[0]
        p.paragraph_format.space_after = Pt(4)
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        
        r_icon = p.add_run("📸  [ WADAH TAMPILAN SCREENSHOT ]\n")
        r_icon.font.name = 'Calibri'
        r_icon.font.size = Pt(11)
        r_icon.font.bold = True
        r_icon.font.color.rgb = RGBColor(79, 70, 229)
        
        r_title = p.add_run(f"{title}\n")
        r_title.font.name = 'Calibri'
        r_title.font.size = Pt(11)
        r_title.font.bold = True
        r_title.font.color.rgb = RGBColor(15, 23, 42)
        
        r_inst = p.add_run(f"Petunjuk Pengambilan SS: {instructions}\n(Silakan klik kanan / Paste Gambar SS Anda tepat di kotak ini)")
        r_inst.font.name = 'Calibri'
        r_inst.font.size = Pt(9.5)
        r_inst.font.italic = True
        r_inst.font.color.rgb = RGBColor(100, 116, 139)
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_before = Pt(4)
        p_cap.paragraph_format.space_after = Pt(12)
        r_cap = p_cap.add_run(f"Gambar Bukti: {caption}")
        r_cap.font.name = 'Calibri'
        r_cap.font.size = Pt(9.5)
        r_cap.font.bold = True
        r_cap.font.italic = True
        r_cap.font.color.rgb = RGBColor(71, 85, 105)

    def render_table(col_widths, headers, data):
        table = doc.add_table(rows=1, cols=len(headers))
        table.alignment = WD_TABLE_ALIGNMENT.CENTER
        table.autofit = False
        
        hdr_cells = table.rows[0].cells
        for i, title in enumerate(headers):
            hdr_cells[i].width = Inches(col_widths[i])
            set_cell_bg(hdr_cells[i], "1E293B")
            set_cell_margins(hdr_cells[i], top=120, bottom=120, left=140, right=140)
            set_cell_border(hdr_cells[i], 
                            bottom={'val': 'single', 'sz': '12', 'color': '4F46E5'},
                            top={'val': 'single', 'sz': '4', 'color': '334155'},
                            left={'val': 'single', 'sz': '4', 'color': '334155'},
                            right={'val': 'single', 'sz': '4', 'color': '334155'})
            p = hdr_cells[i].paragraphs[0]
            p.paragraph_format.space_after = Pt(0)
            r = p.add_run(title)
            r.font.name = 'Calibri'
            r.font.size = Pt(9.5)
            r.font.bold = True
            r.font.color.rgb = RGBColor(255, 255, 255)
            
        for r_idx, row_data in enumerate(data):
            row_cells = table.add_row().cells
            bg_color = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
            for c_idx, val in enumerate(row_data):
                row_cells[c_idx].width = Inches(col_widths[c_idx])
                set_cell_bg(row_cells[c_idx], bg_color)
                set_cell_margins(row_cells[c_idx], top=100, bottom=100, left=140, right=140)
                set_cell_border(row_cells[c_idx],
                                bottom={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                                top={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                                left={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                                right={'val': 'single', 'sz': '4', 'color': 'E2E8F0'})
                p = row_cells[c_idx].paragraphs[0]
                p.paragraph_format.space_after = Pt(0)
                r = p.add_run(str(val))
                r.font.name = 'Calibri'
                r.font.size = Pt(9)
                r.font.color.rgb = RGBColor(51, 65, 85)
        
        sp = doc.add_paragraph()
        sp.paragraph_format.space_after = Pt(6)

    # Header Title Banner
    p_cover = doc.add_paragraph()
    p_cover.paragraph_format.space_before = Pt(10)
    p_cover.paragraph_format.space_after = Pt(18)
    
    r_main_t = p_cover.add_run(f"{doc_title}\n")
    r_main_t.font.name = 'Calibri'
    r_main_t.font.size = Pt(22)
    r_main_t.font.bold = True
    r_main_t.font.color.rgb = RGBColor(30, 41, 59)
    
    r_sub_t = p_cover.add_run(doc_subtitle)
    r_sub_t.font.name = 'Calibri'
    r_sub_t.font.size = Pt(13)
    r_sub_t.font.color.rgb = RGBColor(79, 70, 229)
    
    render_table([1.8, 4.5], ["Properti Dokumen", "Keterangan Spesifikasi"], metadata_rows)

    return doc, add_h1, add_h2, add_h3, add_p, add_bullet, add_ss_placeholder, render_table

def build_technical_documentation():
    meta = [
        ["Nama Peserta", "[Isi Nama Peserta Sertifikasi BNSP]"],
        ["Skema Sertifikasi", "Software Developer / Pengembang Perangkat Lunak (BNSP)"],
        ["Nama Berkas", "Dokumentasi Teknis Modul Product Management Service"],
        ["Stack Perangkat Lunak", "Laravel 12 (PHP 8.4), React TypeScript, Inertia v2, Tailwind v4, Swagger"],
        ["Tanggal Terbit", "31 Juli 2026"],
        ["Status Sistem", "Production Ready - 33 PHPUnit Tests Passed (100%)"],
    ]
    doc, add_h1, add_h2, add_h3, add_p, add_bullet, add_ss_placeholder, render_table = create_styled_document(
        "DOKUMENTASI TEKNIS (TECHNICAL SPECIFICATION)",
        "Modul Product Management Service — Katalog Produk Digital Perusahaan",
        meta
    )

    # BAB 1
    add_h1("1. Pendahuluan & Lingkup Sistem")
    add_p("Dokumen ini berisi spesifikasi teknis lengkap untuk Modul Product Management Service yang dibangun sesuai standar instruksi DIT (Daftar Instruksi Terstruktur) uji kompetensi BNSP Software Developer. Modul ini bertanggung jawab mengelola katalog produk digital internal perusahaan (Software License, SaaS Subscription, Cloud Service, API Integration, dll.).")

    add_h2("1.1 Pemetaan Task 1 - 6 ke Komponen Arsitektur Kode")
    render_table(
        [0.5, 1.6, 2.3, 1.9],
        ["No", "Persyaratan Task", "Lokasi Berkas & Kelas Utama", "Teknologi & Pattern"],
        [
            ["1", "Pengelolaan Data Produk (CRUD)", "ProductController.php, ProductPageController.php, ProductService.php, ProductRepository.php", "Laravel 12 (PHP 8.4), Eloquent ORM, SoftDeletes, Service-Repository Pattern"],
            ["2", "Pencarian Produk", "ProductRepository::search(), ProductFilterDTO.php, ProductSearchInput.tsx", "Eloquent LIKE Query (Nama/SKU), Multi-column Sort, Pagination Limit Capping"],
            ["3", "Validasi & Error Handling", "StoreProductRequest, UpdateProductRequest, bootstrap/app.php, ApiResponser Trait", "FormRequest Validation, Rule::enum, Standardized Exception Handler JSON"],
            ["4", "Logging Aktivitas Modul", "LogProductApiActivity.php (File Log), AuditProductActivity.php (DB Audit)", "Monolog (product.log), Database Table product_audits, Single Middleware Logging"],
            ["5", "Pemantauan Performa Modul", "MonitorApiPerformance.php, AssignRequestId.php, config/monitoring.php", "Performance Header X-Response-Time-Ms, Tracing ID X-Request-ID, Slow Warning Alert"],
            ["6", "Dokumentasi Teknis & User", "docs/01-technical-documentation.md, Swagger UI (/api/documentation)", "OpenAPI / Swagger 3.0 via darkaonline/l5-swagger, Python-Docx Generator"]
        ]
    )

    # BAB 2
    add_h1("2. Arsitektur Aplikasi & Alur Pemrosesan Request")
    add_p("Aplikasi menerapkan Clean Layered Architecture (Service-Repository Pattern) untuk memisahkan logika antarmuka, aturan bisnis, dan akses basis data. Setiap HTTP Request yang masuk diproses melalui pipeline middleware terstruktur:")
    
    add_bullet(" Menggenerate dan menyuntikkan ID unik penelusuran X-Request-ID pada setiap request.", "1. AssignRequestId Middleware:")
    add_bullet(" Mencatat aktivitas teknis API (method, URL, status code, user context) ke file storage/logs/product.log.", "2. LogProductApiActivity Middleware:")
    add_bullet(" Menghitung durasi eksekusi dalam milidetik (X-Response-Time-Ms) dan mencatat alert jika durasi >= 500ms.", "3. MonitorApiPerformance Middleware:")
    add_bullet(" Mencatat mutasi data bisnis (POST/PUT/DELETE) tepat 1x ke tabel database product_audits.", "4. AuditProductActivity Middleware:")
    add_bullet(" Memastikan otorisasi berbasis role (Admin, Manager, Viewer) sebelum mengeksekusi operasi bisnis.", "5. Policy Authorization (ProductPolicy):")

    add_ss_placeholder(
        "Diagram Arsitektur Sistem & Alur Pemrosesan Request",
        "Ambil screenshot diagram alur arsitektur (Client -> Middleware Stack -> Controller -> Policy -> Service -> Repository -> Database).",
        "Arsitektur Layered & Alur Pemrosesan Request HTTP"
    )

    # BAB 3
    add_h1("3. Perancangan Basis Data & Model Data")
    add_p("Basis data menggunakan SQLite / MySQL yang dirancang dengan skema relasional terstruktur dan memanfaatkan fitur SoftDeletes pada entitas produk.")

    add_h2("3.1 Tabel Basis Data & Relasi Entitas")
    render_table(
        [1.3, 1.2, 3.8],
        ["Nama Tabel", "Primary Key", "Fungsi & Kolom Utama"],
        [
            ["categories", "ULID (id)", "Menyimpan nama kategori, slug, tipe (digital/service/physical), dan status aktif."],
            ["products", "ULID (id)", "Menyimpan category_id (FK), name, sku (unique), description, price, stock, status, metadata (JSON), dan deleted_at (SoftDeletes)."],
            ["product_audits", "ULID (id)", "Menyimpan product_id, user_id, user_name, user_email, event, payload (JSON), request_id, ip_address, user_agent, method, path."]
        ]
    )

    add_h2("3.2 Policy Otorisasi Hak Akses (RBAC Matrix)")
    render_table(
        [1.2, 0.7, 0.7, 0.7, 0.7, 0.7, 0.8, 0.8],
        ["Role User", "View", "Create", "Update", "Restock", "Delete", "Restore", "Purge"],
        [
            ["Admin", "Ya", "Ya", "Ya", "Ya", "Ya", "Ya", "Ya"],
            ["Manager", "Ya", "Ya", "Ya", "Ya", "Tidak", "Tidak", "Tidak"],
            ["Viewer", "Ya", "Tidak", "Tidak", "Tidak", "Tidak", "Tidak", "Tidak"]
        ]
    )

    # BAB 4
    add_h1("4. Spesifikasi REST API & OpenAPI Swagger Documentation")
    add_p("Seluruh API produk dikembangkan mengikuti standar RESTful API berbasis JSON dengan prefix /api/v1. Dokumentasi interaktif Swagger 3.0 tersedia di /api/documentation.")

    render_table(
        [0.8, 2.2, 2.3, 1.0],
        ["Method", "Endpoint Path", "Deskripsi Operasi", "Min Role"],
        [
            ["GET", "/api/v1/health", "Health check status layanan & DB", "Public"],
            ["GET", "/api/v1/products", "List & Search katalog produk digital", "Viewer"],
            ["POST", "/api/v1/products", "Membuat produk digital baru", "Manager"],
            ["GET", "/api/v1/products/{id}", "Detail satu produk digital", "Viewer"],
            ["PUT/PATCH", "/api/v1/products/{id}", "Memperbarui data produk digital", "Manager"],
            ["DELETE", "/api/v1/products/{id}", "Soft delete produk digital", "Admin"],
            ["POST", "/api/v1/products/{id}/restock", "Menambah stok produk digital (Restock)", "Manager"],
            ["GET", "/api/v1/products/trash", "Daftar produk digital yang terhapus (Trash)", "Admin"],
            ["POST", "/api/v1/products/{id}/restore", "Pulihkan produk dari soft delete", "Admin"],
            ["DELETE", "/api/v1/products/{id}/purge", "Hapus produk secara permanen", "Admin"]
        ]
    )

    add_ss_placeholder(
        "Antarmuka OpenAPI / Swagger UI Dokumentasi API",
        "Buka browser -> Akses http://127.0.0.1:9091/api/documentation -> Take SS antarmuka Swagger API.",
        "Dokumentasi API Interaktif Swagger UI"
    )

    # BAB 5
    add_h1("5. Technical Logging & Performance Monitoring")
    add_bullet(" Disimpan ke storage/logs/product.log melalui LogProductApiActivity middleware untuk melacak aktivitas teknis.", "1. File Log Teknis:")
    add_bullet(" Disimpan ke tabel product_audits melalui AuditProductActivity middleware tepat 1x per mutasi data.", "2. Audit Log Bisnis:")
    add_bullet(" Menyuntikkan X-Response-Time-Ms & X-Request-ID serta mencatat WARNING jika durasi >= 500ms.", "3. Monitor Performa & Alert:")

    add_ss_placeholder(
        "Tampilan Console Command Laporan Log CLI (product:log-report)",
        "Buka terminal -> Jalankan `php artisan product:log-report --limit=10` -> Take SS tabel laporan konsol CLI.",
        "Laporan Audit Log & Technical File Log via Artisan CLI Command"
    )

    # BAB 6
    add_h1("6. Laporan Kualitas, Testing & Stress Benchmark")
    add_p("Aplikasi diuji secara ketat menggunakan 3 lapisan pengujian kualitatif:")
    add_bullet(" 33 Test Cases lulus 100% (109 Assertions) menguji DTO, Service, Repository, API, Policy, dan Operasi.", "1. Automated PHPUnit Testing:")
    add_bullet(" Larastan Level 5 tanpa error tipe atau undefined variables.", "2. PHPStan Static Analysis:")
    add_bullet(" k6 Load Test pada 50 Virtual Users menghasilkan Response Time P95 142.5ms (Threshold < 800ms) dan Error Rate 0.00%.", "3. k6 Stress Testing:")

    add_ss_placeholder(
        "Hasil Eksekusi Perintah php artisan test di Terminal",
        "Buka terminal -> Jalankan `php artisan test` -> Take SS layar terminal 33 PASSED.",
        "Bukti Hasil Lulus Pengujian Otomatis PHPUnit"
    )

    out_path = "/var/www/sertifikasi_manual/docs/Dokumentasi_Teknis_Product_Management_Service.docx"
    doc.save(out_path)
    print(f"Technical Doc created at: {out_path}")

def build_user_guide_documentation():
    meta = [
        ["Nama Peserta", "[Isi Nama Peserta Sertifikasi BNSP]"],
        ["Skema Sertifikasi", "Software Developer / Pengembang Perangkat Lunak (BNSP)"],
        ["Nama Berkas", "Panduan Pengguna (User Guide) Modul Product Management Service"],
        ["Aplikasi", "Portal Admin Katalog Produk Digital Perusahaan"],
        ["URL Web Portal", "http://127.0.0.1:9091/login"],
        ["Tanggal Terbit", "31 Juli 2026"],
    ]
    doc, add_h1, add_h2, add_h3, add_p, add_bullet, add_ss_placeholder, render_table = create_styled_document(
        "PANDUAN PENGGUNA (USER GUIDE MANUAL)",
        "Modul Product Management Service — Portal Manajemen Produk Digital",
        meta
    )

    # BAB 1
    add_h1("1. Pendahuluan & Akun Pengujian System")
    add_p("Panduan pengguna ini disusun sebagai petunjuk langkah demi langkah dalam mengoperasikan Portal Admin Modul Product Management Service. Aplikasi ini memungkinkan pengguna mengelola katalog produk digital perusahaan (lisensi software, langganan SaaS, kuota API, cloud storage, dll.).")

    add_h2("1.1 Kredensial Akses Akun Demo Pengujian")
    render_table(
        [1.5, 1.8, 1.0, 2.0],
        ["Role Pengguna", "Email Login", "Password", "Hak Akses Utama"],
        [
            ["Administrator", "admin@example.com", "password", "Akses Penuh: CRUD, Restock, Soft Delete, Restore, Purge, Dashboard, Activity Logs"],
            ["Manager", "manager@example.com", "password", "Operasional: Membaca katalog, Membuat produk, Edit produk, dan Restock stok"],
            ["Viewer", "viewer@example.com", "password", "Read-Only: Membaca katalog produk, detail produk, dashboard, dan activity log"]
        ]
    )

    # BAB 2
    add_h1("2. Memulai Aplikasi & Otentikasi (Login & Logout)")
    add_p("1. Buka aplikasi web browser (Chrome / Firefox / Edge) dan navigasikan ke alamat URL: http://127.0.0.1:9091/login")
    add_p("2. Pada halaman Login, Anda akan melihat kartu masuk berdesain modern yang dilengkapi dengan petunjuk kredensial cepat.")
    add_p("3. Masukkan Email dan Password sesuai role yang ingin Anda uji, lalu klik tombol 'Masuk ke Portal Admin'.")
    add_p("4. Untuk Logout (keluar sistem), klik tombol 'Logout' di bagian kanan atas Topbar atau di bagian bawah Sidebar profil.")

    add_ss_placeholder(
        "Tampilan Halaman Login Portal Admin",
        "Buka browser -> Akses http://127.0.0.1:9091/login -> Take SS halaman Login.",
        "Antarmuka Halaman Login Portal Admin"
    )

    # BAB 3
    add_h1("3. Navigasi Portal & Dashboard Utama")
    add_p("Setelah berhasil login, pengguna akan diarahkan ke Halaman Dashboard Utama (/dashboard). Dashboard menyediakan rangkuman informasi eksekutif:")
    
    add_bullet(" Menampilkan total jumlah jenis produk digital yang terdaftar.", " Total Produk Digital:")
    add_bullet(" Menampilkan akumulasi seluruh eksemplar/unit stok digital yang tersedia di sistem.", " Total Stok Digital:")
    add_bullet(" Menampilkan grafik visual distribusi persentase produk per tipe (Digital, Service, Physical).", " Chart Distribusi Kategori:")
    add_bullet(" Panel aktivitas terbaru yang menampilkan siapa dan kapan transaksi produk dilakukan.", " Real-Time Audit Log Widget:")

    add_ss_placeholder(
        "Tampilan Dashboard Utama Admin",
        "Buka browser -> Akses http://127.0.0.1:9091/dashboard -> Take SS halaman Dashboard Admin.",
        "Antarmuka Dashboard Utama Admin & Real-Time Audit Log"
    )

    # BAB 4
    add_h1("4. Pengelolaan Katalog Produk Digital (CRUD)")

    add_h2("4.1 Membaca & Mencari Produk (/products)")
    add_p("1. Buka menu 'Products' di Sidebar navigasi sebelah kiri.")
    add_p("2. Kolom Pencarian: Ketik nama produk atau SKU (misal: 'FlowCRM' atau 'SKU-DIG') pada kolom cari. Data akan memfilter secara otomatis.")
    add_p("3. Filter Kategori & Status: Gunakan dropdown filter untuk menampilkan produk pada kategori tertentu atau status Active/Inactive/Archived.")
    add_p("4. Pagination: Gunakan tombol navigasi halaman di bagian bawah tabel untuk berpindah halaman data.")

    add_ss_placeholder(
        "Katalog Produk Digital & Fitur Pencarian / Filter",
        "Buka browser -> Halaman /products -> Ketik 'FlowCRM' pada pencarian -> Take SS.",
        "Antarmuka Katalog Produk Digital & Filter Pencarian"
    )

    add_h2("4.2 Menambah Produk Digital Baru (/products/create)")
    add_p("1. Pada halaman katalog produk, klik tombol '+ Tambah Produk Baru' di sudut kanan atas (Role Admin & Manager).")
    add_p("2. Pilih Kategori Produk (misal: Software License, SaaS Subscription).")
    add_p("3. Isi Nama Produk (misal: 'NovaDesk Pro License') dan SKU unik (format `SKU-DIG-...`).")
    add_p("4. Masukkan Deskripsi lengkap produk, Harga (dalam Rupiah), Jumlah Stok awal, dan Status produk.")
    add_p("5. Klik tombol 'Simpan Produk'. Sistem akan menyimpan data dan mencatat audit log secara otomatis.")

    add_ss_placeholder(
        "Form Input Tambah Produk Digital Baru",
        "Buka browser -> Akses http://127.0.0.1:9091/products/create -> Take SS form create.",
        "Antarmuka Form Tambah Produk Baru"
    )

    add_h2("4.3 Mengubah Data Produk Digital (/products/{id}/edit)")
    add_p("1. Pada tabel katalog produk, klik tombol ikon pensil / 'Edit' pada baris produk yang ingin diubah.")
    add_p("2. Perbarui informasi seperti harga, stok, atau deskripsi produk pada form edit.")
    add_p("3. Klik tombol 'Perbarui Produk' untuk menyimpan perubahan.")

    add_ss_placeholder(
        "Form Edit Data Produk Digital",
        "Buka browser -> Halaman /products/{id}/edit -> Take SS form edit.",
        "Antarmuka Form Edit Produk Digital"
    )

    # BAB 5
    add_h1("5. Operasi Khusus Produk Digital (/operations/*)")

    add_h2("5.1 Penambahan Stok Produk (Restock Stok)")
    add_p("1. Pilih menu 'Operations' -> 'Restock Product' pada Sidebar.")
    add_p("2. Pilih produk yang akan ditambah stoknya dari daftar dropdown.")
    add_p("3. Masukkan Jumlah Stok Tambahan (bilangan bulat positif) dan Alasan Restock (misal: 'Aktivasi lisensi dari vendor').")
    add_p("4. Klik tombol 'Proses Restock'. Stok lama dan stok baru akan tercatat pada riwayat audit log.")

    add_ss_placeholder(
        "Halaman Operasi Restock Stok Produk Digital",
        "Buka browser -> Akses http://127.0.0.1:9091/operations/restock -> Take SS.",
        "Antarmuka Halaman Operasi Restock Stok"
    )

    add_h2("5.2 Soft Delete, Restore & Purge Permanen (Khusus Admin)")
    add_bullet(" Memindahkan produk dari katalog aktif ke Trash (menu /operations/delete).", "1. Soft Delete Produk:")
    add_bullet(" Mengembalikan produk dari Trash ke katalog aktif kembali (menu /operations/restore).", "2. Restore Produk:")
    add_bullet(" Menghapus produk secara permanen dari basis data (menu /operations/purge).", "3. Purge Permanen:")

    add_ss_placeholder(
        "Halaman Menu Operasi Soft Delete & Purge Produk",
        "Buka browser -> Halaman /operations/delete atau /operations/purge -> Take SS.",
        "Antarmuka Menu Operasi Hapus & Purge Produk"
    )

    # BAB 6
    add_h1("6. Pemantauan Audit Log Aktivitas User (/logs/user-activity)")
    add_p("1. Buka menu 'User Activity Logs' di Sidebar navigasi sebelah kiri.")
    add_p("2. Halaman ini menampilkan riwayat audit bisnis lengkap mencakup Waktu Transaksi, Event (ProductCreated, Restocked, Deleted, dll.), Nama Produk, User Executor, IP Address, dan Request ID.")
    add_p("3. Gunakan kolom pencarian log untuk melacak aktivitas tertentu berdasarkan nama user atau jenis event.")

    add_ss_placeholder(
        "Tampilan Halaman User Activity Logs",
        "Buka browser -> Akses http://127.0.0.1:9091/logs/user-activity -> Take SS.",
        "Antarmuka Log Aktivitas User (User Activity Logs)"
    )

    out_path = "/var/www/sertifikasi_manual/docs/Dokumentasi_Pengguna_Product_Management_Service.docx"
    doc.save(out_path)
    print(f"User Guide created at: {out_path}")

if __name__ == '__main__':
    build_technical_documentation()
    build_user_guide_documentation()
