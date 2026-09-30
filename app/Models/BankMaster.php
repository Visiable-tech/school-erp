<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'bank_name',
        'short_name',
        'bank_code',
        'branch_name',
        'branch_code',
        'ifsc_code',
        'micr_code',
        'address',
        'city',
        'contact_person',
        'phone',
        'email',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolAccounts()
    {
        return $this->hasMany(
            SchoolAccount::class,
            'bank_master_id'
        );
    }
}