<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'project_manager') {
            $pendingApprovals = Budget::where('status', 'submitted')
                ->with(['user', 'items', 'approvals'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'finance') {
            $pendingApprovals = Budget::where('status', 'pm_approved')
                ->with(['user', 'items', 'approvals'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $pendingApprovals = collect();
        }

        return view('approvals.index', compact('pendingApprovals'));
    }

    public function approve(Request $request, Budget $budget)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            if ($user->role === 'project_manager' && $budget->status === 'submitted') {
                $approval = BudgetApproval::where('budget_id', $budget->id)
                    ->where('role', 'project_manager')
                    ->first();

                if (!$approval) {
                    return back()->with('error', 'Approval record not found.');
                }

                $approval->update([
                    'approver_id' => $user->id,
                    'status' => 'approved',
                    'note' => $validated['note'] ?? null,
                    'approved_at' => now(),
                ]);

                $budget->update([
                    'status' => 'pm_approved',
                    'approved_total' => $budget->total_amount,
                ]);

                DB::commit();
                return redirect()->route('approvals.index')->with('success', 'Budget approved successfully.');

            } elseif ($user->role === 'finance' && $budget->status === 'pm_approved') {
                $approval = BudgetApproval::where('budget_id', $budget->id)
                    ->where('role', 'finance')
                    ->first();

                if (!$approval) {
                    return back()->with('error', 'Approval record not found.');
                }

                $approval->update([
                    'approver_id' => $user->id,
                    'status' => 'approved',
                    'note' => $validated['note'] ?? null,
                    'approved_at' => now(),
                ]);

                $budget->update(['status' => 'finance_approved']);

                DB::commit();
                return redirect()->route('approvals.index')->with('success', 'Budget approved successfully.');
            } else {
                DB::rollback();
                return back()->with('error', 'You cannot approve this budget at this time.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve budget.');
        }
    }

    public function reject(Request $request, Budget $budget)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            if ($user->role === 'project_manager' && $budget->status === 'submitted') {
                $approval = BudgetApproval::where('budget_id', $budget->id)
                    ->where('role', 'project_manager')
                    ->first();

                if (!$approval) {
                    return back()->with('error', 'Approval record not found.');
                }

                $approval->update([
                    'approver_id' => $user->id,
                    'status' => 'rejected',
                    'note' => $validated['note'],
                    'approved_at' => now(),
                ]);

                $budget->update(['status' => 'rejected']);

                DB::commit();
                return redirect()->route('approvals.index')->with('success', 'Budget rejected.');

            } elseif ($user->role === 'finance' && $budget->status === 'pm_approved') {
                $approval = BudgetApproval::where('budget_id', $budget->id)
                    ->where('role', 'finance')
                    ->first();

                if (!$approval) {
                    return back()->with('error', 'Approval record not found.');
                }

                $approval->update([
                    'approver_id' => $user->id,
                    'status' => 'rejected',
                    'note' => $validated['note'],
                    'approved_at' => now(),
                ]);

                $budget->update(['status' => 'rejected']);

                DB::commit();
                return redirect()->route('approvals.index')->with('success', 'Budget rejected.');
            } else {
                DB::rollback();
                return back()->with('error', 'You cannot reject this budget at this time.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to reject budget.');
        }
    }
}
