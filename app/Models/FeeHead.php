<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'fee_component_group_id',
        'fee_cycle_id',

        'name',
        'code',

        // Legacy field - retained during migration
        'frequency',

        'is_optional',
        'is_refundable',
        'allow_concession',
        'allow_waiver',

        'description',

        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'is_refundable' => 'boolean',
        'allow_concession' => 'boolean',
        'allow_waiver' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function componentGroup()
    {
        return $this->belongsTo(
            FeeComponentGroup::class,
            'fee_component_group_id'
        );
    }

    public function feeCycle()
    {
        return $this->belongsTo(
            FeeCycle::class,
            'fee_cycle_id'
        );
    }

    public function structureItems()
    {
        return $this->hasMany(
            FeeStructureItem::class
        );
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