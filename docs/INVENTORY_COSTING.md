# Metode Penilaian Persediaan & HPP (Inventory Costing)

## 1. Metode Biaya: Moving Weighted Average
Sesuai rekomendasi dan praktik terbaik usaha bakery, sistem menerapkan **Moving Weighted Average** per cabang dan per item:

$$\text{Biaya Rata-Rata Baru} = \frac{(\text{Stok Lama} \times \text{Biaya Rata-Rata Lama}) + (\text{Kuantitas Masuk} \times \text{Biaya Satuan Masuk})}{\text{Stok Lama} + \text{Kuantitas Masuk}}$$

### Aturan Perhitungan:
1. **Penerimaan Pembelian**:
   - Jika kuantitas bertambah melalui penerimaan barang (`purchase_receipt`), nilai rata-rata unit dihitung ulang berdasarkan total biaya perolehan (harga beli bersih + alokasi ongkos kirim).
2. **Pengeluaran untuk Produksi**:
   - Bahan baku yang dikeluarkan menggunakan biaya moving average saat transaksi order diproses.
3. **Penyelesaian Produk Jadi**:
   - Total biaya batch produksi (bahan aktual + tenaga kerja + overhead) dibagi total produk layak jual yang dihasilkan untuk memperoleh HPP per unit produk jadi.
   - Nilai ini kemudian memperbarui moving average produk jadi di cabang tersebut.
4. **Penjualan**:
   - Stok keluar dinilai menggunakan moving average produk jadi saat penjualan terjadi.
   - Nilai pengeluaran ini langsung didebit ke akun HPP (`5100`) dan dikredit ke Persediaan (`1143`).
5. **Transfer Antarcabang**:
   - Barang keluar dinilai sebesar moving average cabang pengirim.
   - Saat tiba di cabang tujuan, nilai tersebut diperlakukan sebagai kuantitas dan harga perolehan masuk bagi cabang tujuan untuk merata-ratakan biaya di lokasi tujuan.

## 2. Pencegahan Stok Negatif
- Sistem menolak transaksi pengurangan stok jika saldo tersedia di cabang kurang dari jumlah permintaan.
- Operasi pengecekan dan pengurangan stok selalu menggunakan pesimistik row locking:
  ```php
  $balance = StockBalance::where('branch_id', $branchId)
      ->where('item_type', $itemType)
      ->where('item_id', $itemId)
      ->lockForUpdate()
      ->first();
  ```
