# Aturan Bisnis (Business Rules)

## 1. Organisasi & Multi-Cabang
- **Kepemilikan Data**: Setiap data operasional wajib terikat pada `company_id` dan `branch_id`.
- **Branch Context Switching**: Pengguna dapat beralih cabang hanya jika memiliki relasi di tabel `branch_users` atau role `owner` / `superadmin`. Sesi aktif cabang disimpan di server-side session dan diotorisasi ulang pada setiap request.
- **Transaksi Kantor Pusat vs Cabang**: Pembelian pusat dicatat di cabang kantor pusat. Stok dapat didistribusikan ke cabang lain hanya melalui dokumen resmi `stock_transfers`.

## 2. Pengadaan (Procurement)
- **Pemisahan Pengiriman & Penagihan**: Barang fisik yang belum tiba (`purchase_receipts`) tidak boleh menambah stok inventaris.
- **Biaya Perolehan**: Biaya kirim/ekspedisi dan bea masuk yang dialokasikan ke pembelian akan meningkatkan nilai kapitalisasi persediaan (capitalized inventory cost).
- **Hutang Usaha**: Pembelian bertempo (`unpaid` / `partially_paid`) menciptakan saldo hutang supplier. Pembayaran hutang mengurangi kas/bank dan mengurangi saldo hutang tanpa mencatat beban baru.

## 3. Resep & Produksi
- **Immutabilitas Biaya Historis**: Perubahan formula resep tidak mengubah riwayat biaya batch produksi yang sudah diposting.
- **Varians Bahan**: Bahan yang dikeluarkan dicatat secara aktual. Jika resep standar memerlukan 10 kg tepung namun aktual menggunakan 10.5 kg, selisih 0.5 kg diakui dalam biaya batch aktual.
- **Waste & Spoilage**: Produk rusak/gagal dalam proses produksi tetap menyerap biaya bahan yang dikeluarkan, sehingga HPP produk jadi yang layak jual mencakup alokasi kerugian normal, atau dicatat sebagai beban kerugian produksi (Production Loss).

## 4. Penjualan & POS
- **Proteksi Stok Negatif**: Penjualan ditolak jika stok fisik produk jadi pada cabang bersangkutan tidak mencukupi.
- **Idempotency**: Setiap checkout POS menyertakan idempotency key untuk mencegah duplicate invoice akibat double-click atau network timeout.
- **Harga Jual & Diskon**: Validasi harga selalu dilakukan di backend berdasarkan harga master (atau harga override cabang), diskon item, dan diskon promo yang valid.
