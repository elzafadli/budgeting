<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiBudgetItem extends Model
{
    protected $fillable = [
        'realisasi_budget_id',
        'budget_item_id',
        'account_id',
        'total_price',
        'remarks',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function realisasiBudget()
    {
        return $this->belongsTo(RealisasiBudget::class);
    }

    public function budgetItem()
    {
        return $this->belongsTo(BudgetItem::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
