# Aturan Akuntansi & Double-Entry (Accounting Rules)

## 1. Standar Chart of Accounts (COA)
Sistem menggunakan kode akun hierarki terstruktur:
- **1000 - ASET LANCAR**
  - `1110`: Kas Kasir / Kas Tunai Cabang
  - `1120`: Bank Utama / Giro Operasional
  - `1130`: Piutang Usaha (Accounts Receivable)
  - `1141`: Persediaan Bahan Baku (Raw Materials)
  - `1142`: Persediaan Barang Dalam Proses (WIP)
  - `1143`: Persediaan Produk Jadi (Finished Goods)
  - `1149`: Persediaan Dalam Perjalanan (Inventory In-Transit)
- **2000 - KEWAJIBAN / HUTANG**
  - `2110`: Hutang Usaha (Accounts Payable)
  - `2120`: Uang Muka Penjualan / Deposit Pelanggan
  - `2130`: Hutang Pajak (PPN Keluaran)
- **3000 - EKUITAS**
  - `3100`: Modal Disetor
  - `3200`: Laba Ditahan (Retained Earnings)
  - `3300`: Prive Pemilik
- **4000 - PENDAPATAN**
  - `4100`: Pendapatan Penjualan Bakery & Makanan
  - `4200`: Pendapatan Lain-lain
  - `4300`: Potongan Penjualan / Diskon (Contra-Revenue)
- **5000 - HARGA POKOK PENJUALAN (HPP / COGS)**
  - `5100`: HPP Produk Jadi Terjual
  - `5200`: Kerugian Kerusakan Bahan Baku & Produk Rusak
- **6000 - BEBAN OPERASIONAL**
  - `6100`: Beban Gaji & Upah
  - `6200`: Beban Listrik, Air, & Gas
  - `6300`: Beban Sewa Tempat
  - `6400`: Beban Pemeliharaan & Peralatan
  - `6500`: Beban Operasional Lainnya

## 2. Aturan Jurnal Transaksi Otomatis
Setiap posting transaksi dieksekusi secara otomatis oleh `AccountingService`:

### A. Pembelian Bahan Baku Tunai
- (Debit) `1141 - Persediaan Bahan Baku`
- (Kredit) `1110 / 1120 - Kas / Bank`

### B. Pembelian Bahan Baku Kredit (Tempo)
- (Debit) `1141 - Persediaan Bahan Baku`
- (Kredit) `2110 - Hutang Usaha`

### C. Pembayaran Hutang Supplier
- (Debit) `2110 - Hutang Usaha`
- (Kredit) `1110 / 1120 - Kas / Bank`

### D. Rilis Bahan Baku untuk Produksi (WIP)
- (Debit) `1142 - Persediaan Barang Dalam Proses (WIP)`
- (Kredit) `1141 - Persediaan Bahan Baku`

### E. Penyelesaian Produksi Menjadi Produk Jadi
- (Debit) `1143 - Persediaan Produk Jadi`
- (Kredit) `1142 - Persediaan Barang Dalam Proses (WIP)`
- *(Opsional alokasi overhead jika ada kas/upah langsung: Kredit Kas/Hutang Gaji)*

### F. Penjualan Tunai di POS
- **Pencatatan Penjualan:**
  - (Debit) `1110 - Kas Kasir / 1120 - Bank / QRIS`
  - (Kredit) `4100 - Pendapatan Penjualan`
- **Pencatatan HPP:**
  - (Debit) `5100 - HPP Produk Jadi Terjual`
  - (Kredit) `1143 - Persediaan Produk Jadi`

### G. Penjualan Kredit (Tempo)
- (Debit) `1130 - Piutang Usaha`
- (Kredit) `4100 - Pendapatan Penjualan`
*(HPP dicatat sama seperti F)*

### H. Pelunasan Piutang Pelanggan
- (Debit) `1110 / 1120 - Kas / Bank`
- (Kredit) `1130 - Piutang Usaha`

### I. Transfer Stok Antarcabang
- **Saat Pengiriman (Branch Asal):**
  - (Debit) `1149 - Persediaan Dalam Perjalanan (In-Transit)`
  - (Kredit) `1143 - Persediaan Produk Jadi (Cabang Asal)`
- **Saat Diterima (Branch Tujuan):**
  - (Debit) `1143 - Persediaan Produk Jadi (Cabang Tujuan)`
  - (Kredit) `1149 - Persediaan Dalam Perjalanan (In-Transit)`

## 3. Prinsip Keseimbangan & Immutabilitas
- Seluruh baris jurnal (`journal_lines`) wajib memenuhi syarat: $\sum \text{Debit} = \sum \text{Credit}$.
- Jurnal yang telah berstatus `posted` **dilarang keras untuk diedit langsung**. Koreksi wajib menggunakan mekanisme **Reversal Journal (Jurnal Pembalik)** dengan referensi ID jurnal asal.
