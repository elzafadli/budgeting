<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'account_description',
        'active_indicator',
        'account_number_parent',
        'account_level',
        'account_type',
    ];

    protected $casts = [
        'active_indicator' => 'boolean',
        'account_level' => 'integer',
    ];

    /**
     * Get the parent account
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'account_number_parent', 'account_number');
    }

    /**
     * Get child accounts
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'account_number_parent', 'account_number');
    }

    /**
     * Get budget items using this account
     */
    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    /**
     * Scope for active accounts only
     */
    public function scopeActive($query)
    {
        return $query->where('active_indicator', true);
    }

    /**
     * Scope for specific account type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    /**
     * Get formatted account display
     */
    public function getFormattedNameAttribute()
    {
        return $this->account_number . ' - ' . $this->account_description;
    }
}
