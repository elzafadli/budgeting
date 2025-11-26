<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->role === 'project_manager') {
            $pendingApprovals = Budget::where('status', 'submitted')
                ->with(['user', 'items', 'approvals'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'finance') {
            // Finance sees both pm_approved budgets and submitted budgets from cashiers
            $pendingApprovals = Budget::whereIn('status', ['pm_approved', 'submitted'])
                ->with(['user', 'items', 'approvals'])
                ->whereHas('approvals', function($query) {
                    $query->where('role', 'finance')->where('status', 'pending');
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $pendingApprovals = collect();
        }

        return view('approvals.index', compact('pendingApprovals'));
    }
}
