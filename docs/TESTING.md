# Panduan Pengujian (Testing Guide)

## 1. Strategi Pengujian
Pengujian mencakup:
1. **Unit Testing**:
   - Kalkulasi Moving Average Costing (`CostingServiceTest`).
   - Formula konversi satuan volume dan massa (`UnitConversionTest`).
   - Penomoran sekuensial transaksi atomik (`SequenceGeneratorTest`).
2. **Feature & Integration Testing**:
   - Isolasi Multi-Cabang (`BranchIsolationTest`).
   - Siklus Pembelian dan Hutang Usaha (`PurchaseAndPayableTest`).
   - Siklus Produksi, Pengurangan Bahan, dan Pengakuan Produk Jadi (`ProductionCycleTest`).
   - Siklus POS Penjualan, HPP, Kasir, dan Pengurangan Stok (`PosSaleTest`).
   - Keseimbangan Jurnal Debit-Kredit Double Entry (`AccountingBalancingTest`).
   - Transfer Stok Antarcabang dan Status In-Transit (`StockTransferTest`).

## 2. Cara Menjalankan Tes
Jalankan pengujian via PHPUnit:
```bash
php artisan test
```
atau secara spesifik:
```bash
php artisan test --filter=AccountingBalancingTest
```
