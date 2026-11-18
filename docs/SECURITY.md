# Kebijakan Keamanan Sistem (Security Policy)

## 1. Autentikasi Dual-Layer (Web Session & JWT API)
1. **Web Interface**:
   - Berbasis Laravel Session yang aman dengan proteksi CSRF token di setiap form.
   - Session dilindungi cookie `HttpOnly`, `SameSite=Lax`, dan `Secure` (di production).
2. **API & Mobile Endpoints**:
   - Menggunakan JWT (JSON Web Token) dengan algoritma `HS256`.
   - Token payload memuat: `sub` (User ID), `company_id`, `branch_id`, `roles`, `iat`, `exp` (12 jam).
   - Verifikasi token dilakukan oleh middleware `JwtAuthMiddleware`.

## 2. Otorisasi & Akses Kontrol (RBAC)
- Setiap route dan controller action diproteksi oleh middleware permission dan Laravel Gate/Policies.
- Akses data dibatasi oleh Branch Context: Kasir tidak dapat melihat penjualan cabang lain, dan staf gudang hanya dapat mengakses stok di cabangnya.

## 3. Pencegahan Manipulasi Transaksi
- **Idempotency Key**: Diwajibkan pada endpoint POS dan transaksi posting untuk mencegah double-charge.
- **Backend Re-calculation**: Nilai subtotal, diskon, pajak, dan HPP selalu dihitung ulang di backend dari data master. Payload input klien yang memodifikasi total harga diabaikan.
- **Audit Logging**: Setiap aksi kritis (login, posting penjualan, posting produksi, penyesuaian stok, pengeluaran kas) dicatat dalam tabel `audit_logs` dengan User ID, IP Address, Branch, dan perubahan data.
