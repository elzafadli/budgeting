<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetFile;
use App\Models\BudgetApproval;
use App\Models\Project;
use App\Models\Account;
use App\Models\AccountBank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class BudgetController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('budgets.index', compact('projects', 'users'));
    }

    public function getData(Request $request)
    {
        $query = Budget::with(['user', 'project'])
            ->select('budgets.*');

        // Apply project filter
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('project_id', $request->project_id);
        }

        // Apply requestor filter
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('budgets.status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('request_no', function ($budget) {
                return '<span class="small">' . $budget->request_no . '</span>';
            })
            ->addColumn('requestor', function ($budget) {
                return '<span class="small">' . ($budget->user ? $budget->user->name : '-') . '</span>';
            })
            ->addColumn('project', function ($budget) {
                return '<span class="small">' . ($budget->project ? $budget->project->name : '-') . '</span>';
            })
            ->addColumn('amount', function ($budget) {
                $currentUserRole = auth()->user()->role;
                $isCashierBudget = $budget->user && $budget->user->role === 'cashier';
                $canViewAmount = in_array($currentUserRole, ['cashier', 'finance']) || !$isCashierBudget;

                if ($canViewAmount) {
                    return '<span class="small text-end d-block">' . number_format($budget->total_amount, 0, ',', '.') . '</span>';
                } else {
                    return '<span class="small text-center d-block"><span class="badge bg-warning text-dark">Restricted</span></span>';
                }
            })
            ->addColumn('status', function ($budget) {
                $badges = [
                    'draft' => '<span class="badge bg-secondary">Draft</span>',
                    'submitted' => '<span class="badge bg-primary">Submitted</span>',
                    'pm_approved' => '<span class="badge bg-info">PM Approved</span>',
                    'finance_approved' => '<span class="badge bg-success">Finance Approved</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    'completed' => '<span class="badge bg-dark">Completed</span>',
                ];
                return '<span class="small">' . ($badges[$budget->status] ?? '<span class="badge bg-secondary">' . ucfirst($budget->status) . '</span>') . '</span>';
            })
            ->addColumn('date', function ($budget) {
                return '<span class="small">' . $budget->created_at->format('d M Y') . '</span>';
            })
            ->addColumn('action', function ($budget) {
                return '<span class="small"><a href="' . route('budgets.show', $budget) . '" class="btn btn-sm btn-outline-primary">View</a></span>';
            })
            ->rawColumns(['request_no', 'requestor', 'project', 'amount', 'status', 'date', 'action'])
            ->make(true);
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
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
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
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
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
        // Only allow updating draft, rejected, or canceled budgets
        if (!in_array($budget->status, ['draft', 'rejected', 'canceled'])) {
            return back()->with('error', 'Only draft, rejected, or canceled budgets can be updated.');
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
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
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
            $updateData = [
                'project_id' => $validated['project_id'] ?? null,
                'account_from_id' => $validated['account_from_id'] ?? null,
                'account_to' => $validated['account_to'] ?? null,
                'document_date' => $validated['document_date'],
                'description' => $validated['description'],
                'total_amount' => $totalAmount,
            ];

            // If budget was canceled, reset status to draft and delete approvals
            if ($budget->status === 'rejected') {
                $updateData['status'] = 'draft';
                // Delete old approvals to prevent duplicates
                $budget->approvals()->delete();
            }

            $budget->update($updateData);

            // Delete old items and create new ones
            $budget->items()->delete();
            foreach ($validated['items'] as $item) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'account_id' => $item['account_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
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
            /** @var \App\Models\User $user */
            $user = auth()->user();

            // Check if the user is a cashier
            if ($user->role === 'cashier') {
                // Cashier only needs finance approval
                $budget->update(['status' => 'submitted']);

                BudgetApproval::create([
                    'budget_id' => $budget->id,
                    'role' => 'finance',
                    'level' => 1,
                    'status' => 'pending',
                ]);
            } else {
                // Regular users need both project manager and finance approval
                $budget->update(['status' => 'submitted']);

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
            }

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

        // Generate PDF
        $pdf = Pdf::loadView('budgets.print', compact('budget'))
            ->setPaper('a4', 'portrait');

        // Download PDF with filename
        $filename = 'Budget_' . $budget->request_no . '_' . date('Ymd') . '.pdf';

        return $pdf->stream($filename);
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
