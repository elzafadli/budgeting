<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetRealization;
use App\Models\BudgetRealizationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RealizationController extends Controller
{
    public function create(Budget $budget)
    {
        if ($budget->status !== 'finance_approved') {
            return back()->with('error', 'Only finance-approved budgets can have realizations.');
        }

        return view('realizations.create', compact('budget'));
    }

    public function store(Request $request, Budget $budget)
    {
        if ($budget->status !== 'finance_approved') {
            return back()->with('error', 'Only finance-approved budgets can have realizations.');
        }

        $validated = $request->validate([
            'realization_date' => 'required|date',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Generate realization number
            $lastRealization = BudgetRealization::latest()->first();
            $realizationNo = 'RLZ-' . date('Ymd') . '-' . str_pad(($lastRealization ? $lastRealization->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Calculate total
            $totalRealized = 0;
            foreach ($validated['items'] as $item) {
                $totalRealized += $item['amount'];
            }

            // Create realization
            $realization = BudgetRealization::create([
                'budget_id' => $budget->id,
                'realization_no' => $realizationNo,
                'realization_date' => $validated['realization_date'],
                'realized_by' => auth()->id(),
                'total_realized' => $totalRealized,
                'status' => 'completed',
                'note' => $validated['note'] ?? null,
            ]);

            // Create realization items
            foreach ($validated['items'] as $index => $item) {
                $proofFilePath = null;
                if (isset($item['proof_file'])) {
                    $file = $request->file("items.$index.proof_file");
                    $proofFilePath = $file->store('proof_files', 'public');
                }

                BudgetRealizationItem::create([
                    'realization_id' => $realization->id,
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                    'proof_file' => $proofFilePath,
                ]);
            }

            // Update budget status
            $budget->update(['status' => 'completed']);

            DB::commit();
            return redirect()->route('budgets.show', $budget)->with('success', 'Realization created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create realization: ' . $e->getMessage())->withInput();
        }
    }

    public function show(BudgetRealization $realization)
    {
        $realization->load(['budget', 'realizedByUser', 'items']);
        return view('realizations.show', compact('realization'));
    }
}
