<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Users & Branches
            ['name' => 'users.view', 'display_name' => 'Lihat Pengguna', 'group' => 'Pengguna'],
            ['name' => 'users.create', 'display_name' => 'Tambah Pengguna', 'group' => 'Pengguna'],
            ['name' => 'users.update', 'display_name' => 'Ubah Pengguna', 'group' => 'Pengguna'],
            ['name' => 'branches.view', 'display_name' => 'Lihat Cabang', 'group' => 'Cabang'],
            ['name' => 'branches.manage', 'display_name' => 'Kelola Cabang', 'group' => 'Cabang'],

            // Master Data
            ['name' => 'ingredients.view', 'display_name' => 'Lihat Bahan Baku', 'group' => 'Master Data'],
            ['name' => 'ingredients.manage', 'display_name' => 'Kelola Bahan Baku', 'group' => 'Master Data'],
            ['name' => 'products.view', 'display_name' => 'Lihat Produk Jadi', 'group' => 'Master Data'],
            ['name' => 'products.manage', 'display_name' => 'Kelola Produk Jadi', 'group' => 'Master Data'],
            ['name' => 'recipes.view', 'display_name' => 'Lihat Resep (BOM)', 'group' => 'Master Data'],
            ['name' => 'recipes.manage', 'display_name' => 'Kelola Resep (BOM)', 'group' => 'Master Data'],

            // Purchases
            ['name' => 'purchases.view', 'display_name' => 'Lihat Pembelian', 'group' => 'Pembelian'],
            ['name' => 'purchases.create', 'display_name' => 'Buat Pembelian', 'group' => 'Pembelian'],
            ['name' => 'purchases.approve', 'display_name' => 'Setujui Pembelian', 'group' => 'Pembelian'],
            ['name' => 'purchases.post', 'display_name' => 'Posting Penerimaan Pembelian', 'group' => 'Pembelian'],

            // Production
            ['name' => 'production.view', 'display_name' => 'Lihat Produksi', 'group' => 'Produksi'],
            ['name' => 'production.create', 'display_name' => 'Buat Order Produksi', 'group' => 'Produksi'],
            ['name' => 'production.post', 'display_name' => 'Posting Hasil Produksi', 'group' => 'Produksi'],

            // Inventory & Transfers
            ['name' => 'inventory.view', 'display_name' => 'Lihat Inventaris', 'group' => 'Inventaris'],
            ['name' => 'inventory.adjust', 'display_name' => 'Penyesuaian Stok (Opname)', 'group' => 'Inventaris'],
            ['name' => 'transfers.create', 'display_name' => 'Buat Transfer Antarcabang', 'group' => 'Transfer Stok'],
            ['name' => 'transfers.approve', 'display_name' => 'Setujui Transfer Antarcabang', 'group' => 'Transfer Stok'],
            ['name' => 'transfers.ship', 'display_name' => 'Kirim Transfer Stok', 'group' => 'Transfer Stok'],
            ['name' => 'transfers.receive', 'display_name' => 'Terima Transfer Stok', 'group' => 'Transfer Stok'],

            // Sales & POS
            ['name' => 'sales.view', 'display_name' => 'Lihat Penjualan', 'group' => 'Penjualan'],
            ['name' => 'sales.create', 'display_name' => 'Buat Penjualan (POS)', 'group' => 'Penjualan'],
            ['name' => 'sales.post', 'display_name' => 'Posting Penjualan', 'group' => 'Penjualan'],
            ['name' => 'sales.cancel', 'display_name' => 'Batalkan Penjualan', 'group' => 'Penjualan'],
            ['name' => 'payments.create', 'display_name' => 'Terima Pembayaran', 'group' => 'Penjualan'],

            // Finance & Accounting
            ['name' => 'expenses.manage', 'display_name' => 'Kelola Beban Pengeluaran', 'group' => 'Keuangan'],
            ['name' => 'accounting.view', 'display_name' => 'Lihat Jurnal & Buku Besar', 'group' => 'Akuntansi'],
            ['name' => 'accounting.post', 'display_name' => 'Posting Jurnal Manual', 'group' => 'Akuntansi'],
            ['name' => 'reports.branch', 'display_name' => 'Lihat Laporan Cabang', 'group' => 'Laporan'],
            ['name' => 'reports.consolidated', 'display_name' => 'Lihat Laporan Konsolidasi', 'group' => 'Laporan'],
            ['name' => 'settings.manage', 'display_name' => 'Kelola Pengaturan Sistem', 'group' => 'Pengaturan'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // Roles
        $rolesData = [
            ['name' => 'owner', 'display_name' => 'Pemilik Usaha (Owner)', 'description' => 'Akses penuh ke seluruh cabang dan laporan konsolidasi.'],
            ['name' => 'admin_pusat', 'display_name' => 'Administrator Pusat', 'description' => 'Akses administratif operasional kantor pusat.'],
            ['name' => 'branch_manager', 'display_name' => 'Manajer Cabang', 'description' => 'Mengawasi operasional dan keuangan cabang tertentu.'],
            ['name' => 'cashier', 'display_name' => 'Kasir POS', 'description' => 'Melayani transaksi penjualan toko dan cetak nota kasir.'],
            ['name' => 'warehouse_staff', 'display_name' => 'Staf Gudang', 'description' => 'Penerimaan barang dan stok opname.'],
            ['name' => 'production_staff', 'display_name' => 'Staf Produksi Bakery', 'description' => 'Menjalankan batch produksi dan resep.'],
            ['name' => 'procurement_staff', 'display_name' => 'Staf Pembelian', 'description' => 'Mengelola pesanan bahan baku ke supplier.'],
            ['name' => 'accounting_staff', 'display_name' => 'Staf Keuangan & Akunting', 'description' => 'Menangani kas, hutang, piutang, dan jurnal.'],
            ['name' => 'auditor', 'display_name' => 'Auditor (Read-Only)', 'description' => 'Hak akses pantau laporan dan log audit.'],
        ];

        foreach ($rolesData as $rd) {
            Role::updateOrCreate(['name' => $rd['name']], $rd);
        }

        // Assign Permissions to Roles
        $ownerRole = Role::where('name', 'owner')->first();
        $ownerRole->permissions()->sync(Permission::all());

        $adminPusat = Role::where('name', 'admin_pusat')->first();
        $adminPusat->permissions()->sync(Permission::all());

        $managerRole = Role::where('name', 'branch_manager')->first();
        $managerRole->permissions()->sync(
            Permission::whereIn('name', [
                'branches.view', 'ingredients.view', 'products.view', 'recipes.view',
                'purchases.view', 'purchases.create', 'production.view', 'production.create', 'production.post',
                'inventory.view', 'inventory.adjust', 'transfers.create', 'transfers.ship', 'transfers.receive',
                'sales.view', 'sales.create', 'sales.post', 'sales.cancel', 'payments.create',
                'expenses.manage', 'accounting.view', 'reports.branch'
            ])->get()
        );

        $cashierRole = Role::where('name', 'cashier')->first();
        $cashierRole->permissions()->sync(
            Permission::whereIn('name', [
                'products.view', 'sales.view', 'sales.create', 'sales.post', 'payments.create'
            ])->get()
        );

        $prodRole = Role::where('name', 'production_staff')->first();
        $prodRole->permissions()->sync(
            Permission::whereIn('name', [
                'ingredients.view', 'recipes.view', 'production.view', 'production.create', 'production.post', 'inventory.view'
            ])->get()
        );

        $whRole = Role::where('name', 'warehouse_staff')->first();
        $whRole->permissions()->sync(
            Permission::whereIn('name', [
                'ingredients.view', 'inventory.view', 'inventory.adjust', 'transfers.receive', 'purchases.view', 'purchases.post'
            ])->get()
        );
    }
}
