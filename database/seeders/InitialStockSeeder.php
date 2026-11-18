<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class InitialStockSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $pusat = Branch::where('code', 'PUSAT')->first();
        $cbg1 = Branch::where('code', 'CBG001')->first();
        $user = User::where('email', 'owner@bakeryerp.local')->first();

        $inventoryService = app(InventoryService::class);
        $accountingService = app(AccountingService::class);

        // 1. Initial Ingredients at PUSAT (Central Kitchen)
        $initialIngredients = [
            'ING-TPG01' => ['qty' => 500, 'cost' => 14000],   // 500 kg
            'ING-MTG01' => ['qty' => 100, 'cost' => 95000],   // 100 kg
            'ING-GLA01' => ['qty' => 200, 'cost' => 17500],   // 200 kg
            'ING-RGI01' => ['qty' => 5000, 'cost' => 120],     // 5000 g
            'ING-TLR01' => ['qty' => 1200, 'cost' => 2200],   // 1200 butir
            'ING-SSU01' => ['qty' => 150, 'cost' => 21000],   // 150 liter
            'ING-CKL01' => ['qty' => 80, 'cost' => 65000],    // 80 kg
            'ING-KJU01' => ['qty' => 60, 'cost' => 85000],    // 60 kg
            'ING-BOX01' => ['qty' => 1500, 'cost' => 2500],   // 1500 pcs
        ];

        $totalRawValue = 0.0;
        foreach ($initialIngredients as $code => $data) {
            $ing = Ingredient::where('code', $code)->first();
            if ($ing) {
                // Check if already seeded
                $exist = StockBalance::where('branch_id', $pusat->id)
                    ->where('item_type', 'ingredient')
                    ->where('item_id', $ing->id)
                    ->exists();

                if (! $exist) {
                    $inventoryService->increaseStock(
                        $company->id,
                        $pusat->id,
                        'ingredient',
                        $ing->id,
                        $data['qty'],
                        $data['cost'],
                        'opening_balance',
                        null,
                        null,
                        'Saldo Awal Gudang Pusat',
                        $user->id
                    );
                }
                $totalRawValue += ($data['qty'] * $data['cost']);
            }
        }

        // 2. Initial Finished Goods at CBG001 (Menteng) & PUSAT
        $initialProducts = [
            'PRD-ROTI-SOBEK' => ['pusat' => 30, 'cbg1' => 25, 'cost' => 15000],
            'PRD-CROISSANT' => ['pusat' => 40, 'cbg1' => 30, 'cost' => 11000],
            'PRD-ROTI-ABON' => ['pusat' => 50, 'cbg1' => 40, 'cost' => 8500],
            'PRD-RED-VELVET' => ['pusat' => 15, 'cbg1' => 10, 'cost' => 22000],
        ];

        $totalFinishedValue = 0.0;
        foreach ($initialProducts as $code => $data) {
            $prd = Product::where('code', $code)->first();
            if ($prd) {
                // Pusat
                $existPusat = StockBalance::where('branch_id', $pusat->id)
                    ->where('item_type', 'product')
                    ->where('item_id', $prd->id)
                    ->exists();

                if (! $existPusat) {
                    $inventoryService->increaseStock(
                        $company->id,
                        $pusat->id,
                        'product',
                        $prd->id,
                        $data['pusat'],
                        $data['cost'],
                        'opening_balance',
                        null,
                        null,
                        'Saldo Awal Produk Jadi Pusat',
                        $user->id
                    );
                }

                // Cabang 1
                $existCbg1 = StockBalance::where('branch_id', $cbg1->id)
                    ->where('item_type', 'product')
                    ->where('item_id', $prd->id)
                    ->exists();

                if (! $existCbg1) {
                    $inventoryService->increaseStock(
                        $company->id,
                        $cbg1->id,
                        'product',
                        $prd->id,
                        $data['cbg1'],
                        $data['cost'],
                        'opening_balance',
                        null,
                        null,
                        'Saldo Awal Display Toko Menteng',
                        $user->id
                    );
                }

                $totalFinishedValue += (($data['pusat'] + $data['cbg1']) * $data['cost']);
            }
        }

        // 3. Opening Balance Journal
        // Sum total cash & bank accounts
        $totalCashAndBank = (float) Account::where('company_id', $company->id)->sum('current_balance');
        $totalAssets = $totalCashAndBank + $totalRawValue + $totalFinishedValue;

        // Check if opening journal already exists
        $hasOpeningJournal = \App\Models\JournalEntry::where('source_type', 'opening_balance')->exists();
        if (! $hasOpeningJournal) {
            $journalLines = [
                ['account_code' => '1110', 'debit' => 34000000, 'credit' => 0, 'description' => 'Saldo Kas Tunai Kasir & Toko'],
                ['account_code' => '1120', 'debit' => 150000000, 'credit' => 0, 'description' => 'Saldo Rekening Bank BCA'],
                ['account_code' => '1141', 'debit' => $totalRawValue, 'credit' => 0, 'description' => 'Saldo Persediaan Bahan Baku Awal'],
                ['account_code' => '1143', 'debit' => $totalFinishedValue, 'credit' => 0, 'description' => 'Saldo Persediaan Produk Jadi Awal'],
                ['account_code' => '3100', 'debit' => 0, 'credit' => (34000000 + 150000000 + $totalRawValue + $totalFinishedValue), 'description' => 'Modal Disetor Pemilik Usaha'],
            ];

            $accountingService->postJournal(
                $company->id,
                $pusat->id,
                'PUSAT-OB-' . now()->format('Ymd') . '-0001',
                now(),
                'Pencatatan Saldo Awal Kas, Bank, dan Nilai Persediaan Usaha',
                'opening_balance',
                $company->id,
                $journalLines,
                $user->id
            );
        }
    }
}
