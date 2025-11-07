<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetFile;
use App\Models\BudgetApproval;
use App\Models\Project;
use App\Models\Account;
use App\Models\AccountBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BudgetController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $budgets = Budget::with(['user', 'items', 'approvals'])
            ->orderBy('created_at', 'desc')
            ->get();


        return view('budgets.index', compact('budgets'));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $accounts = Account::active()->orderBy('account_number')->get();
        $accountBanks = AccountBank::orderBy('bank_name')->get();
        $selectedProject = $request->query('project');

        return view('budgets.form', compact('projects', 'accounts', 'accountBanks', 'selectedProject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'account_from_id' => 'nullable|exists:accounts,id',
            'account_to' => 'nullable|string',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
            'files.*' => 'nullable|file|max:2040|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            // Generate request number
            $lastBudget = Budget::latest()->first();
            $requestNo = 'BDG-' . date('Ymd') . '-' . str_pad(($lastBudget ? $lastBudget->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['total_price'];
            }

            // Create budget
            $budget = Budget::create([
                'request_no' => $requestNo,
                'user_id' => auth()->id(),
                'project_id' => $validated['project_id'] ?? null,
                'account_from_id' => $validated['account_from_id'] ?? null,
                'account_to' => $validated['account_to'] ?? null,
                'document_date' => $validated['document_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
                'status' => 'draft',
            ]);

            // Create budget items
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'account_id' => $item['account_id'],
                    'total_price' => $item['total_price'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('budget_files', 'public');

                    BudgetFile::create([
                        'budget_id' => $budget->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('budgets.show', $budget)->with('success', 'Budget request created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create budget request: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Budget $budget)
    {
        $budget->load(['user', 'project', 'accountFrom', 'items.account', 'approvals.approver', 'realisasiBudgets', 'files']);
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        // Only allow editing draft or canceled budgets
        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return redirect()->route('budgets.show', $budget)
                ->with('error', 'Only draft or rejected budgets can be edited.');
        }

        // Only allow owner to edit
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $budget->load('files');
        $projects = Project::orderBy('name')->get();
        $accounts = Account::active()->orderBy('account_number')->get();
        $accountBanks = AccountBank::orderBy('bank_name')->get();
        return view('budgets.form', compact('budget', 'projects', 'accounts', 'accountBanks'));
    }

    public function update(Request $request, Budget $budget)
    {
        // Only allow updating draft or rejected budgets
        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected budgets can be updated.');
        }

        // Only allow owner to update
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'document_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'account_from_id' => 'nullable|exists:accounts,id',
            'account_to' => 'nullable|string',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
            'files.*' => 'nullable|file|max:2048|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['total_price'];
            }

            // Update budget
            $budget->update([
                'project_id' => $validated['project_id'] ?? null,
                'account_from_id' => $validated['account_from_id'] ?? null,
                'account_to' => $validated['account_to'] ?? null,
                'document_date' => $validated['document_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
            ]);

            // Delete old items and create new ones
            $budget->items()->delete();
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'account_id' => $item['account_id'],
                    'total_price' => $item['total_price'],
                    'remarks' => $item['remarks'] ?? null,
                ]);
            }

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->store('budget_files', 'public');

                    BudgetFile::create([
                        'budget_id' => $budget->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('budgets.show', $budget)->with('success', 'Budget request updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update budget request: ' . $e->getMessage())->withInput();
        }
    }

    public function submit(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        if ($budget->status !== 'draft') {
            return back()->with('error', 'Only draft budgets can be submitted.');
        }

        DB::beginTransaction();
        try {
            $budget->update(['status' => 'submitted']);

            // Create approval records
            BudgetApproval::create([
                'budget_id' => $budget->id,
                'role' => 'project_manager',
                'level' => 1,
                'status' => 'pending',
            ]);

            BudgetApproval::create([
                'budget_id' => $budget->id,
                'role' => 'finance',
                'level' => 2,
                'status' => 'pending',
            ]);

            DB::commit();
            return redirect()->route('budgets.show', $budget)->with('success', 'Budget request submitted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to submit budget request.');
        }
    }

    public function destroy(Budget $budget)
    {
        if ($budget->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($budget->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected budgets can be deleted.');
        }

        $budget->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget request deleted successfully.');
    }

    public function print(Budget $budget)
    {
        $budget->load(['user', 'project', 'accountFrom', 'items.account', 'approvals.approver']);
        return view('budgets.print', compact('budget'));
    }

    public function cashierEdit(Budget $budget)
    {
        // Only cashier can access this
        if (auth()->user()->role !== 'cashier') {
            abort(403);
        }

        // Only finance_approved budgets can be edited by cashier
        if ($budget->status !== 'finance_approved') {
            return redirect()->route('budgets.show', $budget)
                ->with('error', 'Only approved budgets can be processed by cashier.');
        }

        $budget->load('files');
        $projects = Project::orderBy('name')->get();
        $accounts = Account::active()->orderBy('account_number')->get();
        $accountBanks = AccountBank::orderBy('bank_name')->get();
        $isCashier = true;

        return view('budgets.form', compact('budget', 'projects', 'accounts', 'accountBanks', 'isCashier'));
    }

    public function cashierUpdate(Request $request, Budget $budget)
    {
        // Only cashier can access this
        if (auth()->user()->role !== 'cashier') {
            abort(403);
        }

        // Only finance_approved budgets can be updated by cashier
        if ($budget->status !== 'finance_approved') {
            return back()->with('error', 'Only approved budgets can be processed by cashier.');
        }

        $validated = $request->validate([
            'account_from_id' => 'required|exists:account_banks,id',
            'files.*' => 'nullable|file|max:2040|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
        ]);

        DB::beginTransaction();
        try {
            // Update account_from_id
            $budget->update([
                'account_from_id' => $validated['account_from_id'],
                'status' => 'completed',
            ]);

            // Create cashier approval record
            BudgetApproval::create([
                'budget_id' => $budget->id,
                'approver_id' => auth()->id(),
                'role' => 'cashier',
                'level' => 3, // Cashier is level 3 (after project_manager and finance)
                'status' => 'approved',
                'note' => 'Payment processed and completed',
                'approved_at' => now(),
            ]);

            // Handle file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('budget_files', $fileName, 'public');

                    BudgetFile::create([
                        'budget_id' => $budget->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('budgets.show', $budget)->with('success', 'Budget completed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to complete budget: ' . $e->getMessage())->withInput();
        }
    }
}
