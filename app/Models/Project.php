<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vendor',
        'no_project',
        'start_date',
        'end_date',
        'amount',
        'description',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get all budgets for this project
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get total budget amount for this project
     */
    public function getTotalBudgetAttribute()
    {
        return $this->budgets()->sum('total_amount');
    }

    /**
     * Check if project is active
     */
    public function isActive()
    {
        return $this->status === 'in_progress';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'in_progress' => 'primary',
            'completed' => 'success',
            'canceled' => 'danger',
            default => 'secondary',
        };
    }
}
