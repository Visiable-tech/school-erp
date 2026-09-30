<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'installments_count',
        'cycle_type',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'installments_count' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function feeComponents()
    {
        return $this->hasMany(
            FeeHead::class,
            'fee_cycle_id'
        );
    }
}