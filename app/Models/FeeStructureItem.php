<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructureItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_structure_id',
        'fee_head_id',
        'amount',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }

    // Fee installments / schedule
    public function installments()
    {
        return $this->hasMany(
            FeeInstallment::class,
            'fee_structure_item_id'
        )
        ->orderBy('sort_order')
        ->orderBy('due_date');
    }
}