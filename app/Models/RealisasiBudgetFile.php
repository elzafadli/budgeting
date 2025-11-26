<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiBudgetFile extends Model
{
    protected $fillable = [
        'realisasi_budget_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function realisasiBudget()
    {
        return $this->belongsTo(RealisasiBudget::class);
    }
}
