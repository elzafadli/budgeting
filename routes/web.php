<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\RealizationController;
use App\Http\Controllers\RealisasiBudgetController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountBankController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfitLossController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Budget routes
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create')->middleware('role:admin');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store')->middleware('role:admin');
    Route::get('/budgets/{budget}', [BudgetController::class, 'show'])->name('budgets.show');
    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])->name('budgets.edit')->middleware('role:admin');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update')->middleware('role:admin');
    Route::get('/budgets/{budget}/print', [BudgetController::class, 'print'])->name('budgets.print');
    Route::post('/budgets/{budget}/submit', [BudgetController::class, 'submit'])->name('budgets.submit')->middleware('role:admin');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy')->middleware('role:admin');

    // Project routes
    Route::resource('projects', ProjectController::class);

    // Account routes
    Route::resource('accounts', AccountController::class);

    // Account Bank routes
    Route::resource('account-banks', AccountBankController::class);

    // Approval routes
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index')->middleware('role:project_manager,finance');
    Route::post('/approvals/{budget}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve')->middleware('role:project_manager,finance');
    Route::post('/approvals/{budget}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject')->middleware('role:project_manager,finance');

    // Realization routes
    Route::get('/realizations/create/{budget}', [RealizationController::class, 'create'])->name('realizations.create')->middleware('role:finance');
    Route::post('/realizations/{budget}', [RealizationController::class, 'store'])->name('realizations.store')->middleware('role:finance');
    Route::get('/realizations/{realization}', [RealizationController::class, 'show'])->name('realizations.show');

    // Realisasi Budget routes
    Route::get('/realisasi-budgets', [RealisasiBudgetController::class, 'index'])->name('realisasi-budgets.index');
    Route::get('/realisasi-budgets/create', [RealisasiBudgetController::class, 'create'])->name('realisasi-budgets.create');
    Route::post('/realisasi-budgets', [RealisasiBudgetController::class, 'store'])->name('realisasi-budgets.store');
    Route::get('/realisasi-budgets/{realisasiBudget}', [RealisasiBudgetController::class, 'show'])->name('realisasi-budgets.show');
    Route::get('/realisasi-budgets/{realisasiBudget}/edit', [RealisasiBudgetController::class, 'edit'])->name('realisasi-budgets.edit');
    Route::put('/realisasi-budgets/{realisasiBudget}', [RealisasiBudgetController::class, 'update'])->name('realisasi-budgets.update');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profit & Loss report
    Route::get('/reports/profit-loss', [ProfitLossController::class, 'index'])->name('reports.profit_loss');
});

require __DIR__ . '/auth.php';
