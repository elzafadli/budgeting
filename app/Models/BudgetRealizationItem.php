<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetRealizationItem extends Model
{
    protected $fillable = [
        'realization_id',
        'description',
        'amount',
        'proof_file',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function realization()
    {
        return $this->belongsTo(BudgetRealization::class, 'realization_id');
    }
}
