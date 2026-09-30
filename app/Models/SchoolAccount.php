<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'bank_master_id',
        'account_name',
        'account_number',
        'account_type',
        'purpose',
        'upi_id',
        'merchant_id',
        'description',
        'is_default',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function bank()
    {
        return $this->belongsTo(
            BankMaster::class,
            'bank_master_id'
        );
    }
}