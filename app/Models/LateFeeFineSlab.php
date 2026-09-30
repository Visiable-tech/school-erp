<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateFeeFineSlab extends Model
{
    use HasFactory;

    protected $fillable = [
        'late_fee_fine_rule_id',
        'period_type',
        'from_day',
        'to_day',
        'amount',
        'per_month',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'from_day'   => 'integer',
        'to_day'     => 'integer',
        'amount'     => 'decimal:2',
        'per_month'  => 'boolean',
        'sort_order' => 'integer',
        'status'     => 'boolean',
    ];

    public function rule()
    {
        return $this->belongsTo(
            LateFeeFineRule::class,
            'late_fee_fine_rule_id'
        );
    }
}