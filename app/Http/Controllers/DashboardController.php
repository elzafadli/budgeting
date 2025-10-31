<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetApproval;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [];
        
        if ($user->role === 'admin') {
            $stats = [
                'total' => Budget::where('user_id', $user->id)->count(),
                'draft' => Budget::where('user_id', $user->id)->where('status', 'draft')->count(),
                'submitted' => Budget::where('user_id', $user->id)->where('status', 'submitted')->count(),
                'approved' => Budget::where('user_id', $user->id)->whereIn('status', ['pm_approved', 'finance_approved', 'completed'])->count(),
                'rejected' => Budget::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];
            
            $recentBudgets = Budget::where('user_id', $user->id)
                ->with(['items', 'approvals'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } elseif ($user->role === 'project_manager') {
            $stats = [
                'pending' => BudgetApproval::where('role', 'project_manager')
                    ->where('status', 'pending')
                    ->count(),
                'approved' => BudgetApproval::where('role', 'project_manager')
                    ->where('approver_id', $user->id)
                    ->where('status', 'approved')
                    ->count(),
                'rejected' => BudgetApproval::where('role', 'project_manager')
                    ->where('approver_id', $user->id)
                    ->where('status', 'rejected')
                    ->count(),
            ];
            
            $recentBudgets = Budget::whereIn('status', ['submitted', 'pm_approved'])
                ->with(['user', 'items', 'approvals'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else { // finance
            $stats = [
                'pending' => BudgetApproval::where('role', 'finance')
                    ->where('status', 'pending')
                    ->count(),
                'approved' => BudgetApproval::where('role', 'finance')
                    ->where('approver_id', $user->id)
                    ->where('status', 'approved')
                    ->count(),
                'rejected' => BudgetApproval::where('role', 'finance')
                    ->where('approver_id', $user->id)
                    ->where('status', 'rejected')
                    ->count(),
            ];
            
            $recentBudgets = Budget::whereIn('status', ['pm_approved', 'finance_approved', 'completed'])
                ->with(['user', 'items', 'approvals', 'realizations'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }
        
        return view('dashboard', compact('stats', 'recentBudgets'));
    }
}
