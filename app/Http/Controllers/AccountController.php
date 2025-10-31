<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of accounts.
     */
    public function index()
    {
        $accounts = Account::orderBy('account_number')->paginate(20);
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        $parentAccounts = Account::orderBy('account_number')->get();
        return view('accounts.form', compact('parentAccounts'));
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_description' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        // Generate auto account_number based on count
        $count = Account::count() + 1;
        $validated['account_number'] = str_pad($count, 4, '0', STR_PAD_LEFT);

        // Set defaults for other fields
        $validated['account_level'] = 1;
        $validated['account_number_parent'] = null;
        $validated['active_indicator'] = true;

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified account.
     */
    public function show(Account $account)
    {
        $account->load(['parent', 'children', 'budgetItems']);
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        $parentAccounts = Account::where('account_number', '!=', $account->account_number)
            ->orderBy('account_number')
            ->get();
        return view('accounts.form', compact('account', 'parentAccounts'));
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'account_description' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        // Keep existing values for fields not in form
        $validated['account_number'] = $account->account_number;
        $validated['account_level'] = $account->account_level;
        $validated['account_number_parent'] = $account->account_number_parent;
        $validated['active_indicator'] = $account->active_indicator;

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified account.
     */
    public function destroy(Account $account)
    {
        // Check if account has budget items
        if ($account->budgetItems()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Cannot delete account with existing budget items.');
        }

        // Check if account has children
        if ($account->children()->count() > 0) {
            return redirect()->route('accounts.index')
                ->with('error', 'Cannot delete account with child accounts.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
