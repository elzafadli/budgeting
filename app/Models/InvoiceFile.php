<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceFile extends Model
{
    protected $fillable = [
        'invoice_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
