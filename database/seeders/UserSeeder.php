<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $pusat = Branch::where('code', 'PUSAT')->first();
        $cbg1 = Branch::where('code', 'CBG001')->first();
        $cbg2 = Branch::where('code', 'CBG002')->first();

        $users = [
            [
                'name' => 'Bapak Hartono (Owner)',
                'email' => 'owner@bakeryerp.local',
                'phone' => '081122334455',
                'role' => 'owner',
                'branches' => [$pusat->id, $cbg1->id, $cbg2->id],
                'default_branch' => $pusat->id,
            ],
            [
                'name' => 'Siti Rahma (Admin Pusat)',
                'email' => 'admin.pusat@bakeryerp.local',
                'phone' => '081233445566',
                'role' => 'admin_pusat',
                'branches' => [$pusat->id],
                'default_branch' => $pusat->id,
            ],
            [
                'name' => 'Budi Santoso (Manajer Menteng)',
                'email' => 'manager.cbg1@bakeryerp.local',
                'phone' => '081344556677',
                'role' => 'branch_manager',
                'branches' => [$cbg1->id],
                'default_branch' => $cbg1->id,
            ],
            [
                'name' => 'Dewi Anggraini (Kasir Menteng)',
                'email' => 'kasir.cbg1@bakeryerp.local',
                'phone' => '081455667788',
                'role' => 'cashier',
                'branches' => [$cbg1->id],
                'default_branch' => $cbg1->id,
            ],
            [
                'name' => 'Agus Baker (Kepala Produksi)',
                'email' => 'produksi.pusat@bakeryerp.local',
                'phone' => '081566778899',
                'role' => 'production_staff',
                'branches' => [$pusat->id],
                'default_branch' => $pusat->id,
            ],
            [
                'name' => 'Eko Prasetyo (Gudang Pusat)',
                'email' => 'gudang.pusat@bakeryerp.local',
                'phone' => '081677889900',
                'role' => 'warehouse_staff',
                'branches' => [$pusat->id],
                'default_branch' => $pusat->id,
            ],
        ];

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'company_id' => $company->id,
                    'name' => $u['name'],
                    'phone' => $u['phone'],
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );

            // Assign role
            $role = Role::where('name', $u['role'])->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            // Assign branch
            $branchData = [];
            foreach ($u['branches'] as $bId) {
                $branchData[$bId] = ['is_default' => ($bId === $u['default_branch'])];
            }
            $user->branches()->sync($branchData);
        }
    }
}
