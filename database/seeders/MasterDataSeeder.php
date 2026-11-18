<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $pusat = Branch::where('code', 'PUSAT')->first();
        $cbg1 = Branch::where('code', 'CBG001')->first();
        $cbg2 = Branch::where('code', 'CBG002')->first();

        // 1. Units
        $units = [
            ['name' => 'Kilogram', 'symbol' => 'kg', 'type' => 'mass'],
            ['name' => 'Gram', 'symbol' => 'g', 'type' => 'mass'],
            ['name' => 'Miligram', 'symbol' => 'mg', 'type' => 'mass'],
            ['name' => 'Liter', 'symbol' => 'l', 'type' => 'volume'],
            ['name' => 'Mililiter', 'symbol' => 'ml', 'type' => 'volume'],
            ['name' => 'Pieces', 'symbol' => 'pcs', 'type' => 'count'],
            ['name' => 'Butir', 'symbol' => 'butir', 'type' => 'count'],
            ['name' => 'Box Kotak', 'symbol' => 'box', 'type' => 'count'],
            ['name' => 'Loyang', 'symbol' => 'loyang', 'type' => 'count'],
        ];

        $unitMap = [];
        foreach ($units as $u) {
            $created = Unit::updateOrCreate(
                ['company_id' => $company->id, 'symbol' => $u['symbol']],
                array_merge($u, ['company_id' => $company->id])
            );
            $unitMap[$u['symbol']] = $created;
        }

        // Unit conversions
        UnitConversion::updateOrCreate(
            ['company_id' => $company->id, 'from_unit_id' => $unitMap['kg']->id, 'to_unit_id' => $unitMap['g']->id],
            ['multiplier' => 1000]
        );
        UnitConversion::updateOrCreate(
            ['company_id' => $company->id, 'from_unit_id' => $unitMap['g']->id, 'to_unit_id' => $unitMap['mg']->id],
            ['multiplier' => 1000]
        );
        UnitConversion::updateOrCreate(
            ['company_id' => $company->id, 'from_unit_id' => $unitMap['l']->id, 'to_unit_id' => $unitMap['ml']->id],
            ['multiplier' => 1000]
        );

        // 2. Categories
        $categories = [
            ['name' => 'Tepung & Bahan Kering', 'type' => 'ingredient', 'description' => 'Bahan baku kering dasar roti'],
            ['name' => 'Mentega, Susu & Dairy', 'type' => 'ingredient', 'description' => 'Produk susu dan olahan lemak'],
            ['name' => 'Topping & Isian', 'type' => 'ingredient', 'description' => 'Cokelat, keju, selai, dan pasta'],
            ['name' => 'Kemasan & Packaging', 'type' => 'ingredient', 'description' => 'Kardus, plastik pembungkus, mika'],
            ['name' => 'Roti Manis & Sobek', 'type' => 'product', 'description' => 'Aneka roti manis isi dan sobek'],
            ['name' => 'Croissant & Pastry', 'type' => 'product', 'description' => 'Viennoiserie dan puff pastry renyah'],
            ['name' => 'Cake & Tart', 'type' => 'product', 'description' => 'Kue basah, bolu, dan slice cake'],
            ['name' => 'Beban Operasional Toko', 'type' => 'expense', 'description' => 'Listrik, air, perlengkapan kasir'],
        ];

        $catMap = [];
        foreach ($categories as $c) {
            $created = Category::updateOrCreate(
                ['company_id' => $company->id, 'name' => $c['name']],
                array_merge($c, ['company_id' => $company->id])
            );
            $catMap[$c['name']] = $created;
        }

        // 3. Chart of Accounts (COA)
        $coaList = [
            // Assets
            ['code' => '1110', 'name' => 'Kas Tunai & Kasir', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1120', 'name' => 'Bank BCA Operasional', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1121', 'name' => 'Bank Mandiri', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1130', 'name' => 'Piutang Usaha (AR)', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1141', 'name' => 'Persediaan Bahan Baku', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1142', 'name' => 'Persediaan Dalam Proses (WIP)', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1143', 'name' => 'Persediaan Produk Jadi', 'type' => 'asset', 'normal_balance' => 'debit'],
            ['code' => '1149', 'name' => 'Persediaan Dalam Perjalanan (In-Transit)', 'type' => 'asset', 'normal_balance' => 'debit'],
            // Liabilities
            ['code' => '2110', 'name' => 'Hutang Usaha (AP)', 'type' => 'liability', 'normal_balance' => 'credit'],
            ['code' => '2130', 'name' => 'Hutang Pajak / PPN', 'type' => 'liability', 'normal_balance' => 'credit'],
            // Equity
            ['code' => '3100', 'name' => 'Modal Pemilik', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '3200', 'name' => 'Laba Ditahan', 'type' => 'equity', 'normal_balance' => 'credit'],
            ['code' => '3300', 'name' => 'Prive Pemilik', 'type' => 'equity', 'normal_balance' => 'debit'],
            // Revenue
            ['code' => '4100', 'name' => 'Pendapatan Penjualan Bakery', 'type' => 'revenue', 'normal_balance' => 'credit'],
            ['code' => '4200', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'normal_balance' => 'credit'],
            // COGS
            ['code' => '5100', 'name' => 'Harga Pokok Penjualan (HPP)', 'type' => 'cogs', 'normal_balance' => 'debit'],
            ['code' => '5200', 'name' => 'Biaya Kerusakan & Waste Produksi', 'type' => 'cogs', 'normal_balance' => 'debit'],
            // Expenses
            ['code' => '6100', 'name' => 'Beban Gaji & Upah Karyawan', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '6200', 'name' => 'Beban Listrik, Air, & Gas', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '6300', 'name' => 'Beban Sewa Outlet', 'type' => 'expense', 'normal_balance' => 'debit'],
            ['code' => '6500', 'name' => 'Beban Operasional Lain-lain', 'type' => 'expense', 'normal_balance' => 'debit'],
        ];

        $coaMap = [];
        foreach ($coaList as $coa) {
            $created = ChartOfAccount::updateOrCreate(
                ['company_id' => $company->id, 'code' => $coa['code']],
                array_merge($coa, ['company_id' => $company->id])
            );
            $coaMap[$coa['code']] = $created;
        }

        // 4. Accounts (Kas & Bank)
        Account::updateOrCreate(
            ['company_id' => $company->id, 'code' => 'KAS-PUSAT'],
            [
                'branch_id' => $pusat->id,
                'name' => 'Kas Operasional Pusat',
                'type' => 'cash',
                'current_balance' => 25000000,
                'chart_of_account_id' => $coaMap['1110']->id,
                'is_active' => true,
            ]
        );

        Account::updateOrCreate(
            ['company_id' => $company->id, 'code' => 'KAS-CBG1'],
            [
                'branch_id' => $cbg1->id,
                'name' => 'Kas Kasir Menteng',
                'type' => 'cash',
                'current_balance' => 5000000,
                'chart_of_account_id' => $coaMap['1110']->id,
                'is_active' => true,
            ]
        );

        Account::updateOrCreate(
            ['company_id' => $company->id, 'code' => 'KAS-CBG2'],
            [
                'branch_id' => $cbg2->id,
                'name' => 'Kas Kasir Senopati',
                'type' => 'cash',
                'current_balance' => 4000000,
                'chart_of_account_id' => $coaMap['1110']->id,
                'is_active' => true,
            ]
        );

        Account::updateOrCreate(
            ['company_id' => $company->id, 'code' => 'BANK-BCA'],
            [
                'branch_id' => $pusat->id,
                'name' => 'Bank BCA PT Roti Nusantara',
                'type' => 'bank',
                'account_number' => '5420991823',
                'current_balance' => 150000000,
                'chart_of_account_id' => $coaMap['1120']->id,
                'is_active' => true,
            ]
        );

        // Expense Categories
        ExpenseCategory::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Listrik, Air & Gas'],
            ['chart_of_account_id' => $coaMap['6200']->id]
        );
        ExpenseCategory::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Gaji & Uang Lembur'],
            ['chart_of_account_id' => $coaMap['6100']->id]
        );
        ExpenseCategory::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Sewa Gedung Cabang'],
            ['chart_of_account_id' => $coaMap['6300']->id]
        );

        // 5. Suppliers
        $suppliers = [
            ['code' => 'SUP001', 'name' => 'PT Bogasari Flour Mills', 'phone' => '021-4390001', 'address' => 'Tanjung Priok, Jakarta'],
            ['code' => 'SUP002', 'name' => 'PT Megah Aneka Mentega', 'phone' => '021-7788991', 'address' => 'Kawasan Industri Pulogadung'],
            ['code' => 'SUP003', 'name' => 'CV Sumber Telur Segar', 'phone' => '0251-832100', 'address' => 'Ciawi, Bogor'],
            ['code' => 'SUP004', 'name' => 'PT Packaging Box Unggul', 'phone' => '021-5901122', 'address' => 'Cikupa, Tangerang'],
        ];

        foreach ($suppliers as $s) {
            Supplier::updateOrCreate(
                ['company_id' => $company->id, 'code' => $s['code']],
                array_merge($s, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // 6. Customers
        $customers = [
            ['code' => 'CUST-GENERAL', 'name' => 'Pelanggan Umum (Walk-in)', 'phone' => '-', 'address' => '-'],
            ['code' => 'CUST001', 'name' => 'Hotel Santika Premiere (B2B)', 'phone' => '021-5361234', 'credit_limit' => 20000000, 'address' => 'Slipi, Jakarta Barat'],
            ['code' => 'CUST002', 'name' => 'Ibu Ratna (Langganan Arisan)', 'phone' => '081299887766', 'credit_limit' => 5000000, 'address' => 'Menteng, Jakarta Pusat'],
        ];

        foreach ($customers as $cust) {
            Customer::updateOrCreate(
                ['company_id' => $company->id, 'code' => $cust['code']],
                array_merge($cust, ['company_id' => $company->id, 'is_active' => true])
            );
        }

        // 7. Ingredients
        $ingredients = [
            ['code' => 'ING-TPG01', 'name' => 'Tepung Cakra Kembar Premium', 'category' => 'Tepung & Bahan Kering', 'unit' => 'kg', 'min_stock' => 50, 'cost' => 14000],
            ['code' => 'ING-MTG01', 'name' => 'Mentega Anchor Unsalted', 'category' => 'Mentega, Susu & Dairy', 'unit' => 'kg', 'min_stock' => 20, 'cost' => 95000],
            ['code' => 'ING-GLA01', 'name' => 'Gula Pasir Kristal Putih', 'category' => 'Tepung & Bahan Kering', 'unit' => 'kg', 'min_stock' => 30, 'cost' => 17500],
            ['code' => 'ING-RGI01', 'name' => 'Ragi Instan Saf-Instant', 'category' => 'Tepung & Bahan Kering', 'unit' => 'g', 'min_stock' => 500, 'cost' => 120],
            ['code' => 'ING-TLR01', 'name' => 'Telur Ayam Negeri Segar', 'category' => 'Tepung & Bahan Kering', 'unit' => 'butir', 'min_stock' => 100, 'cost' => 2200],
            ['code' => 'ING-SSU01', 'name' => 'Susu Segar Pasteurisasi', 'category' => 'Mentega, Susu & Dairy', 'unit' => 'l', 'min_stock' => 25, 'cost' => 21000],
            ['code' => 'ING-CKL01', 'name' => 'Cokelat Meises Ceres', 'category' => 'Topping & Isian', 'unit' => 'kg', 'min_stock' => 15, 'cost' => 65000],
            ['code' => 'ING-KJU01', 'name' => 'Keju Cheddar Olahan Prochiz', 'category' => 'Topping & Isian', 'unit' => 'kg', 'min_stock' => 15, 'cost' => 85000],
            ['code' => 'ING-BOX01', 'name' => 'Kotak Roti Box Exclusive', 'category' => 'Kemasan & Packaging', 'unit' => 'pcs', 'min_stock' => 200, 'cost' => 2500],
        ];

        $ingMap = [];
        foreach ($ingredients as $ing) {
            $created = Ingredient::updateOrCreate(
                ['company_id' => $company->id, 'code' => $ing['code']],
                [
                    'company_id' => $company->id,
                    'name' => $ing['name'],
                    'category_id' => $catMap[$ing['category']]->id,
                    'unit_id' => $unitMap[$ing['unit']]->id,
                    'min_stock' => $ing['min_stock'],
                    'cost_price' => $ing['cost'],
                    'is_active' => true,
                ]
            );
            $ingMap[$ing['code']] = $created;
        }

        // 8. Products
        $products = [
            ['code' => 'PRD-ROTI-SOBEK', 'barcode' => '8991001001', 'name' => 'Roti Sobek Cokelat Keju', 'category' => 'Roti Manis & Sobek', 'unit' => 'pcs', 'price' => 32000, 'min_stock' => 20],
            ['code' => 'PRD-CROISSANT', 'barcode' => '8991001002', 'name' => 'Butter Croissant Klasik', 'category' => 'Croissant & Pastry', 'unit' => 'pcs', 'price' => 28000, 'min_stock' => 15],
            ['code' => 'PRD-ROTI-ABON', 'barcode' => '8991001003', 'name' => 'Roti Manis Floss Abon Sapi', 'category' => 'Roti Manis & Sobek', 'unit' => 'pcs', 'price' => 18000, 'min_stock' => 25],
            ['code' => 'PRD-RED-VELVET', 'barcode' => '8991001004', 'name' => 'Red Velvet Cake Slice', 'category' => 'Cake & Tart', 'unit' => 'pcs', 'price' => 45000, 'min_stock' => 10],
        ];

        $prdMap = [];
        foreach ($products as $prd) {
            $created = Product::updateOrCreate(
                ['company_id' => $company->id, 'code' => $prd['code']],
                [
                    'company_id' => $company->id,
                    'barcode' => $prd['barcode'],
                    'name' => $prd['name'],
                    'category_id' => $catMap[$prd['category']]->id,
                    'unit_id' => $unitMap[$prd['unit']]->id,
                    'sale_price' => $prd['price'],
                    'min_stock' => $prd['min_stock'],
                    'is_active' => true,
                ]
            );
            $prdMap[$prd['code']] = $created;
        }

        // 9. Recipes (BOM)
        // Resep Roti Sobek (Yield 10 pcs)
        $recipe1 = Recipe::updateOrCreate(
            ['product_id' => $prdMap['PRD-ROTI-SOBEK']->id, 'version' => '1.0'],
            [
                'company_id' => $company->id,
                'name' => 'Resep Roti Sobek Cokelat Keju V1',
                'yield_quantity' => 10,
                'yield_unit_id' => $unitMap['pcs']->id,
                'standard_cost' => 125000,
                'labor_cost' => 15000,
                'overhead_cost' => 10000,
                'prep_time_minutes' => 120,
                'instructions' => '1. Aduk tepung, ragi, gula, susu hingga kalis.\n2. Masukkan mentega dan garam, uleni elastis.\n3. Fermentasi 45 menit.\n4. Timbang @50g, isi cokelat dan keju, tata di loyang.\n5. Panggang 180C selama 25 menit.',
                'is_active' => true,
            ]
        );

        $recipe1->items()->delete();
        $recipe1->items()->createMany([
            ['ingredient_id' => $ingMap['ING-TPG01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 1.0, 'standard_cost' => 14000],
            ['ingredient_id' => $ingMap['ING-MTG01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.2, 'standard_cost' => 19000],
            ['ingredient_id' => $ingMap['ING-GLA01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.15, 'standard_cost' => 2625],
            ['ingredient_id' => $ingMap['ING-RGI01']->id, 'unit_id' => $unitMap['g']->id, 'quantity' => 15, 'standard_cost' => 1800],
            ['ingredient_id' => $ingMap['ING-TLR01']->id, 'unit_id' => $unitMap['butir']->id, 'quantity' => 2, 'standard_cost' => 4400],
            ['ingredient_id' => $ingMap['ING-SSU01']->id, 'unit_id' => $unitMap['l']->id, 'quantity' => 0.3, 'standard_cost' => 6300],
            ['ingredient_id' => $ingMap['ING-CKL01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.2, 'standard_cost' => 13000],
            ['ingredient_id' => $ingMap['ING-KJU01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.2, 'standard_cost' => 17000],
            ['ingredient_id' => $ingMap['ING-BOX01']->id, 'unit_id' => $unitMap['pcs']->id, 'quantity' => 10, 'standard_cost' => 25000],
        ]);

        // Resep Butter Croissant (Yield 12 pcs)
        $recipe2 = Recipe::updateOrCreate(
            ['product_id' => $prdMap['PRD-CROISSANT']->id, 'version' => '1.0'],
            [
                'company_id' => $company->id,
                'name' => 'Resep Butter Croissant French Style V1',
                'yield_quantity' => 12,
                'yield_unit_id' => $unitMap['pcs']->id,
                'standard_cost' => 95000,
                'labor_cost' => 20000,
                'overhead_cost' => 15000,
                'prep_time_minutes' => 240,
                'instructions' => '1. Campur tepung, gula, ragi, susu dingin.\n2. Istirahatkan adonan di chiller.\n3. Lakukan laminasi mentega tawar 3 kali single fold.\n4. Potong segitiga dan gulung.\n5. Proofing 2 jam pada 26C, panggang 200C selama 18 menit.',
                'is_active' => true,
            ]
        );

        $recipe2->items()->delete();
        $recipe2->items()->createMany([
            ['ingredient_id' => $ingMap['ING-TPG01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.8, 'standard_cost' => 11200],
            ['ingredient_id' => $ingMap['ING-MTG01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.45, 'standard_cost' => 42750],
            ['ingredient_id' => $ingMap['ING-GLA01']->id, 'unit_id' => $unitMap['kg']->id, 'quantity' => 0.1, 'standard_cost' => 1750],
            ['ingredient_id' => $ingMap['ING-RGI01']->id, 'unit_id' => $unitMap['g']->id, 'quantity' => 10, 'standard_cost' => 1200],
            ['ingredient_id' => $ingMap['ING-SSU01']->id, 'unit_id' => $unitMap['l']->id, 'quantity' => 0.25, 'standard_cost' => 5250],
        ]);
    }
}
