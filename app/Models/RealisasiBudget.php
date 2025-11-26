<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RealisasiBudget extends Model
{
    protected $fillable = [
        'realisasi_no',
        'budget_id',
        'user_id',
        'realisasi_date',
        'description',
        'total_amount',
        'approved_total',
        'status',
    ];

    protected $casts = [
        'realisasi_date' => 'date',
        'total_amount' => 'decimal:2',
        'approved_total' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RealisasiBudgetItem::class);
    }

    public function files()
    {
        return $this->hasMany(RealisasiBudgetFile::class);
    }

    public static function generateRealisasiNo()
    {
        $lastRealisasi = self::latest('id')->first();
        $number = $lastRealisasi ? (int)substr($lastRealisasi->realisasi_no, -5) + 1 : 1;
        return 'REAL-' . date('Ymd') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
