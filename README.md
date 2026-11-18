# Sistem Manajemen Usaha Makanan & Bakery Multi-Cabang (Bakery ERP Pro)

Sistem operasional dan akuntansi enterprise-grade untuk manajemen toko kue, bakery, katering, dan makanan multi-cabang dengan dukungan produksi terpusat & per-cabang, transfer stok transit, akuntansi double-entry terotomatisasi, kalkulasi HPP Moving Average, POS kasir responsif, dan audit log lengkap.

---

## 📌 Ringkasan Fitur Utama

1. **Multi-Company & Multi-Branch Architecture**:
   - Struktur hirarki: Perusahaan -> Banyak Cabang (Pusat, Cabang Toko/Outlet, Gudang Terpisah).
   - Isolasi data ketat berbasis Server-side session & JWT middleware, Policy, dan Scope.
   - Pilihan cabang aktif aman (Branch Switcher) dengan validasi kepemilikan akses.
   - Penomoran transaksi atomik berbasis DB Sequence lock (Contoh: `PUSAT-PB-20261010-0001`).

2. **Master Data & Konversi Satuan**:
   - Master Kategori (Bahan Baku, Produk Jadi, Biaya, dll).
   - Satuan multi-level (kg, gram, mg, liter, ml, pcs, box, loyang, butir) dengan tabel konversi presisi.
   - Bahan baku, Produk jadi, Supplier, dan Pelanggan.

3. **Manajemen Pembelian (Procurement)**:
   - Pemisahan status penerimaan barang (`draft`, `ordered`, `partially_received`, `received`) dan status pembayaran (`unpaid`, `partially_paid`, `paid`, `overdue`).
   - Pembelian tunai & tempo (Hutang Usaha / AP).
   - Pengalokasian biaya perolehan dan update nilai moving average cost secara instan.

4. **Resep & Bill of Materials (BOM)**:
   - Multi-versi resep per produk jadi.
   - Kalkulasi kebutuhan bahan dinamis (scaling kuantitas) & estimasi HPP per batch vs per unit.

5. **Manajemen Produksi & Batch**:
   - Order produksi per cabang/pusat.
   - Pemakaian bahan aktual vs resep standar (pencatatan varians bahan).
   - Alokasi biaya tenaga kerja langsung (DL) dan overhead pabrik (FOH).
   - Pencatatan produk jadi layak jual, produk rusak (waste/spoilage), dan produk sampel.

6. **Manajemen Persediaan & Transfer Stok**:
   - Kartu stok otomatis (Stock Ledger) dengan historical unit cost & moving average.
   - Transfer stok antarcabang dengan status In-Transit (pengurangan cabang asal saat kirim -> penerimaan fisik cabang tujuan -> jurnal transit otomatis).
   - Pencegahan stok negatif secara ketat (`DB row locking` & validation).

7. **Point of Sale (POS) & Penjualan**:
   - Antarmuka kasir cepat, pencarian instan, filter kategori, shortcut modal bayar.
   - Pembayaran tunai, transfer bank, QRIS, kredit/piutang pelanggan.
   - Pengurangan stok seketika, pengakuan HPP otomatis, cetak nota thermal (58mm, 80mm) dan browser print.

8. **Akuntansi Double-Entry & Keuangan**:
   - Chart of Accounts (COA) standar industri F&B dan Bakery.
   - Jurnal umum otomatis untuk pembelian, penerimaan, pemakaian bahan produksi, barang jadi, penjualan, HPP, kas/bank, dan penyesuaian.
   - Debit & Kredit selalu seimbang (atomik DB transaction).
   - Laporan Laba Rugi, Neraca, Arus Kas (Cash Flow), Nilai Persediaan, Mutasi Stok, dan Buku Besar.

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 12 (PHP 8.2+), Eloquent ORM, Service Layer, Form Requests, Policies.
- **Database**: MySQL 8 / MariaDB (InnoDB, Decimal 18,4 / 14,2, Foreign Keys, Indexing, Row Locking).
- **Frontend**: Blade, Livewire 3/4, Tailwind CSS, Alpine.js, Lucide Icons.
- **Keamanan & Autentikasi**: JWT (HMAC-SHA256) & Cookie/Session Guard, Role-Permission berbasis RBAC.
- **Dokumen**: Barryvdh/Laravel-DomPDF untuk cetak A4/PDF & CSS Thermal Receipt 58mm/80mm.

---

## 🚀 Panduan Instalasi Cepat

1. Pastikan PHP 8.2+, Composer, Node.js, dan MySQL berjalan.
2. Salin `.env.example` ke `.env` dan konfigurasikan database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bakery_erp
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan migrasi dan seeder data awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. Build aset frontend:
   ```bash
   npm run build
   ```
5. Jalankan server aplikasi:
   ```bash
   php artisan serve
   ```
6. Buka browser di `http://localhost:8000`.
   - Akun Owner: `owner@bakeryerp.local` / `password`
   - Akun Manajer Cabang: `manager.cbg1@bakeryerp.local` / `password`
   - Akun Kasir: `kasir.cbg1@bakeryerp.local` / `password`

---

## 📚 Dokumentasi Lengkap
Dokumentasi teknis mendalam tersedia di direktori `docs/`:
- [`ARCHITECTURE.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/ARCHITECTURE.md)
- [`ERD.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/ERD.md)
- [`BUSINESS_RULES.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/BUSINESS_RULES.md)
- [`ACCOUNTING_RULES.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/ACCOUNTING_RULES.md)
- [`INVENTORY_COSTING.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/INVENTORY_COSTING.md)
- [`MULTI_BRANCH.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/MULTI_BRANCH.md)
- [`SECURITY.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/SECURITY.md)
- [`TESTING.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/TESTING.md)
- [`DEPLOYMENT.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/DEPLOYMENT.md)
- [`IMPLEMENTATION_STATUS.md`](file:///C:/Users/WAHANA%2026/.gemini/antigravity-ide/scratch/bakery-erp/docs/IMPLEMENTATION_STATUS.md)
