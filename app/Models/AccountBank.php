<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountBank extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_holder_name',
        'account_number',
        'bank_name',
    ];

    /**
     * Get all budgets using this account bank
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'account_from_id');
    }

    /**
     * Get formatted account display
     */
    public function getFormattedAccountAttribute()
    {
        return "{$this->bank_name} - {$this->account_number} ({$this->account_holder_name})";
    }
}
