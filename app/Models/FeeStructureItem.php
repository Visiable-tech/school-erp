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
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function structure()
    {
        return $this->belongsTo(
            FeeStructure::class,
            'fee_structure_id'
        );
    }

    public function feeHead()
    {
        return $this->belongsTo(
            FeeHead::class
        );
    }

    public function installments()
    {
        return $this->hasMany(
            FeeInstallment::class
        );
    }
}