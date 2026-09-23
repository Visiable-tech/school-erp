<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'frequency',
        'is_optional',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function structureItems()
    {
        return $this->hasMany(FeeStructureItem::class);
    }

    public function feeStructures()
    {
        return $this->belongsToMany(
            FeeStructure::class,
            'fee_structure_items'
        )
        ->withPivot([
            'amount',
            'sort_order',
            'status'
        ])
        ->withTimestamps();
    }
}