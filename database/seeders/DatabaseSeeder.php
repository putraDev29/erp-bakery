<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CompanyAndBranchSeeder::class,
            UserSeeder::class,
            MasterDataSeeder::class,
            InitialStockSeeder::class,
        ]);
    }
}
