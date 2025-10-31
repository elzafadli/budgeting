<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetApproval extends Model
{
    protected $fillable = [
        'budget_id',
        'approver_id',
        'role',
        'level',
        'status',
        'note',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
