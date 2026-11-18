# Arsitektur Multi-Cabang & Isolasi Data (Multi-Branch)

## 1. Konsep Multi-Cabang
Aplikasi mengusung satu entitas perusahaan dengan fleksibilitas ekspansi multi-cabang:
- **Pusat (Headquarters / Central Kitchen)**: Melakukan pembelian bahan baku skala besar, produksi batch utama, dan distribusi ke cabang.
- **Cabang Toko / Outlet**: Menerima transfer produk jadi atau bahan baku, menjalankan produksi segar harian di toko, dan melayani penjualan POS.

## 2. Struktur Akses & Keamanan Cabang
- Pengguna dihubungkan ke cabang melalui tabel `branch_users`.
- Role `owner` dan `admin_pusat` memiliki flag akses global ke seluruh cabang.
- Role `branch_manager`, `cashier`, `production_staff`, dan `warehouse_staff` hanya dapat melihat dan bertransaksi pada cabang yang di-assign secara spesifik.
- Sistem menyediakan komponen **Branch Switcher** di Topbar:
  - Mengubah branch aktif pengguna ke dalam session: `session(['active_branch_id' => $branchId])`.
  - Backend memvalidasi apakah pengguna memiliki hak akses sah ke `active_branch_id` tersebut.
  - Jika seorang pengguna mencoba memanipulasi form POST atau API dengan `branch_id` cabang lain, sistem akan memblokir dengan `403 Forbidden`.

## 3. Penomoran Dokumen Unik per Cabang
Format: `{KODE_CABANG}-{KODE_DOKUMEN}-{YYYYMMDD}-{NOMOR_URUT_4_DIGIT}`
- Pembelian: `PUSAT-PB-20261010-0001`
- Penjualan: `CBG001-PJ-20261010-0001`
- Produksi: `PUSAT-PR-20261010-0001`
- Transfer: `PUSAT-TR-20261010-0001`
- Kas Masuk: `CBG001-KM-20261010-0001`
- Kas Keluar: `CBG001-KK-20261010-0001`

Penomoran di-generate melalui tabel `branch_sequences` menggunakan database row lock (`lockForUpdate()`) agar bebas dari race conditions / nomor dobel saat jam sibuk transaksi kasir.
