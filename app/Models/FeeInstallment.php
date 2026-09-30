<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'fee_structure_id',
        'fee_structure_item_id',
        'installment_name',
        'period_start',
        'period_end',
        'due_date',
        'amount',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(
            FeeStructure::class
        );
    }

    public function feeStructureItem()
    {
        return $this->belongsTo(
            FeeStructureItem::class,
            'fee_structure_item_id'
        );
    }

    public function studentDues()
    {
        return $this->hasMany(
            StudentFeeDue::class,
            'fee_installment_id'
        );
    }
}