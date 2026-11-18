<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class CompanyAndBranchSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['name' => 'PT Roti & Kue Nusantara'],
            [
                'legal_name' => 'PT Roti & Kue Nusantara Sejahtera',
                'tax_id' => '01.234.567.8-012.000',
                'phone' => '021-5558899',
                'email' => 'corporate@bakeryerp.local',
                'address' => 'Jl. Sudirman Kav. 45, Jakarta Pusat',
                'currency' => 'IDR',
                'timezone' => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        $branchesData = [
            [
                'code' => 'PUSAT',
                'name' => 'Kantor Pusat & Central Kitchen',
                'is_central' => true,
                'phone' => '021-5558801',
                'email' => 'pusat@bakeryerp.local',
                'address' => 'Jl. Industri Bakery No. 1, Jakarta Timur',
                'is_active' => true,
            ],
            [
                'code' => 'CBG001',
                'name' => 'Cabang Menteng (Bakery & Cafe)',
                'is_central' => false,
                'phone' => '021-5558802',
                'email' => 'menteng@bakeryerp.local',
                'address' => 'Jl. HOS Cokroaminoto No. 24, Menteng, Jakarta Pusat',
                'is_active' => true,
            ],
            [
                'code' => 'CBG002',
                'name' => 'Cabang Senopati (Pastry & Coffee)',
                'is_central' => false,
                'phone' => '021-5558803',
                'email' => 'senopati@bakeryerp.local',
                'address' => 'Jl. Senopati No. 78, Kebayoran Baru, Jakarta Selatan',
                'is_active' => true,
            ],
        ];

        foreach ($branchesData as $bData) {
            $branch = Branch::firstOrCreate(
                ['company_id' => $company->id, 'code' => $bData['code']],
                array_merge($bData, ['company_id' => $company->id])
            );

            // Create default warehouse for each branch
            Warehouse::firstOrCreate(
                ['branch_id' => $branch->id, 'code' => 'GD-' . $branch->code],
                [
                    'company_id' => $company->id,
                    'name' => 'Gudang Utama ' . $branch->name,
                    'is_default' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
