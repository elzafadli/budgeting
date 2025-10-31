<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'request_no',
        'user_id',
        'project_id',
        'account_bank_id',
        'document_date',
        'description',
        'total_amount',
        'approved_total',
        'status',
    ];

    protected $casts = [
        'document_date' => 'date',
        'total_amount' => 'decimal:2',
        'approved_total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function accountBank()
    {
        return $this->belongsTo(AccountBank::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function approvals()
    {
        return $this->hasMany(BudgetApproval::class);
    }

    public function realizations()
    {
        return $this->hasMany(BudgetRealization::class);
    }

    public function realisasiBudgets()
    {
        return $this->hasMany(RealisasiBudget::class);
    }

    public function files()
    {
        return $this->hasMany(BudgetFile::class);
    }

    public function pmApproval()
    {
        return $this->hasOne(BudgetApproval::class)->where('role', 'project_manager');
    }

    public function financeApproval()
    {
        return $this->hasOne(BudgetApproval::class)->where('role', 'finance');
    }
}
