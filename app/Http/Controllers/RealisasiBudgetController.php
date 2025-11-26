<?php

namespace App\Http\Controllers;

use App\Models\RealisasiBudget;
use App\Models\RealisasiBudgetItem;
use App\Models\RealisasiBudgetFile;
use App\Models\Budget;
use App\Models\Account;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class RealisasiBudgetController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('realisasi_budgets.index', compact('projects', 'users'));
    }

    public function getData(Request $request)
    {
        $query = RealisasiBudget::with(['user', 'budget.project'])
            ->join('budgets', 'realisasi_budgets.budget_id', '=', 'budgets.id')
            ->join('projects', 'budgets.project_id', '=', 'projects.id')
            ->join('users', 'realisasi_budgets.user_id', '=', 'users.id')
            ->select('realisasi_budgets.*', 'projects.name as project_name', 'users.name as user_name', 'budgets.request_no');

        // Apply project filter
        if ($request->has('project_id') && $request->project_id != '') {
            $query->where('budgets.project_id', $request->project_id);
        }

        // Apply requestor filter
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('realisasi_budgets.user_id', $request->user_id);
        }

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('realisasi_budgets.status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('project', function ($realisasi) {
                return '<span class="small">' . ($realisasi->budget && $realisasi->budget->project ? $realisasi->budget->project->name : '-') . '</span>';
            })
            ->addColumn('realisasi_no', function ($realisasi) {
                return '<span class="small">' . $realisasi->realisasi_no . '</span>';
            })
            ->addColumn('realisasi_date', function ($realisasi) {
                return '<span class="small">' . ($realisasi->realisasi_date ? date('d M Y', strtotime($realisasi->realisasi_date)) : '-') . '</span>';
            })
            ->addColumn('budget_no', function ($realisasi) {
                if ($realisasi->budget) {
                    return '<a href="' . route('budgets.show', $realisasi->budget->id) . '" target="_blank" class="small text-decoration-none">' . $realisasi->budget->request_no . '</a>';
                }
                return '<span class="small">-</span>';
            })
            ->addColumn('requestor', function ($realisasi) {
                return '<span class="small">' . ($realisasi->user ? $realisasi->user->name : '-') . '</span>';
            })
            ->addColumn('amount', function ($realisasi) {
                return '<span class="small text-end d-block">' . number_format($realisasi->total_amount, 0, ',', '.') . '</span>';
            })
            ->addColumn('status', function ($realisasi) {
                $statusClass = [
                    'draft' => 'secondary',
                    'submitted' => 'info',
                    'approved' => 'success',
                    'completed' => 'dark',
                    'rejected' => 'danger',
                ];
                $class = $statusClass[$realisasi->status] ?? 'secondary';
                $statusText = ucfirst(str_replace('_', ' ', $realisasi->status));
                return '<span class="badge bg-' . $class . '">' . $statusText . '</span>';
            })
            ->addColumn('action', function ($realisasi) {
                $buttons = '
                    <a href="' . route('realisasi-budgets.show', $realisasi) . '" class="btn btn-sm btn-outline-primary">
                        View
                    </a>
                ';

                // Show edit button only for document owner
                if (auth()->id() === $realisasi->user_id) {
                    $buttons .= '
                        <a href="' . route('realisasi-budgets.edit', $realisasi) . '" class="btn btn-sm btn-outline-warning ms-1">
                            Ubah
                        </a>
                    ';
                }

                return $buttons;
            })
            ->rawColumns(['project', 'realisasi_no', 'realisasi_date', 'budget_no', 'requestor', 'amount', 'status', 'action'])
            ->make(true);
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
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total_price' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
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
                    $filePath = $file->storeAs('realisasi_budgets', $fileName, 'public');

                    RealisasiBudgetFile::create([
                        'realisasi_budget_id' => $realisasiBudget->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
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
        $realisasiBudget->load(['budget.project', 'user', 'items.account', 'items.budgetItem', 'files']);
        return view('realisasi_budgets.show', compact('realisasiBudget'));
    }

    public function edit(RealisasiBudget $realisasiBudget)
    {
        // Only allow owner to edit their own document
        if ($realisasiBudget->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own realization.');
        }

        $budget = $realisasiBudget->budget()->with(['items.account'])->first();
        $accounts = Account::active()->orderBy('account_number')->get();

        return view('realisasi_budgets.form', compact('realisasiBudget', 'budget', 'accounts'));
    }

    public function update(Request $request, RealisasiBudget $realisasiBudget)
    {
        // Only allow owner to update their own document
        if ($realisasiBudget->user_id !== auth()->id()) {
            abort(403, 'You can only update your own realization.');
        }

        $validated = $request->validate([
            'realisasi_date' => 'required|date',
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.budget_item_id' => 'nullable|exists:budget_items,id',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
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
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
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
