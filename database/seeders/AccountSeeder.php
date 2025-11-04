<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Consumable Accounts
            [
                'account_number' => '6101',
                'account_description' => 'Consumable',
                'active_indicator' => true,
                'account_number_parent' => null,
                'account_level' => 1,
                'account_type' => 'expense',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'account_number' => '6102',
                'account_description' => 'Operational',
                'active_indicator' => true,
                'account_number_parent' => null,
                'account_level' => 1,
                'account_type' => 'expense',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('accounts')->insert($accounts);
    }
}
