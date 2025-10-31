<?php

namespace App\Http\Controllers;

use App\Models\AccountBank;
use Illuminate\Http\Request;

class AccountBankController extends Controller
{
    /**
     * Display a listing of account banks.
     */
    public function index()
    {
        $accountBanks = AccountBank::latest()->paginate(15);
        return view('account_banks.index', compact('accountBanks'));
    }

    /**
     * Show the form for creating a new account bank.
     */
    public function create()
    {
        return view('account_banks.form');
    }

    /**
     * Store a newly created account bank.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        AccountBank::create($validated);

        return redirect()->route('account-banks.index')
            ->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    /**
     * Display the specified account bank.
     */
    public function show(AccountBank $accountBank)
    {
        return view('account_banks.show', compact('accountBank'));
    }

    /**
     * Show the form for editing the specified account bank.
     */
    public function edit(AccountBank $accountBank)
    {
        return view('account_banks.form', compact('accountBank'));
    }

    /**
     * Update the specified account bank.
     */
    public function update(Request $request, AccountBank $accountBank)
    {
        $validated = $request->validate([
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
        ]);

        $accountBank->update($validated);

        return redirect()->route('account-banks.index')
            ->with('success', 'Rekening bank berhasil diperbarui.');
    }

    /**
     * Remove the specified account bank.
     */
    public function destroy(AccountBank $accountBank)
    {
        // Check if account bank is being used in budgets
        if ($accountBank->budgets()->count() > 0) {
            return redirect()->route('account-banks.index')
                ->with('error', 'Tidak dapat menghapus rekening yang sedang digunakan dalam anggaran.');
        }

        $accountBank->delete();

        return redirect()->route('account-banks.index')
            ->with('success', 'Rekening bank berhasil dihapus.');
    }
}
