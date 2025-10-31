<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index()
    {
        $rows = DB::table('accounts')
            ->leftJoin('budget_items', 'accounts.id', '=', 'budget_items.account_id')
            ->select('accounts.id', 'accounts.account_number', 'accounts.account_description', 'accounts.account_number_parent', DB::raw('COALESCE(SUM(budget_items.total_price),0) as amount'))
            ->groupBy('accounts.id', 'accounts.account_number', 'accounts.account_description', 'accounts.account_number_parent')
            ->orderBy('accounts.account_number')
            ->get();

        $nodes = [];
        foreach ($rows as $r) {
            $nodes[$r->account_number] = [
                'id' => $r->id,
                'account_number' => $r->account_number,
                'description' => $r->account_description,
                'parent' => $r->account_number_parent,
                'amount' => (float) $r->amount,
                'children' => [],
            ];
        }

        $tree = [];
        foreach ($nodes as $num => &$node) {
            $parent = $node['parent'];
            if ($parent && isset($nodes[$parent])) {
                $nodes[$parent]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node);

        $accumulate = function (&$node) use (&$accumulate) {
            $sum = $node['amount'];
            foreach ($node['children'] as &$child) {
                $sum += $accumulate($child);
            }
            $node['amount_total'] = $sum;
            return $sum;
        };

        $grandTotal = 0;
        foreach ($tree as &$n) {
            $grandTotal += $accumulate($n);
        }

        return view('reports.profit_loss', compact('tree', 'grandTotal'));
    }
}
