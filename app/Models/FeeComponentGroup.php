<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeComponentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
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

    /*
     * We will activate this relationship
     * after adding fee_component_group_id
     * to fee_heads.
     */
    public function feeComponents()
    {
        return $this->hasMany(
            FeeHead::class,
            'fee_component_group_id'
        );
    }
}