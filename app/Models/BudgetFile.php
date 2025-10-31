<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BudgetFile extends Model
{
    protected $fillable = [
        'budget_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if (Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
            }
        });
    }
}
