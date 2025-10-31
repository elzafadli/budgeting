<?php

namespace App\Http\Controllers;

use App\Models\RealisasiBudget;
use App\Models\RealisasiBudgetItem;
use App\Models\Budget;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealisasiBudgetController extends Controller
{
    public function index()
    {
        $realisasiBudgets = RealisasiBudget::with(['budget', 'user', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('realisasi_budgets.index', compact('realisasiBudgets'));
    }

    public function create(Request $request)
    {
        // Get budget_id from query parameter
        $budgetId = $request->query('budget_id');
        
        if (!$budgetId) {
            return redirect()->route('budgets.index')
                ->with('error', 'Budget ID is required.');
        }

        $budget = Budget::with(['items.account'])->findOrFail($budgetId);

        // Check if budget is completed
        if ($budget->status !== 'completed') {
            return redirect()->route('budgets.show', $budget)
                ->with('error', 'Only completed budgets can be realized.');
        }

        $accounts = Account::active()->orderBy('account_number')->get();

        return view('realisasi_budgets.form', compact('budget', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'realisasi_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.budget_item_id' => 'nullable|exists:budget_items,id',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate realisasi number
            $realisasiNo = RealisasiBudget::generateRealisasiNo();

            // Calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['total_price'];
            }

            // Create realisasi budget with auto-completed status
            $realisasiBudget = RealisasiBudget::create([
                'realisasi_no' => $realisasiNo,
                'budget_id' => $validated['budget_id'],
                'user_id' => auth()->id(),
                'realisasi_date' => $validated['realisasi_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
                'approved_total' => $totalAmount, // Auto-approve
                'status' => 'completed', // Auto-complete
            ]);

            // Create realisasi budget items
            foreach ($validated['items'] as $item) {
                RealisasiBudgetItem::create([
                    'realisasi_budget_id' => $realisasiBudget->id,
                    'budget_item_id' => $item['budget_item_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'total_price' => $item['total_price'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('realisasi-budgets.show', $realisasiBudget)
                ->with('success', 'Budget realization created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create budget realization: ' . $e->getMessage())->withInput();
        }
    }

    public function show(RealisasiBudget $realisasiBudget)
    {
        $realisasiBudget->load(['budget.project', 'user', 'items.account', 'items.budgetItem']);
        return view('realisasi_budgets.show', compact('realisasiBudget'));
    }

    public function edit(RealisasiBudget $realisasiBudget)
    {
        // Only allow editing draft realisasi
        if ($realisasiBudget->status !== 'draft') {
            return redirect()->route('realisasi-budgets.show', $realisasiBudget)
                ->with('error', 'Only draft realization can be edited.');
        }

        // Only allow owner to edit
        if ($realisasiBudget->user_id !== auth()->id()) {
            abort(403);
        }

        $budget = $realisasiBudget->budget()->with(['items.account'])->first();
        $accounts = Account::active()->orderBy('account_number')->get();
        
        return view('realisasi_budgets.form', compact('realisasiBudget', 'budget', 'accounts'));
    }

    public function update(Request $request, RealisasiBudget $realisasiBudget)
    {
        // Only allow updating draft realisasi
        if ($realisasiBudget->status !== 'draft') {
            return back()->with('error', 'Only draft realization can be updated.');
        }

        // Only allow owner to update
        if ($realisasiBudget->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'realisasi_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.budget_item_id' => 'nullable|exists:budget_items,id',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['total_price'];
            }

            // Update realisasi budget
            $realisasiBudget->update([
                'realisasi_date' => $validated['realisasi_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
                'approved_total' => $totalAmount,
            ]);

            // Delete old items and create new ones
            $realisasiBudget->items()->delete();
            foreach ($validated['items'] as $item) {
                RealisasiBudgetItem::create([
                    'realisasi_budget_id' => $realisasiBudget->id,
                    'budget_item_id' => $item['budget_item_id'] ?? null,
                    'account_id' => $item['account_id'],
                    'total_price' => $item['total_price'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('realisasi-budgets.show', $realisasiBudget)
                ->with('success', 'Budget realization updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update budget realization: ' . $e->getMessage())->withInput();
        }
    }
}
