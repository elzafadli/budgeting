<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfitLossSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear example data (use delete to keep FK integrity safer)
        DB::table('budget_items')->delete();
        DB::table('accounts')->delete();

        $accountsData = [
            ['account_number' => '1', 'account_description' => 'Pendapatan', 'account_number_parent' => null, 'account_type' => 'revenue'],
            ['account_number' => '1.1', 'account_description' => 'Pendapatan Operasional', 'account_number_parent' => '1', 'account_type' => 'revenue'],
            ['account_number' => '1.1.1', 'account_description' => 'Penjualan Produk', 'account_number_parent' => '1.1', 'account_type' => 'revenue'],
            ['account_number' => '2', 'account_description' => 'Beban', 'account_number_parent' => null, 'account_type' => 'expense'],
            ['account_number' => '2.1', 'account_description' => 'Beban Operasional', 'account_number_parent' => '2', 'account_type' => 'expense'],
            ['account_number' => '2.1.1', 'account_description' => 'Gaji', 'account_number_parent' => '2.1', 'account_type' => 'expense'],
        ];

        $idByNumber = [];
        foreach ($accountsData as $acc) {
            $id = DB::table('accounts')->insertGetId([
                'account_number' => $acc['account_number'],
                'account_description' => $acc['account_description'],
                'active_indicator' => true,
                'account_number_parent' => $acc['account_number_parent'],
                'account_level' => substr_count($acc['account_number'], '.') + 1,
                'account_type' => $acc['account_type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $idByNumber[$acc['account_number']] = $id;
        }

        $budgetItems = [
            ['budget_id' => 1, 'account_number' => '1.1.1', 'total_price' => 10000000, 'remarks' => 'Penjualan A'],
            ['budget_id' => 1, 'account_number' => '2.1.1', 'total_price' => 4000000, 'remarks' => 'Gaji Oktober'],
            ['budget_id' => 1, 'account_number' => '2.1.1', 'total_price' => 1000000, 'remarks' => 'Gaji Bonus'],
        ];

        foreach ($budgetItems as $item) {
            DB::table('budget_items')->insert([
                'budget_id' => $item['budget_id'],
                'account_id' => $idByNumber[$item['account_number']] ?? null,
                'total_price' => $item['total_price'],
                'remarks' => $item['remarks'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}


