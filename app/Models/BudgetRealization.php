<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetRealization extends Model
{
    protected $fillable = [
        'budget_id',
        'realization_no',
        'realization_date',
        'realized_by',
        'total_realized',
        'status',
        'note',
    ];

    protected $casts = [
        'realization_date' => 'date',
        'total_realized' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function realizedByUser()
    {
        return $this->belongsTo(User::class, 'realized_by');
    }

    public function items()
    {
        return $this->hasMany(BudgetRealizationItem::class, 'realization_id');
    }
}
