# Dokumen Arsitektur Sistem (Bakery ERP Pro)

## 1. Prinsip Desain Arsitektur
Aplikasi dibangun dengan prinsip **Clean Monolithic Architecture** dalam framework Laravel 12. Prinsip utama:
1. **Single Source of Truth**: Semua transaksi stok, piutang, hutang, dan keuangan disimpan dalam ledger (jurnal & stock movements) yang tidak dapat diubah seenaknya.
2. **Service Layer Pattern**: Logika bisnis kompleks (misal kalkulasi moving average, auto-journaling, atomic stock reservation) diisolasi dalam service classes terpisah di `app/Services/`. Controller hanya bertugas menangani HTTP validation dan payload forwarding.
3. **Database Concurrency & Atomicity**: Semua mutasi yang melibatkan multi-tabel dibungkus dalam `DB::transaction()` dengan pesimistic row locking (`lockForUpdate()`) untuk mencegah race condition dan stok minus.
4. **Data Isolation Multi-Tenant (Multi-Company & Multi-Branch)**: Setiap record transaksi memiliki `company_id` dan `branch_id`. Akses data diverifikasi pada layer middleware, service, dan Eloquent scope.

```
                    ┌──────────────────────────────────────────────┐
                    │               Client Layer                   │
                    │   (Blade + Livewire + Alpine.js + Tailwind)   │
                    └──────────────────────┬───────────────────────┘
                                           │ HTTP / AJAX
                                           ▼
                    ┌──────────────────────────────────────────────┐
                    │           Routing & Middleware               │
                    │ (AuthSession, JwtVerify, EnsureActiveBranch) │
                    └──────────────────────┬───────────────────────┘
                                           │
                                           ▼
                    ┌──────────────────────────────────────────────┐
                    │              Controller Layer                │
                    │   (Validation via FormRequest & Policies)    │
                    └──────────────────────┬───────────────────────┘
                                           │
                                           ▼
                    ┌──────────────────────────────────────────────┐
                    │               Service Layer                  │
                    │ ┌──────────────────────┐ ┌─────────────────┐ │
                    │ │ InventoryService     │ │ CostingService  │ │
                    │ ├──────────────────────┤ ├─────────────────┤ │
                    │ │ ProductionService    │ │ SaleService     │ │
                    │ ├──────────────────────┤ ├─────────────────┤ │
                    │ │ AccountingService    │ │ PurchaseService │ │
                    │ ├──────────────────────┤ ├─────────────────┤ │
                    │ │ StockTransferService │ │ SequenceService │ │
                    │ └──────────────────────┘ └─────────────────┘ │
                    └──────────────────────┬───────────────────────┘
                                           │ DB::transaction()
                                           ▼
                    ┌──────────────────────────────────────────────┐
                    │               Data Layer (ORM)               │
                    │         Eloquent Models & Relations          │
                    └──────────────────────┬───────────────────────┘
                                           │
                                           ▼
                    ┌──────────────────────────────────────────────┐
                    │             MySQL 8 / MariaDB                │
                    │ (InnoDB, Foreign Keys, Indexes, Constraints) │
                    └──────────────────────────────────────────────┘
```

## 2. Struktur Modul & Layanan
- **Company & Branch Management**: Menangani profil usaha dan unit cabang.
- **RBAC (Role-Based Access Control)**: Pengelolaan peran dan izin granular.
- **Sequence Generator**: Penomoran transaksi berbasis database lock untuk mencegah duplikasi nota.
- **Inventory & Costing**: Perhitungan Moving Average Costing, ledger stok per cabang, dan penanganan batch/kedaluwarsa.
- **Procurement**: Pembelian bahan baku, penerimaan bertahap, dan pencatatan hutang supplier.
- **Bill of Materials & Production**: Formulasi resep, kalkulasi kebutuhan bahan, dan pencatatan hasil produksi serta varians biaya.
- **Branch Stock Transfer**: Siklus transfer antar cabang dengan transit safety.
- **POS / Kasir**: Penjualan cepat, integrasi metode bayar multi-channel, dan pencatatan piutang.
- **Automated Double-Entry Accounting**: Pembuatan jurnal transaksi debit-kredit otomatis ke akun COA.
- **Reporting Engine**: Agregasi real-time untuk Cash Flow, Laba Rugi (P&L), Neraca (Balance Sheet), dan valuasi stok.
