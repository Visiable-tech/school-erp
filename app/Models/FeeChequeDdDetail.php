<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeChequeDdDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'fee_collection_id',

        'instrument_type',
        'instrument_no',
        'instrument_date',

        'bank_master_id',
        'bank_name',
        'branch_name',

        'amount',

        'school_account_id',
        'deposit_date',

        'clearance_status',
        'clearance_date',

        'cheque_bounce_reason_id',
        'bounce_date',
        'bounce_remarks',

        'remarks',

        'created_by',
        'updated_by',

        'payment_reversed_at',
        'payment_reversed_by',
        'bounce_charge_amount',
        'bounce_charge_applied',
    ];

    protected $casts = [
        'instrument_date' => 'date',
        'deposit_date'    => 'date',
        'clearance_date'  => 'date',
        'bounce_date'     => 'date',
        'payment_reversed_at' => 'datetime',

        'amount' => 'decimal:2',
        'bounce_charge_amount' => 'decimal:2',
        'bounce_charge_applied' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | School
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Fee Receipt / Collection
    |--------------------------------------------------------------------------
    */

    public function feeCollection()
    {
        return $this->belongsTo(
            FeeCollection::class,
            'fee_collection_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Bank
    |--------------------------------------------------------------------------
    */

    public function bank()
    {
        return $this->belongsTo(
            BankMaster::class,
            'bank_master_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | School Account
    |--------------------------------------------------------------------------
    */

    public function schoolAccount()
    {
        return $this->belongsTo(
            SchoolAccount::class,
            'school_account_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Bounce Reason
    |--------------------------------------------------------------------------
    */

    public function bounceReason()
    {
        return $this->belongsTo(
            ChequeBounceReason::class,
            'cheque_bounce_reason_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Created By
    |--------------------------------------------------------------------------
    */

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Updated By
    |--------------------------------------------------------------------------
    */

    public function updatedBy()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->clearance_status === 'pending';
    }


    public function isDeposited(): bool
    {
        return $this->clearance_status === 'deposited';
    }


    public function isCleared(): bool
    {
        return $this->clearance_status === 'cleared';
    }


    public function isBounced(): bool
    {
        return $this->clearance_status === 'bounced';
    }


    public function isCancelled(): bool
    {
        return $this->clearance_status === 'cancelled';
    }

    public function paymentReversedBy()
    {
        return $this->belongsTo(
            User::class,
            'payment_reversed_by'
        );
    }
}